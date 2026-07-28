<?php

namespace ApiLib\v2\MasterAccount;

use Api;

class Recovery extends Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function changePasswordWithEmailCode($vars)
    {
        $response = $this->init()->addParam('payload', $vars)->post('v2/master-account/recovery/email/change-password')->response();
        return $response;
    }

}