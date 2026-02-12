<?php

namespace ApiLib;


class LaApi extends \Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function character_items($vars)
    {
        return $this->init()->addParam('items', $vars)->get('v2/LA/character/items')->response();
    }

    public function market_get($vars)
    {
        return $this->init()->addParam('market', $vars)->post('v2/LA/market/get')->response();
    }

    public function market_sell($vars)
    {
        return $this->init()->addParam('market', $vars)->post('v2/LA/market/sell')->response();
    }

    public function market_character($vars)
    {
        return $this->init()->addParam('market', $vars)->post('v2/LA/market/sell-character')->response();
    }

    public function market_sell_delete($vars)
    {
        return $this->init()->addParam('market', $vars)->delete('v2/LA/market/sell')->response();
    }

    public function market_buy($vars)
    {
        return $this->init()->addParam('market', $vars)->post('v2/LA/market/buy')->response();
    }
    public function market_transfer($vars)
    {
        return $this->init()->addParam('market', $vars)->post('v2/LA/market/transfer')->response();
    }

    public function market_withdraw($vars)
    {
        return $this->init()->addParam('market', $vars)->post('v2/LA/market/withdraw')->response();
    }

    public function refresh_info($vars)
    {
        return $this->init()->addParam('market', $vars)->get('v2/LA/market/info')->response();
    }

    public function history($vars)
    {
        return $this->init()->addParam('market', $vars)->get('v2/LA/market/history')->response();
    }

    public function transfer_log($vars)
    {
        return $this->init()->addParam('market', $vars)->get('v2/LA/market/transfer')->response();
    }

    public function stat_log($vars)
    {
        return $this->init()->addParam('market', $vars)->get('v2/LA/market/stat')->response();
    }

    public function teleport_char($vars)
    {
        return $this->init()->addParam('teleport', $vars)->post('v2/LA/character/teleport')->response();
    }

    public function reset_hwid_char($vars)
    {
        return $this->init()->addParam('reset_hwid', $vars)->post('v2/LA/character/reset-hwid')->response();
    }

    public function reset_pin_char($vars)
    {
        return $this->init()->addParam('reset_pin', $vars)->post('v2/LA/character/reset-pin')->response();
    }

    public function reset_pin_account($vars)
    {
        return $this->init()->addParam('reset_pin', $vars)->post('v2/LA/account/reset-pin')->response();
    }

    public function reset_hwid_account($vars)
    {
        return $this->init()->addParam('reset_hwid', $vars)->post('v2/LA/account/reset-hwid')->response();
    }

}