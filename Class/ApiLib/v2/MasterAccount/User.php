<?php

namespace ApiLib\v2\MasterAccount;

use Api;

class User extends Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function emailVerificationSendCode($vars)
    {
        return $this->init()->addParam('payload', $vars)->post('v2/master-account/user/email-verification/send-code')->response();
    }

    public function emailVerificationConfirm($vars)
    {
        return $this->init()->addParam('payload', $vars)->post('v2/master-account/user/email-verification/confirm')->response();
    }

}