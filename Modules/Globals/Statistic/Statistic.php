<?php

namespace Modules\Globals\Statistic;

use Modules\MainModulesClass;


class Statistic extends MainModulesClass
{

    public function __construct()
    {

        $this->mDir = dirname(__FILE__);

        include_once $this->mDir."/func.php";
        $this->func = new \Statistic\func( $this );

    }

    public function info()
    {
        return array(
            "author" => "mmoweb",
            "game" => "Global",
            "version" => "1.0",
            "description" => array(
                'ru' => 'Модуль статистики',
                'en' => 'Statistic module',
            ),
            "url" => "https://mmoweb.biz/",
            "created" => "28.02.2020",
            "lastUpdated" => "28.02.2020",
            "class" => __CLASS__,

        );
    }

    public function onRequest(){
        return $this->func->widget_rating();
    }

}