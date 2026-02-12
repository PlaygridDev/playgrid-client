<?php

class Cache
{

    public static function set(string $key, $data, int $ttl = 600): bool
    {

        $safeKey = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $key);

        $filePath = ROOT_DIR . CACHEPATH . '/' . $safeKey . '.txt';

        $contents = [
            'time' => time(),
            'ttl'  => $ttl,
            'data' => $data,
        ];

        $serialized = serialize($contents);

        if (($fp = @fopen($filePath, 'wb')) === false) {
            return false;
        }

        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            return false;
        }

        $length  = strlen($serialized);
        $written = 0;
        $result  = 0;

        while ($written < $length) {
            $result = fwrite($fp, substr($serialized, $written));
            if ($result === false) {
                break;
            }
            $written += $result;
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        if (is_int($result)) {
            @chmod($filePath, 0640);
            return true;
        }

        return false;
    }

    public static function get(
        string $key,
        bool $get_val = true,
        bool $get_data = false,
        bool $del_time_end = true
    ) {

        $safeKey = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $key);

        $filePath = ROOT_DIR . CACHEPATH . '/' . $safeKey . '.txt';

        if (!is_file($filePath)) {
            return false;
        }

        $data = @unserialize(@file_get_contents($filePath));

        if (!is_array($data)) {
            return false;
        }

        if (
            $data['ttl'] > 0 &&
            time() > ($data['time'] + $data['ttl'])
        ) {
            if ($del_time_end) {
                @unlink($filePath);
            }

            if ($get_data) {
                $data['cache_end'] = true;
                return $data;
            }

            return false;
        }

        if ($get_val) {
            return is_array($data) ? $data['data'] : false;
        }

        return $data;

    }

    public static function delete(string $key)
    {
        $safeKey = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $key);

        $filePath = ROOT_DIR . CACHEPATH . '/' . $safeKey . '.txt';

        if (!is_file($filePath)) {
            return false;
        }

        return @unlink($filePath);
    }

}