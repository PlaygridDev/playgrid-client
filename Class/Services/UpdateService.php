<?php

namespace Services;

use ApiLib\GlobalApi;
use Exception;
use GitHub\Repository;
use Cache;

class UpdateService
{

    private array $versionConfig;
    private Repository $gitHubRepository;
    private string $processKey;
    private string $processDir;
    private GlobalApi $globalApi;

    private int $processStartTime;

    const PROCESS_EXECUTE_TIMEOUT = 1800;

    public function __construct(Repository $gitHubRepository)
    {

        $this->versionConfig = getConfig('version');
        if(empty($this->versionConfig)) {
            throw new Exception('Empty version config');
        }

        $this->gitHubRepository = $gitHubRepository;
        $this->processKey = '';
        $this->processDir = '';

        $this->processStartTime = 0;

        $this->globalApi = new GlobalApi();

    }

    public function setProcessKey(string $key)
    {
        $this->processKey = $key;
    }

    public function getProcessKey()
    {
        return $this->processKey;
    }

    public function getProcessDir()
    {
        return $this->processDir;
    }

    public function setProcessDir(string $dir)
    {
        $this->processDir = $dir;
    }

    public function updateIsActive()
    {
        return $this->getUpdateStatus() === 'IN_PROGRESS';
    }

    public function getUpdateStatus()
    {
        $updateCache = Cache::get('update');
        if(empty($updateCache)) {
            return null;
        }

        return $updateCache['status'] ?? null;
    }

    public function getCacheProcessKey()
    {
        $updateCache = Cache::get('update');
        if(empty($updateCache)) {
            return null;
        }

        return $updateCache['process_key'] ?? null;
    }

    public function generateProcessKey()
    {
        return hash('sha256', random_bytes(32) . microtime(true));
    }

    public function checkProcessKey(string $key)
    {
        return $key === $this->getCacheProcessKey();
    }

    public function setUpdateStatus(string $status)
    {

        Cache::set('update', [
            'status' => $status,
            'process_key' => $this->processKey
        ], 1800);

        if($status !== 'CREATED') {
            $this->globalApi->sendUpdaterProcessStatus($this->processKey, $status);
        }

    }

    private function createProcessDir()
    {

        if(empty($this->processKey)) {
            throw new Exception('Process key is not set.');
        }

        $processDir = ROOT_DIR . DIRECTORY_SEPARATOR . 'Files' . DIRECTORY_SEPARATOR . 'update' . DIRECTORY_SEPARATOR . $this->processKey;

        if (!is_dir($processDir)) {
            if (!mkdir($processDir, 0775, true) && !is_dir($processDir)) {
                throw new Exception('Failed to create update process directory: ' . $processDir);
            }
        }

        $this->setProcessDir($processDir);

        $this->log('Process directory created: ' . $processDir);

        return true;

    }

