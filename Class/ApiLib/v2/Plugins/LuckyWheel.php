<?php

namespace ApiLib\v2\Plugins;

use Api;

class LuckyWheel extends Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function spin($vars)
    {
        $response = $this->init()->addParam('lucky_wheel', $vars)->post('v2/plugins/lucky-wheel/spin')->response();
        return $response;
    }

    public function getHistory($vars)
    {
        $response = $this->init()->addParam('lucky_wheel', $vars)->get('v2/plugins/lucky-wheel/history')->response();
        return $response;
    }

}