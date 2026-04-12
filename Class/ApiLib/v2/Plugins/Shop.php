<?php

namespace ApiLib\v2\Plugins;

use Api;

class Shop extends Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function buyItem($vars)
    {
        return $this->init()
        ->addParam('item', $vars)
        ->post('v2/plugins/shop/buy-item')
        ->response();
    }

    public function buyService($vars)
    {
        return $this->init()
        ->addParam('service', $vars)
        ->post('v2/plugins/shop/buy-service')
        ->response();
    }

}