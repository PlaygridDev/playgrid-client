<?php

namespace GitHub;

use Exception;
use InvalidArgumentException;
use RuntimeException;

class Repository
{

    private array $config;

    private string $apiBaseUri;
    private string $repositoryPath;
    private int $repositoryTimeout;
    private string $token;

    private string $errorMessage;

    public function __construct(array $config)
    {

        if(!$this->checkConfig($config)) {
            throw new InvalidArgumentException('One or more required configs is empty!');
        }

        $this->config = $config;

        $this->apiBaseUri           = $this->config['api_base_uri'] ?? '';
        $this->repositoryPath       = $this->config['repository_path'] ?? '';
        $this->repositoryTimeout    = $this->config['timeout'] ?? 15;
        $this->token                = $this->config['token'] ?? '';

        $this->errorMessage = '';

    }

    private function checkConfig(array $config)
    {
        return !empty($config['api_base_uri']) && !empty($config['repository_path']);
    }

    public function getRepository()
    {

        $response = $this->sendRequest('/');

        return $response['data'] ?? false;

    }

    private function sendRequest(string $uri, array $params = [], string $method = 'GET')
    {

        try {

            if(empty($uri)) {
                throw new InvalidArgumentException('empty $uri');
            }

            $url = $this->apiBaseUri . '/repos/' . $this->repositoryPath . '/' . ltrim($uri, '/');
            $url = rtrim($url, '/');

            $headers['User-Agent'] = 'MmoWeb Client Updater';
            $headers['Content-Type'] = 'application/json';
            $headers['Accept'] = 'application/vnd.github.v3+json';
            $headers['X-GitHub-Api-Version'] = '2022-11-28';

            if (!empty($this->token)) {
                $headers['Authorization'] = 'Bearer ' . $this->token;
            }

            $curl = new \Curl\Curl($url);
            $curl->setTimeout($this->repositoryTimeout);
            $curl->setHeaders($headers);

            if($method === 'POST') {
                $curl->post($url, $params);
            } else {
                $curl->get($url, $params);
            }

            $responseCode    = $curl->getHttpStatusCode();
            $responseBody    = $curl->getRawResponse();
            $responseHeaders = $curl->getResponseHeaders();

            if($curl->getCurlErrorCode() !== 0) {
                throw new RuntimeException('cURL error: ' . $curl->getErrorMessage());
            }

            if($responseCode < 200 || $responseCode >= 300) {
                throw new RuntimeException('GitHub API returned error code ' . $responseCode . ': ' . $responseBody);
            }

            return [
                'code'      => $responseCode,
                'body'      => $responseBody,
                'headers'   => $responseHeaders,
                'data'      => json_decode($responseBody, true),
            ];

        } catch (Exception $e) {

            log_write('github', $e->getMessage());
            error_log($e->getMessage());
            error_log($e->getTraceAsString());

            $this->errorMessage = $e->getMessage();

            return false;

        }

    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getGitRefByTag(string $tag)
    {
        $response = $this->sendRequest('git/ref/tags/' . $tag);
        return $response['data'] ?? null;
    }

    public function getCommit(string $commitHash, int $page = 1)
    {

        usleep(250000);

        $url = 'commits/' . $commitHash;
        if ($page > 1) {
            $url .= '?page=' . $page;
        }

        $response = $this->sendRequest($url);

        return [
            'links' => $response['headers']['Link'] ?? null,
            'data'  => $response['data'] ?? null,
        ];
    }

    public function getReleaseByTag(string $tag)
    {
        $response = $this->sendRequest('releases/tags/' . $tag);
        return $response['data'] ?? null;
    }

}