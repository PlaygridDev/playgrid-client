<?php

namespace ApiLib\v2\MasterAccount;

use Api;

class GameAccount extends Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function changePassword($vars)
    {
        return $this->init()->addParam('payload', $vars)->post('v2/master-account/game-account/change-password')->response();
    }

}