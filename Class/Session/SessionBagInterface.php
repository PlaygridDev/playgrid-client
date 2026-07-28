<?php

namespace Session;

interface SessionBagInterface
{
    /**
     * Получить значение.
     *
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null);

    /**
     * Записать значение.
     *
     * @param mixed $value
     */
    public static function set(string $key, $value): void;

    /**
     * Проверить существование ключа.
     */
    public static function has(string $key): bool;

    /**
     * Удалить значение.
     */
    public static function remove(string $key): void;

    /**
     * Получить значение и удалить его.
     *
     * @param mixed $default
     * @return mixed
     */
    public static function pull(string $key, $default = null);

    /**
     * Очистить содержимое Bag.
     */
    public static function clear(): void;
}