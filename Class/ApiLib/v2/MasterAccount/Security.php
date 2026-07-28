<?php

namespace ApiLib\v2\MasterAccount;

use Api;

class Security extends Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function changePassword($vars)
    {
        return $this->init()->addParam('payload', $vars)->post('v2/master-account/security/change-password')->response();
    }

    public function startTwoFactorMethodActivation($vars)
    {
        return $this->init()->addParam('payload', $vars)->post('v2/master-account/security/2fa/methods')->response();
    }

    public function confirmTwoFactorMethodActivation($vars)
    {
        return $this->init()->addParam('payload', $vars)->post('v2/master-account/security/2fa/methods/confirm')->response();
    }

    public function startTwoFactorVerification($vars)
    {
        return $this->init()->addParam('payload', $vars)->post('v2/master-account/security/2fa/verification')->response();
    }

    public function twoFactorAuthDeactivation($vars)
    {
        return $this->init()->addParam('payload', $vars)->post('v2/master-account/security/2fa/deactivation')->response();
    }


}