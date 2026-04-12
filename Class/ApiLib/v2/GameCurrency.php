<?php

namespace ApiLib\v2;

use Api;

class GameCurrency extends Api
{

    public function __construct($url = false, $key = false)
    {
        parent::__construct($url, $key);
    }

    public function buy($vars)
    {
        return $this->init()
        ->addParam('game_currency', $vars)
        ->post('v2/game-currency/buy')
        ->response();
    }

}
