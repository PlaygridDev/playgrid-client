<?php

namespace ApiLib\v2\Plugins;

use Api;

class GiftCode extends Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function getHistory()
    {
        return $this->init()->get('v2/plugins/gift-code/history')->response();
    }

}
