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

        self::log($this->processKey, 'Process directory created: ' . $processDir, $this->processStartTime);

        return true;

    }

    private function saveReleaseZip(array $release, string $processDir)
    {
        $tag = $release['tag_name'] ?? null;
        $zipUrl = $release['zipball_url'] ?? null;

        self::log($this->processKey, "Downloading release {$tag} ZIP file...", $this->processStartTime);

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

        self::log($this->processKey, 'Release ZIP file downloaded and saved to: ' . $zipPath, $this->processStartTime);

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

            if (!in_array($error['type'], $fatalErrors, true)) {

                if (DEBUG) {
                    \Services\UpdateService::log($processKey, 'NON-FATAL: ' . json_encode($error));
                }

                return;
            }

            Cache::set('update', [
                'status' => 'FAILED',
                'process_key' => $processKey
            ], 1800);

            \Services\UpdateService::log($processKey, 'FATAL ERROR: ' . json_encode($error));

            $this->globalApi->sendUpdaterProcessError($processKey, json_encode($error));
            $this->globalApi->sendUpdaterProcessStatus($processKey, 'FAILED');

        });

    }

    private function createBackup()
    {

        self::log($this->processKey, 'Creating backup...', $this->processStartTime);

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

        $projectDir = realpath(ROOT_DIR);
        if ($projectDir === false) {
            throw new Exception('Failed to resolve ROOT_DIR: ' . ROOT_DIR);
        }
        $projectDir = rtrim($projectDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($projectDir, \FilesystemIterator::SKIP_DOTS),
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

            $relativePath = substr($filePath, strlen($projectDir));
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

        self::log($this->processKey, 'Backup created: ' . $backupPath, $this->processStartTime);

        return true;

    }

    private function logDebugInfo()
    {
        if (DEBUG) {

            self::log($this->processKey, '=== System information ===', $this->processStartTime);

            self::log($this->processKey, '[PHP]', $this->processStartTime);
            self::log($this->processKey, 'Version: ' . PHP_VERSION, $this->processStartTime);
            self::log($this->processKey, 'SAPI: ' . PHP_SAPI, $this->processStartTime);

            self::log($this->processKey, '[Environment]', $this->processStartTime);
            self::log($this->processKey, 'OS: ' . PHP_OS, $this->processStartTime);
            self::log($this->processKey, 'Architecture: ' . (PHP_INT_SIZE * 8) . '-bit', $this->processStartTime);

            self::log($this->processKey, '[Limits]', $this->processStartTime);
            self::log($this->processKey, 'Memory limit: ' . ini_get('memory_limit'), $this->processStartTime);
            self::log($this->processKey, 'Max execution time: ' . ini_get('max_execution_time'), $this->processStartTime);
            self::log($this->processKey, 'Upload max filesize: ' . ini_get('upload_max_filesize'), $this->processStartTime);
            self::log($this->processKey, 'Post max size: ' . ini_get('post_max_size'), $this->processStartTime);

            self::log($this->processKey, '[Extensions]', $this->processStartTime);
            foreach (get_loaded_extensions() as $ext) {
                $version = phpversion($ext) ?: 'unknown';
                self::log($this->processKey, "- {$ext}: {$version}", $this->processStartTime);
            }

            self::log($this->processKey, '==========================', $this->processStartTime);
        }

    }

    private function getTreeShaByTag(string $tag): string
    {

        $ref = $this->gitHubRepository->getGitRefByTag($tag);
        if (empty($ref) || empty($ref['object']['sha'])) {
            throw new Exception('Git reference for tag not found: ' . $tag);
        }

        $commit = $this->gitHubRepository->getCommit($ref['object']['sha']);

        if ($commit === null) {
            throw new Exception('GitHub API request failed for commit: ' . $this->gitHubRepository->getErrorMessage());
        }

        $treeSha = $commit['data']['commit']['tree']['sha'] ?? null;

        if (empty($treeSha)) {
            throw new Exception('Failed to resolve tree SHA for tag: ' . $tag);
        }

        return $treeSha;

    }

    private function getAllTreeDiffFiles(string $baseTag, string $headTag): array
    {

        $oldTreeSha = $this->getTreeShaByTag($baseTag);
        $newTreeSha = $this->getTreeShaByTag($headTag);

        self::log($this->processKey, "Tree SHA base ({$baseTag}): {$oldTreeSha}", $this->processStartTime);
        self::log($this->processKey, "Tree SHA head ({$headTag}): {$newTreeSha}", $this->processStartTime);

        $oldTree = $this->gitHubRepository->getGitTree($oldTreeSha);
        $newTree = $this->gitHubRepository->getGitTree($newTreeSha);

        if ($oldTree['truncated'] || $newTree['truncated']) {
            throw new Exception('Git tree response is truncated. The repository is too large for tree-based diff.');
        }

        $oldFiles = [];
        foreach ($oldTree['tree'] as $item) {
            if ($item['type'] === 'blob') {
                $oldFiles[$item['path']] = $item['sha'];
            }
        }

        $newFiles = [];
        foreach ($newTree['tree'] as $item) {
            if ($item['type'] === 'blob') {
                $newFiles[$item['path']] = $item['sha'];
            }
        }

        $result = [];

        foreach ($newFiles as $path => $sha) {
            if (!isset($oldFiles[$path])) {
                $result[] = ['filename' => $path, 'status' => 'added', 'previous_filename' => null];
            } elseif ($oldFiles[$path] !== $sha) {
                $result[] = ['filename' => $path, 'status' => 'modified', 'previous_filename' => null];
            }
        }

        foreach ($oldFiles as $path => $sha) {
            if (!isset($newFiles[$path])) {
                $result[] = ['filename' => $path, 'status' => 'removed', 'previous_filename' => null];
            }
        }

        return $result;

    }

    public function installRelease(string $tag)
    {

        $this->processStartTime = time();

        $this->registerShutdownFunction();

        ignore_user_abort(true);
        set_time_limit(UpdateService::PROCESS_EXECUTE_TIMEOUT);
        ini_set('memory_limit', '256M');
        umask(0022);

        self::log($this->processKey, "Starting installation of release: {$tag}", $this->processStartTime);

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

            $currentVersion = $this->getVersion();
            self::log($this->processKey, "Comparing trees: {$currentVersion} → {$tag}", $this->processStartTime);

            $updateFiles = $this->getAllTreeDiffFiles($currentVersion, $tag);
            $total = count($updateFiles);

            if($total === 0) {
                throw new Exception('No files to update found in the release.');
            }

            $ping = $this->globalApi->sendUpdaterProcessProgress($this->processKey, 0.0);
            if (empty($ping['http_code']) || $ping['http_code'] !== 200) {
                throw new Exception('API connection check failed (http_code: ' . ($ping['http_code'] ?? 'none') . '). Update aborted.');
            }

            $this->setUpdateStatus('IN_PROGRESS');

            $this->createProcessDir();
            $this->createBackup();
            $zipPath = $this->saveReleaseZip($release, $this->getProcessDir());

            $zip = new \ZipArchive();
            if ($zip->open($zipPath) !== true) {
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

            self::log($this->processKey, "Found {$total} files to update, starting installation...", $this->processStartTime);

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

                self::log($this->processKey, "Processing {$filename} ({$status})", $this->processStartTime);

                switch ($status) {

                    case 'added':
                    case 'modified':
                        if ($zip->locateName($archivePath) === false) {
                            throw new Exception("File not found in ZIP: {$archivePath}");
                        }
                        $this->extractFileFromZip($zip, $archivePath, $targetPath);
                        self::log($this->processKey, "Applied {$status}: {$filename}", $this->processStartTime);
                        break;

                    case 'removed':
                        self::log($this->processKey, "Removed in release (not deleted locally): {$filename}", $this->processStartTime);
                        break;

                    default:
                        throw new Exception("Unknown compare status '{$status}' for {$filename}");
                        break;
                }

                $n++;

                $progress = round((($n / $total) * 100), 2);
                Cache::set('update_progress_' . $this->processKey, $progress, UpdateService::PROCESS_EXECUTE_TIMEOUT);
                self::log($this->processKey, 'Update progress: ' . $progress . '%', $this->processStartTime);

                if($n < $total) {
                    if ($sendedProgressTime === 0 || (time() - $sendedProgressTime) >= 3) {
                        $this->globalApi->sendUpdaterProcessProgress($this->processKey, $progress);
                        $sendedProgressTime = time();
                    }
                } else {
                    $this->globalApi->sendUpdaterProcessProgress($this->processKey, 100);
                }

            }

            if (!SaveConfig([
                'tag' => $tag,
                'timestamp' => $release['published_at'],
                'release_id' => $release['id']
            ], 'version')) {
                throw new Exception('Failed to save version config after update. Files were updated but version record was not saved.');
            }

            $this->setUpdateStatus('COMPLETED');

            if(!DEBUG) {
                Cache::delete('update_progress_' . $this->processKey);
                Cache::delete('update');
            }

            self::log($this->processKey, 'Update completed successfully. Version updated to ' . $tag, $this->processStartTime);

            return true;

        } finally {

            if ($zip instanceof \ZipArchive) {
                $zip->close();
            }

            if(DEBUG) {
                self::log($this->processKey, 'Update process finished in ' . (time() - $this->processStartTime) . ' seconds.', $this->processStartTime);
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
            self::log($this->processKey, "Simulating file extraction (development mode)", $this->processStartTime);
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
        if (!$out) {
            throw new Exception("Cannot create temp file for: {$targetPath}");
        }

        $bytesCopied = stream_copy_to_stream($stream, $out);

        fclose($stream);
        fclose($out);

        if ($bytesCopied === false) {
            unlink($tmp);
            throw new Exception("Failed to write file content: {$targetPath}");
        }

        chmod($tmp, 0644);

        if (!rename($tmp, $targetPath)) {
            unlink($tmp);
            throw new Exception("Failed to replace file: {$targetPath}");
        }

        return true;

    }

    public static function log(string $processKey, string $message, int $processStartTime = 0): void
    {
        static $startTime = 0;

        if (!empty($processKey)) {
            if ($processStartTime > 0) {
                $startTime = $processStartTime;
            }
            $time = $startTime > 0 ? $startTime : time();
            log_write('update_' . date('Y-m-d_H-i', $time) . '_' . substr($processKey, 0, 8), $message, false);
        }
    }

}