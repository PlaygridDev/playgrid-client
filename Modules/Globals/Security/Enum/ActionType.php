<?php

namespace Modules\Globals\Security\Enum;


class ActionType
{
    const SIGNIN = 'signin';
    const TWO_FA_DISABLE = '2fa_disable';

    public static function getAll()
    {
        return [
            self::SIGNIN,
            self::TWO_FA_DISABLE
        ];
    }

    public static function isValid(string $action): bool
    {
        return in_array($action, self::getAll());
    }

}



