<?php

namespace ApiLib\v2\Plugins;

use Api;

class BonusCode extends Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function activate($vars)
    {
        $response = $this->init()->addParam('bonus_cod', $vars)->post('v2/plugins/bonus-code/activate')->response();
        return $response;
    }

}