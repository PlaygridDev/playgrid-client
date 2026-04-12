<?php

class CSRF
{
    const SESSION_KEY = 'csrf_token';

    /**
     * Генерирует новый токен, сохраняет в сессии и возвращает его.
     */
    public static function generate()
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::SESSION_KEY] = $token;
        return $token;
    }

    /**
     * Возвращает текущий токен из сессии или генерирует новый.
     */
    public static function getToken()
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            return self::generate();
        }
        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Сравнивает переданный токен с токеном сессии
     */
    public static function validate(string $token)
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            return false;
        }
        return hash_equals($_SESSION[self::SESSION_KEY], $token);
    }
}
