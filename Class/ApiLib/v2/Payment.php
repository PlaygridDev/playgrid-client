<?php

namespace ApiLib\v2;

use Api;

class Payment extends Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function createOrder($vars)
    {

        $response = $this->init()
            ->addParam('order', $vars)
            ->post('v2/payment/order/create')
            ->response();

        return $response;

    }

    /**
     * not implemented, soon
     */
    public function completeOrder($vars)
    {

        $response = $this->init()
            ->addParam('order', $vars)
            ->post('v2/payment/order/complete')
            ->response();

        return $response;

    }

    /**
     * not implemented, soon
     */
    public function cancelOrder($vars)
    {

        $response = $this->init()
            ->addParam('order', $vars)
            ->post('v2/payment/order/cancel')
            ->response();

        return $response;

    }

    public function getMethods($vars)
    {

        $response = $this->init()
            ->addParam('methods', $vars)
            ->get('v2/payment/methods')
            ->response();

        return $response;

    }


}
