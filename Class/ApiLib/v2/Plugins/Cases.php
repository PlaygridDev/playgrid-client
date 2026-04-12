<?php

namespace ApiLib\v2\Plugins;

use Api;

class Cases extends Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function open($vars)
    {
        $response = $this->init()->addParam('cases', $vars)->post('v2/plugins/cases/open')->response();
        return $response;
    }

}