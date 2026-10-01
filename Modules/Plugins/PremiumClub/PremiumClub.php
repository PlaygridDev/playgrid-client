<?php

namespace Modules\Plugins\PremiumClub;

use Modules\MainModulesClass;

class PremiumClub extends MainModulesClass
{

    public function __construct()
    {

        $this->mDir = dirname(__FILE__);

        include_once $this->mDir."/func.php";
        $this->func = new \PremiumClub\func($this);

    }

    public function info()
    {
        return array(
            "author" => "mmoweb",
            "game" => "Plugins",
            "version" => "1.0",
            "description" => array(
                'ru' => 'Premium Club',
                'en' => 'Premium Club',
            ),
            "url" => "https://mmoweb.biz/",
            "created" => "26.09.2026",
            "lastUpdated" => "26.09.2026",
            "class" => __CLASS__,

        );
    }

    public function renderWindow()
    {
        return array(
            '/panel' => array(
                'grid' => array(
                    array(
                        'class' => 'grid-item col-12 col-md-6 col-xl-4',
                        'level' => 3.5,
                        'widget_status' => function() { return $this->func->widget_status();},
                    ),
                ),
            ),
        );
    }

}
