<?php

namespace BonusBalance;

class func
{

    public $this_main;

    public function __construct($this_main)
    {
        $this->this_main = $this_main;
    }

    public static function getSettings()
    {

        $bonusBalanceConfig = get_instance()->config['bonus_balance'];
        $serverId = get_instance()->session->getSid();

        $settings = [
            'balance_name' => '',
            'display_mode' => '',
        ];

        if(get_instance()->check_plugin('bonus_balance')) {

            if(!empty($bonusBalanceConfig)) {

                $settings = [

                    'balance_name'  => $bonusBalanceConfig['balance_mode'] === 'global'
                                    ? $bonusBalanceConfig['global']['balance_name']
                                    : $bonusBalanceConfig['servers'][$serverId]['balance_name'],

                    'display_mode'  => $bonusBalanceConfig['balance_mode'] === 'global'
                                    ? $bonusBalanceConfig['global']['display_mode']
                                    : $bonusBalanceConfig['servers'][$serverId]['display_mode'],

                    'config'        => $bonusBalanceConfig,

                ];

            }

        }

        return $settings;

    }


}