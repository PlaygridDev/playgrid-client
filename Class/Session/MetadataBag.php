<?php

namespace Session;

class MetadataBag implements SessionBagInterface
{
    /**
     * Имя раздела в $_SESSION.
     */
    private const BAG_NAME = 'metadata';

    /**
     * Получить весь bag.
     */
    private static function all(): array
    {
        return Session::get(self::BAG_NAME, []);
    }

    /**
     * Сохранить весь bag.
     */
    private static function save(array $data): void
    {
        if (empty($data)) {
            Session::remove(self::BAG_NAME);
            return;
        }

        Session::set(self::BAG_NAME, $data);
    }

    public static function get(string $key, $default = null)
    {
        return self::all()[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        $data = self::all();

        $data[$key] = $value;

        self::save($data);
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, self::all());
    }

    public static function remove(string $key): void
    {
        $data = self::all();

        if (!array_key_exists($key, $data)) {
            return;
        }

        unset($data[$key]);

        self::save($data);
    }

    public static function pull(string $key, $default = null)
    {
        $value = self::get($key, $default);

        self::remove($key);

        return $value;
    }

    public static function clear(): void
    {
        Session::remove(self::BAG_NAME);
    }
}