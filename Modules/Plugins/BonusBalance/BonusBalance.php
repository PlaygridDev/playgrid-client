<?php

namespace Modules\Plugins\BonusBalance;

use Modules\MainModulesClass;

class BonusBalance extends MainModulesClass
{

    public function __construct()
    {

        $this->mDir = dirname(__FILE__);

        include_once $this->mDir."/func.php";
        $this->func = new \BonusBalance\func($this);

    }

    public function info()
    {
        return array(
            "author" => "mmoweb",
            "game" => "Plugins",
            "version" => "1.0",
            "description" => array(
                'ru' => 'Бонусный баланс',
                'en' => 'Bonus balance',
            ),
            "url" => "https://mmoweb.biz/",
            "created" => "30.04.2020",
            "lastUpdated" => "30.04.2020",
            "class" => __CLASS__,

        );
    }

    public function onAjax()
    {

        return array(

        );

    }


}