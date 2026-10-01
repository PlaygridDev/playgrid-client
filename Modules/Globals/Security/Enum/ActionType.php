<?php

namespace Modules\Globals\Security\Enum;


class ActionType
{
    const SIGNIN = 'signin';
    const TWO_FA_DISABLE = '2fa_disable';
    const GAME_ACCOUNT_TRANSFER = 'game_account_transfer';

    public static function getAll()
    {
        return [
            self::SIGNIN,
            self::TWO_FA_DISABLE,
            self::GAME_ACCOUNT_TRANSFER
        ];
    }

    public static function isValid(string $action): bool
    {
        return in_array($action, self::getAll());
    }

}



