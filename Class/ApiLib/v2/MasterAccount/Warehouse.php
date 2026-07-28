<?php

namespace ApiLib\v2\MasterAccount;

use Api;

class Warehouse extends Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function deliveryItem($vars)
    {
        $response = $this->init()->addParam('payload', $vars)->post('v2/master-account/warehouse/item/delivery')->response();
        return $response;
    }

}