    private function saveReleaseZip(array $release, string $processDir)
    {
        $tag = $release['tag_name'] ?? null;
        $zipUrl = $release['zipball_url'] ?? null;

        $this->log("Downloading release {$tag} ZIP file...");

        if(empty($tag)) {
            throw new Exception('Release does not contain a tag name.');
        }

        if(empty($zipUrl)) {
            throw new Exception('Release does not contain a zipball URL.');
        }

        $zipPath = $processDir . DIRECTORY_SEPARATOR . $tag . '.zip';

        if (file_exists($zipPath)) {
            if (!unlink($zipPath)) {
                throw new Exception('Failed to delete existing ZIP file: ' . $zipPath);
            }
        }

        $fp = fopen($zipPath, 'w+');
        if (!$fp) {
            throw new Exception('Failed to open file for writing: ' . $zipPath);
        }

        $ch = curl_init($zipUrl);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, UpdateService::PROCESS_EXECUTE_TIMEOUT + 300);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: MmoWeb Client Updater',
        ]);

        if (!curl_exec($ch)) {
            $error = curl_error($ch);
            fclose($fp);
            curl_close($ch);
            throw new Exception('Failed to download release ZIP file from: ' . $zipUrl . '. Error: ' . $error);
        }

        curl_close($ch);
        fclose($fp);

        $this->log('Release ZIP file downloaded and saved to: ' . $zipPath);

        return $zipPath;

    }

    private function registerShutdownFunction()
    {

        error_clear_last();

        $processKey = $this->getProcessKey();

        register_shutdown_function(function () use ($processKey) {

            $error = error_get_last();
            if ($error === null) {
                return;
            }

            $fatalErrors = [
                E_ERROR,
                E_PARSE,
                E_CORE_ERROR,
                E_COMPILE_ERROR,
                E_USER_ERROR,
            ];

            if (DEBUG && !in_array($error['type'], $fatalErrors, true)) {

                log_write(
                    'update_' . date('Y-m-d_H-i') . '_' . substr($processKey, 0, 8),
                    'NON-FATAL: ' . json_encode($error),
                    false
                );

                return;
            }

            Cache::set('update', [
                'status' => 'FAILED',
                'process_key' => $processKey
            ], 1800);

            log_write(
                'update_' . date('Y-m-d_H-i') . '_' . substr($processKey, 0, 8),
                'FATAL ERROR: ' . json_encode($error),
                false
            );

            $this->globalApi->sendUpdaterProcessError($processKey, json_encode($error));
            $this->globalApi->sendUpdaterProcessStatus($processKey, 'FAILED');

        });

    }

    private function createBackup()
    {

        $this->log('Creating backup...');

        $processDir = $this->getProcessDir();

        if (empty($processDir) || !is_dir($processDir)) {
            throw new Exception('Process directory does not exist: ' . $processDir);
        }

        $ignoredExtensions = [
            'zip', 'rar', '7z',
            'tar', 'gz', 'bz2',
            'xz', 'tgz',
        ];

        $backupPath = $processDir . DIRECTORY_SEPARATOR . 'backup.zip';

        $zip = new \ZipArchive();
        if ($zip->open($backupPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new Exception('Failed to create backup ZIP: ' . $backupPath);
        }

        $root = rtrim(realpath(ROOT_DIR), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if ($root === false) {
            throw new Exception('Failed to resolve ROOT_DIR.');
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {

            /** @var \SplFileInfo $file */
            $filePath = $file->getPathname();

            if (strpos($filePath, DIRECTORY_SEPARATOR . 'Files' . DIRECTORY_SEPARATOR . 'update' . DIRECTORY_SEPARATOR) !== false) {
                continue;
            }

            if ($file->isLink() || !$file->isReadable()) {
                continue;
            }

            if ($file->isFile()) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, $ignoredExtensions, true)) {
                    continue;
                }
            }

            $relativePath = substr($filePath, strlen($root));
            $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

            if ($file->isDir()) {
                if (!$zip->addEmptyDir($relativePath)) {
                    throw new Exception('Failed to add directory to backup: ' . $relativePath);
                }
            } else {
                if (!$zip->addFile($filePath, $relativePath)) {
                    throw new Exception('Failed to add file to backup: ' . $relativePath);
                }
            }

        }

        $zip->close();

        if (!file_exists($backupPath) || filesize($backupPath) === 0) {
            throw new Exception('Backup ZIP was not created or is empty.');
        }

        $this->log('Backup created: ' . $backupPath);

        return true;

    }

    private function logDebugInfo()
    {
        if (DEBUG) {

            $this->log('=== System information ===');

            $this->log('[PHP]');
            $this->log('Version: ' . PHP_VERSION);
            $this->log('SAPI: ' . PHP_SAPI);

            $this->log('[Environment]');
            $this->log('OS: ' . PHP_OS);
            $this->log('Architecture: ' . (PHP_INT_SIZE * 8) . '-bit');

            $this->log('[Limits]');
            $this->log('Memory limit: ' . ini_get('memory_limit'));
            $this->log('Max execution time: ' . ini_get('max_execution_time'));
            $this->log('Upload max filesize: ' . ini_get('upload_max_filesize'));
            $this->log('Post max size: ' . ini_get('post_max_size'));

            $this->log('[Extensions]');
            foreach (get_loaded_extensions() as $ext) {
                $version = phpversion($ext) ?: 'unknown';
                $this->log("- {$ext}: {$version}");
            }

            $this->log('==========================');
        }

    }

    private function getAllCommitFiles(string $commitHash)
    {

        $files = [];
        $page  = 1;

        do {

            $commit = $this->gitHubRepository->getCommit($commitHash, $page);

            if (empty($commit) || empty($commit['data'])) {
                throw new Exception('Git commit for the release tag not found.');
            }

            if (!empty($commit['data']['files'])) {
                foreach ($commit['data']['files'] as $file) {

                    if (empty($file['filename']) || empty($file['status'])) {
                        throw new Exception("GitHub API returned invalid file entry: missing filename or status.");
                    }

                    $files[$file['filename']] = [
                        'filename'          => $file['filename'],
                        'status'            => $file['status'],
                        'previous_filename' => $file['previous_filename'] ?? null,
                    ];
                }
            }

            $hasNextPage = false;

            if (!empty($commit['links'])) {
                $hasNextPage = strpos($commit['links'], 'rel="next"') !== false;
            }

            $page++;

            if ($page > 50) {
                throw new Exception('Too many commit pages while fetching commit files.');
            }

        } while ($hasNextPage);

        return array_values($files);

    }

    public function installRelease(string $tag)
    {

        $this->processStartTime = time();

        $this->registerShutdownFunction();

        ignore_user_abort(true);
        set_time_limit(UpdateService::PROCESS_EXECUTE_TIMEOUT);
        ini_set('memory_limit', '256M');
        umask(0022);

        $this->log("Starting installation of release: {$tag}");

        $this->logDebugInfo();

        $zip = null;

        try {

            if($this->updateIsActive()) {
                throw new Exception('An update is already in progress.');
            }

            if($tag === $this->getVersion()) {
                throw new Exception('The specified tag is already installed.');
            }

            $release = $this->gitHubRepository->getReleaseByTag($tag);

            if(empty($release)) {
                throw new Exception('Release with the specified tag not found.');
            }

            if(empty($release['tag_name']) || $release['tag_name'] !== $tag) {
                throw new Exception('Release tag does not match the specified tag.');
            }

            $ref = $this->gitHubRepository->getGitRefByTag($tag);
            if(empty($ref) || empty($ref['object']['sha'])) {
                throw new Exception('Git reference for the specified tag not found.');
            }

            $commitHash = $ref['object']['sha'];
            $this->log("Release commit SHA: {$commitHash}");

            $this->setUpdateStatus('IN_PROGRESS');

            $zipPath = null;
            if($this->createProcessDir()) {
                $this->createBackup();
                $zipPath = $this->saveReleaseZip($release, $this->getProcessDir());
            } else {
                throw new Exception('Failed to create update process directory.');
            }

            $zip = new \ZipArchive();
            if (!is_null($zipPath) && $zip->open($zipPath) !== true) {
                throw new Exception('Failed to open ZIP file: ' . $zipPath . '. The file may be corrupted.');
            }

            $releaseFolder = null;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $fileName = $zip->getNameIndex($i);
                $parts = explode('/', $fileName);
                if (!empty($parts[0])) {
                    $releaseFolder = $parts[0];
                    break;
                }
            }

            if($releaseFolder === null) {
                throw new Exception('Failed to determine release folder inside ZIP.');
            }

            $updateFiles = $this->getAllCommitFiles($commitHash);
            $total = count($updateFiles);

            if($total === 0) {
                throw new Exception('No files to update found in the release.');
            }

            $this->log("Found {$total} files to update, starting installation...");

            $n = 0;
            $sendedProgressTime = 0;
            $progress = 0;

            foreach ($updateFiles as $file) {

                $filename = $file['filename'] ?? null;
                $status   = $file['status'] ?? null;

                if (!$filename || !$status) {
                    throw new Exception("GitHub Compare API returned invalid file entry: missing filename or status.");
                }

                $targetPath  = ROOT_DIR . DIRECTORY_SEPARATOR . $filename;
                $archivePath = $releaseFolder . '/' . $filename;

                if (
                    strpos($filename, '..') !== false ||
                    preg_match('#^(\/|\\\\|[a-zA-Z]:)#', $filename)
                ) {
                    throw new Exception('ZIP Slip detected: invalid path: ' . $filename);
                }

                $this->log("Processing {$filename} ({$status})");

                switch ($status) {

                    case 'added':
                    case 'modified':
                        if ($zip->locateName($archivePath) === false) {
                            throw new Exception("File not found in ZIP: {$archivePath}");
                        }
                        $this->extractFileFromZip($zip, $archivePath, $targetPath);
                        $this->log("Applied {$status}: {$filename}");
                        break;

                    case 'renamed':
                        if ($zip->locateName($archivePath) === false) {
                            throw new Exception("Renamed file not found in ZIP: {$archivePath}");
                        }
                        $this->extractFileFromZip($zip, $archivePath, $targetPath);
                        $this->log("Renamed treated as copy: {$file['previous_filename']} → {$filename}");
                        break;

                    case 'removed':
                        $this->log("Removed in release (not deleted locally): {$filename}");
                        break;

                    default:
                        throw new Exception("Unknown compare status '{$status}' for {$filename}");
                        break;
                }

                $n++;

                $progress = round((($n / $total) * 100), 2);
                Cache::set('update_progress_' . $this->processKey, $progress, 600);
                $this->log('Update progress: ' . $progress . '%');

                if($n < $total) {
                    if ($sendedProgressTime === 0 || (time() - $sendedProgressTime) >= 3) {
                        $this->globalApi->sendUpdaterProcessProgress($this->processKey, $progress);
                        $sendedProgressTime = time();
                    }
                } elseif($n === $total) {
                    $this->globalApi->sendUpdaterProcessProgress($this->processKey, 100);
                } else {
                    throw new Exception('Update progress calculation error.');
                }

            }

            $this->setUpdateStatus('COMPLETED');

            SaveConfig([
                'tag' => $tag,
                'timestamp' => $release['published_at'],
                'release_id' => $release['id']
            ], 'version');

            if(!DEBUG) {
                Cache::delete('update_progress_' . $this->processKey);
                Cache::delete('update');
            }

            $this->log('Update completed successfully. Version updated to ' . $tag);

            return true;

        } catch (Exception $e) {

            $this->setUpdateStatus('FAILED');
            $this->log('Error: ' . $e->getMessage());
            $this->log('-- UPDATE FAILED --');

            $this->globalApi->sendUpdaterProcessError($this->processKey, $e->getMessage());

            throw $e;

        } finally {

            if ($zip instanceof \ZipArchive) {
                $zip->close();
            }

            if(DEBUG) {
                $this->log('Update process finished in ' . (time() - $this->processStartTime) . ' seconds.');
            }

        }

    }

    public function getVersion()
    {
        return $this->versionConfig['tag'] ?? 'unknown';
    }

    public function checkPHPVersion()
    {
        return version_compare(PHP_VERSION, '7.4.33', '>=');
    }

    public function checkGitHubConnection()
    {
        return $this->gitHubRepository->getRepository();
    }

    private function extractFileFromZip(\ZipArchive $zip, string $archivePath, string $targetPath)
    {

        if(defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            $this->log("Simulating file extraction (development mode)");
            return true;
        }

        $stream = $zip->getStream($archivePath);
        if (!$stream) {
            throw new Exception("Cannot open $archivePath");
        }

        $targetDir = dirname($targetPath);
        if(!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                throw new Exception('Failed to create directory for file: ' . $targetPath);
            }
        }

        $tmp = $targetPath . '.tmp';
        $out = fopen($tmp, 'wb');

        stream_copy_to_stream($stream, $out);

        fclose($stream);
        fclose($out);

        chmod($tmp, 0644);

        if (!rename($tmp, $targetPath)) {
            unlink($tmp);
            throw new Exception("Failed to replace file: {$targetPath}");
        }

        return true;

    }

    private function log(string $message)
    {
        $processKey = $this->processKey;
        if(!empty($processKey)) {
            log_write('update_' . date('Y-m-d_H-i', $this->processStartTime) . '_' . substr($processKey, 0, 8), $message, false);
        }
    }

}