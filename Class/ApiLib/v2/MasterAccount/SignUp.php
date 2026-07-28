<?php

namespace ApiLib\v2\MasterAccount;

use Api;

class SignUp extends Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function signUpWithEmail($vars)
    {
        $response = $this->init()->addParam('payload', $vars)->post('v2/master-account/sign-up/email')->response();
        return $response;
    }

    public function signUpWithPhone($vars)
    {
        $response = $this->init()->addParam('payload', $vars)->post('v2/master-account/sign-up/phone')->response();
        return $response;
    }

    public function activationWithEmailCode($vars)
    {
        $response = $response = $this->init()->addParam('payload', $vars)->post('v2/master-account/activation/email')->response();
        return $response;
    }

    public function sendSmsCode($vars)
    {
        $response = $this->init()->addParam('payload', $vars)->post('v2/master-account/activation/send-sms')->response();
        return $response;
    }

}