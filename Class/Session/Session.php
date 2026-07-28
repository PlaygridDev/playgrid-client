<?php

namespace Session;

class Session
{
    /**
     * Получить значение.
     *
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Записать значение.
     *
     * @param mixed $value
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Проверить существование ключа.
     */
    public static function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    /**
     * Удалить ключ.
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Очистить всю сессию.
     */
    public static function clear(): void
    {
        $_SESSION = [];
    }
}