<?php

namespace ApiLib\v2\MasterAccount;

use Api;

class SignIn extends Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function signInWithEmail($vars)
    {
        $response = $this->init()->addParam('payload', $vars)->post('v2/master-account/sign-in/email')->response();
        return $response;
    }

    public function signInWithPhone($vars)
    {
        $response = $this->init()->addParam('payload', $vars)->post('v2/master-account/sign-in/phone')->response();
        return $response;
    }

    public function signInWithGameAccount($vars)
    {
        $response = $this->init()->addParam('payload', $vars)->post('v2/master-account/sign-in/game-account')->response();
        return $response;
    }

}