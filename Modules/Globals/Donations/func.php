<?php

namespace Donations;

use Modules\Globals\Donations\Integrations\PaymentHandler;

use ApiLib\GlobalApi;
use ApiLib\v2\Payment;

class func
{

    public $this_main = false;
    public $advertising = false;
    public $market = false;
    public $sid = false;

    public $payment_list = array(
        'unitpay',
        'payu',
        'paypal',
        'payop',
        'paygol',
        'enot',
        'interkassa',
        'primepayments',
        'unitpay_two',
        'hotskins',
        'interkassa_two',
        'paypalych',
        'paypalych_two',
        'moneytigo',
        'stripe',
        'pagseguro',
        'binance',
        'portmone',
        'capitalist',
        'pgs',
        'monobank',
        'b2pay',
        'antilopay',
        'cryptocloud',
        'paddle',
        'paymntspro',
        'hydracode',
        'severpay',
        'severpay_byn',
        'settlepay_pix',
        'settlepay_cbucvu',
        'abankcomua',
        'betatransfer',
        'wayforpay',
        'liqpay'
    );

    public function __construct($this_main)
    {
        /**@var $this_main \Modules\Globals\Donations\Donations*/
        $this->this_main = $this_main;

        if ($this->advertising === false) {
            $this->advertising = getConfig('advertising');
        }

        if (isset(get_instance()->config['payment_system']['sorting_pay'])) {
            $this->payment_list = get_instance()->config['payment_system']['sorting_pay'];
        }

        if (
            defined('PAYMENT_INTEGRATIONS')
            && is_array(PAYMENT_INTEGRATIONS)
            && !empty(PAYMENT_INTEGRATIONS)
        ) {
            foreach (PAYMENT_INTEGRATIONS as $integration => $handlerClass) {

                if (!class_exists($handlerClass)) {
                    continue;
                }

                $integrationInstance = new $handlerClass();

                if (!$integrationInstance instanceof PaymentHandler) {
                    continue;
                }

                if(!method_exists($integrationInstance, 'getStatus') || !$integrationInstance->getStatus()) {
                    continue;
                }

                if (method_exists($integrationInstance, 'getIdentifier')) {
                    $identifier = $integrationInstance->getIdentifier();
                }

                $paymentMethod = 'cstm:' . $identifier;

                if (in_array($paymentMethod, $this->payment_list, true)) {
                    continue;
                }

                $sortOrder = 100;

                if (method_exists($integrationInstance, 'getSortOrder')) {
                    $sortOrder = (int) $integrationInstance->getSortOrder() - 1;
                }

                array_splice(
                    $this->payment_list,
                    $sortOrder,
                    0,
                    [$paymentMethod]
                );

            }
        }

        $this->market = get_instance()->market;
        $this->sid = get_instance()->sid;

        if (isset($this->market[$this->sid])) {
            $this->market = $this->market[$this->sid];

            if (isset($this->market['balance']))
                $this->market = $this->market['balance'] == false;
            else
                $this->market = false;
        }else
            $this->market = false;


    }
    public function widget_donations_no_auth(){

        get_instance()->seo->addTeg('head', 'rangeslider_css', 'link', array('rel' => 'stylesheet', 'href' => VIEWPATH.'/panel/assets/js/plugins/ion-rangeslider/css/ion.rangeSlider.css'));
        get_instance()->seo->addTeg('footer', 'rangeslider', 'script', array('src' => VIEWPATH.'/panel/assets/js/plugins/ion-rangeslider/js/ion.rangeSlider.min.js'));


        //Вычисление бонуса к выбронуму серверу
        $event_cfg = get_instance()->config['event'];
        $sid = get_instance()->get_sid();
        if (isset($event_cfg[$sid])){
            $event_list = array();
            foreach ($event_cfg[$sid] as $event){

                if (isset($event['type']) AND $event['type'] == true) {
                    continue;
                }
                $now = new \DateTime();
                $start = new \DateTime($event['start']);
                $end = new \DateTime($event['end']);

                if ($start < $now && $now < $end) {
                    $item_temp = array();
                    if ($event['item_enable']) {
                        foreach ($event['item'] as $item) {

                            $temp_item = set_item($item['id'], false, true);
                            $item['id'] = $temp_item['id'];
                            $item['name'] = $temp_item['name'];
                            $item['add_name'] = $temp_item['add_name'];
                            $item['icon'] = $temp_item['icon'];

                            $item_temp[$item['lv']][] = $item;
                        }
                    }
                    $event['item'] = $item_temp;
                    $event_list[] = $event;
                }

            }
        }else
            $event_list = false;



        return get_instance()->fenom->fetch(
            get_tpl_file('widget_donations_no_auth.tpl', get_class($this->this_main)),

            array_merge(
                array(
                    'payment_system' => get_instance()->config['payment_system'],
                    'config_cabinet' => get_instance()->config['cabinet'],
                    'event_cfg' => $event_list,
                    'payment_list' => $this->payment_list,
                    get_lang('course.lang')

                ),
                get_lang('widget_donate.lang'),
                get_lang('payment.lang')
            )

        );


    }

    public function widget_donations(){

        get_instance()->seo->addTeg('head', 'rangeslider_css', 'link', array('rel' => 'stylesheet', 'href' => VIEWPATH.'/panel/assets/js/plugins/ion-rangeslider/css/ion.rangeSlider.css'));
        get_instance()->seo->addTeg('footer', 'rangeslider', 'script', array('src' => VIEWPATH.'/panel/assets/js/plugins/ion-rangeslider/js/ion.rangeSlider.min.js'));

        if (isset(get_instance()->session->session["master_account"]['mid']))
            $mid = get_instance()->session->session["master_account"]['mid'];
        else
            $mid = 0;

        //Вычисление бонуса к выбронуму серверу
        $event_cfg = get_instance()->config['event'];
        $sid = get_instance()->get_sid();
        if (isset($event_cfg[$sid])){
            $event_list = array();
            foreach ($event_cfg[$sid] as $event){


                $now = new \DateTime();
                $start = new \DateTime($event['start']);
                $end = new \DateTime($event['end']);

                if ($start < $now && $now < $end) {

                    $item_temp = array();
                    if ($event['item_enable']) {
                        foreach ($event['item'] as $item) {

                            $temp_item = set_item($item['id'], false, true);
                            $item['id'] = $temp_item['id'];
                            $item['name'] = $temp_item['name'];
                            $item['add_name'] = $temp_item['add_name'];
                            $item['icon'] = $temp_item['icon'];

                            $item_temp[$item['lv']][] = $item;
                        }
                    }
                    $event['item'] = $item_temp;

                    if (isset($event['type']) AND $event['type']){
                        if (!in_array($mid, $event['individual'])){
                            continue;
                        }

                        if (isset($event['link'])){
                            foreach ($event['link'] as $ids){
                                unset($event_list[$ids]);
                            }
                        }
                    }

                    $event_list[$event['id']] = $event;
                }
            }
            foreach ($event_list as $event){
                if (isset($event['type']) AND isset($event['link'])){
                    foreach ($event['link'] as $ids) {
                        unset($event_list[$ids]);
                    }
                }
            }

            $event_list = array_values($event_list);

        }else
            $event_list = false;


        return get_instance()->fenom->fetch(
            get_tpl_file('widget_donations.tpl', get_class($this->this_main)),

            array_merge(
                array(
                    'payment_system' => get_instance()->config['payment_system'],
                    'market' => $this->market,
                    'event_cfg' => $event_list,
                    'payment_list' => $this->payment_list,
                    'config_cabinet' => get_instance()->config['cabinet'],
                    get_lang('course.lang')

                ),
                get_lang('widget_donate.lang'),
                get_lang('payment.lang')
            )

        );


    }

    public function getPaymentMethods()
    {

        $post = $_POST;

        if(empty($post['payment_system'])) {
            return get_instance()->ajaxmsg->notify(get_lang('widget_donate.lang')['donate_ajax_empty_payment_method'])->danger();
        }

        $payment = new Payment();
        $response = $payment->getMethods([
            'payment_system' => $post['payment_system']
        ]);

        if ($response['ok']) {
            if (isset($response['error'])) {
                $error = get_lang('payment.lang')[$response['error']] ?? $response['error'];
                $send = get_instance()->ajaxmsg->notify($error)->danger();
            } else {
                if(isset($response["response"]->methods) && !empty($response["response"]->methods)) {
                    $send = get_instance()
                            ->ajaxmsg
                            ->broadcast(
                                $post['payment_system'] . '_payment_methods',
                                json_encode($response["response"]->methods),
                                'updatePaymentMethods'
                            )
                            ->send();
                } else {
                    $send = get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_login_error'])->danger();
                }
            }
        } else {
            $send = get_instance()->ajaxmsg->notify('Error: ' . $response['http_error'] . '<br>Code: ' . $response['http_code'])->danger();
        }

        return $send;

    }

    public function ajax_checkout()
    {

        if (!captcha_check()) {
            return get_instance()->ajaxmsg->notify(get_lang('signup.lang')['signup_ajax_error_captcha'])->eval_js(captcha_reload('checkout'))->danger();
        }

        if (get_instance()->session->isLogin()) {

            $vars = [];

            if (!isset($_REQUEST['sum']) OR empty($_REQUEST['sum'])) {
                return get_instance()->ajaxmsg->notify(get_lang('widget_donate.lang')['donate_ajax_empty_sum'])->danger();
            } else {
                $vars["sum"] = $_REQUEST['sum'];
            }

            if (!isset($_REQUEST['payment_system']) OR empty($_REQUEST['payment_system'])) {
                return get_instance()->ajaxmsg->notify(get_lang('widget_donate.lang')['donate_ajax_empty_payment_method'])->danger();
            } else {
                $vars["payment_system"] = $_REQUEST['payment_system'];
            }

            if(!empty($_REQUEST['payment_method'])) {
                $vars["payment_method"] = $_REQUEST['payment_method'];
            }

            if(!empty($_REQUEST['method_currency'])) {
                $vars["method_currency"] = $_REQUEST['method_currency'];
            }

            if(isset($_REQUEST['custom_fields']) && !empty($_REQUEST['custom_fields']) && is_array($_REQUEST['custom_fields'])) {
                $vars['custom_fields'] = $_REQUEST['custom_fields'];
            }

            $isCustomIntegration = false;

            if(strpos($vars['payment_system'], 'cstm:') === 0) {

                $handlerClass = PAYMENT_INTEGRATIONS[str_replace('cstm:', '', $vars['payment_system'])] ?? null;

                if ($handlerClass && class_exists($handlerClass)) {

                    $handlerInstance = new $handlerClass();

                    if (($handlerInstance instanceof PaymentHandler) === false) {
                        return get_instance()->ajaxmsg->notify('Invalid payment handler')->danger();
                    }

                    if(!method_exists($handlerInstance, 'getStatus') || !$handlerInstance->getStatus()) {
                        return get_instance()->ajaxmsg->notify('Payment method is currently unavailable')->danger();
                    }

                    if(!method_exists($handlerInstance, 'getIdentifier') || $handlerInstance->getIdentifier() !== str_replace('cstm:', '', $vars['payment_system'])) {
                        return get_instance()->ajaxmsg->notify('Payment handler identifier mismatch')->danger();
                    }

                    if(method_exists($handlerInstance, 'getCurrency') && $handlerInstance->getCurrency()) {
                        $vars['currency'] = $handlerInstance->getCurrency();
                    }

                    $isCustomIntegration = true;

                }
            }

            if (isset($this->advertising['gawpid']) AND !empty($this->advertising['gawpid'])) {

                $vars["gaid"] = $this->advertising['gawpid'];

                if (isset($_COOKIE['_ga']) AND !empty($_COOKIE['_ga'])) {
                    $vars["_ga"] = $_COOKIE['_ga'];
                }

                if(get_utm('ga_session_id')) {
                    $vars['ga_session_id'] = get_utm('ga_session_id');
                }

                if(!empty($this->advertising['measurement_id'])) {
                    $vars['measurement_id'] = $this->advertising['measurement_id'];
                }

            }

            if (isset($this->advertising['ymid']) AND !empty($this->advertising['ymid'])) {
                if (isset($_COOKIE['_ym_uid']) AND !empty($_COOKIE['_ym_uid']))
                    $vars["_ym"] = $_COOKIE['_ym_uid'];

                $vars["ymid"] = $this->advertising['ymid'];
            }

            $vars["type"] = 1;

            $payment = new Payment();
            $response = $payment->createOrder($vars);

            if ($response['ok']) {

                if (isset($response['error'])) {

                    if (isset($response["response"]->input)) {
                        $send = get_instance()->ajaxmsg->notify($response['error'])->input_error($response["response"]->input)->danger();
                    } else {
                        $send = get_instance()->ajaxmsg->notify($response['error'])->danger();
                    }

                } else {
                    if (isset($response["response"]->redirect)) {

                        if ($isCustomIntegration) {

                            if(empty($response["response"]->order_id)) {
                                return get_instance()->ajaxmsg->notify('order not created')->danger();
                            }

                            $orderData['order_id'] = (string) $response["response"]->order_id;
                            $orderData = array_merge($orderData, $vars);

                            try {
                                /** @var CheckoutResponse */
                                $checkoutResponse = $handlerInstance->checkout($orderData);
                            } catch (\Exception $e) {
                                return get_instance()->ajaxmsg->notify('Error during checkout: ' . $e->getMessage())->danger();
                            }

                            $response['response']->redirect = $checkoutResponse->redirect ?? null;

                            if (isset($checkoutResponse->post) && !empty($checkoutResponse->post)) {
                                return get_instance()->ajaxmsg->post($checkoutResponse->post)->notify($checkoutResponse->success, $checkoutResponse->redirect)->success();
                            } else {
                                return get_instance()->ajaxmsg->notify($checkoutResponse->success, $checkoutResponse->redirect)->success();
                            }

                        }

                        if (isset($response["response"]->post) AND !empty($response["response"]->post)) {
                            $send = get_instance()->ajaxmsg->post($response["response"]->post)->notify((string)$response["response"]->success, (string)$response["response"]->redirect)->success();
                        } else {
                            $send = get_instance()->ajaxmsg->notify((string)$response["response"]->success, (string)$response["response"]->redirect)->success();
                        }

                    } else {
                        $send = get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_login_error'])->danger();
                    }

                }

            } else {
                $send = get_instance()->ajaxmsg->notify('Error: ' . $response['http_error'] . '<br>Code: ' . $response['http_code'])->danger();
            }

        } else {
            $send = get_instance()->ajaxmsg->notify(get_lang('api.lang')['session_lost'])->location('sign-in')->danger();
        }

        return $send;

    }

    public function ajax_checkout_no_auth()
    {

        if (!captcha_check()) {
            return get_instance()->ajaxmsg->notify(get_lang('signup.lang')['signup_ajax_error_captcha'])->eval_js(captcha_reload('checkout'))->danger();
        }

        $vars = [];

        if (!isset($_REQUEST['sum']) OR empty($_REQUEST['sum'])) {
            return get_instance()->ajaxmsg->notify(get_lang('widget_donate.lang')['donate_ajax_empty_sum'])->danger();
        } else {
            $vars["sum"] = $_REQUEST['sum'];
        }

        if (!isset($_REQUEST['payment_system']) OR empty($_REQUEST['payment_system'])) {
            return get_instance()->ajaxmsg->notify(get_lang('widget_donate.lang')['donate_ajax_empty_payment_method'])->danger();
        } else {
            $vars["payment_system"] = $_REQUEST['payment_system'];
        }

        if(!empty($_REQUEST['payment_method'])) {
            $vars["payment_method"] = $_REQUEST['payment_method'];
        }

        if(!empty($_REQUEST['method_currency'])) {
            $vars["method_currency"] = $_REQUEST['method_currency'];
        }

        if(isset($_REQUEST['custom_fields']) && !empty($_REQUEST['custom_fields']) && is_array($_REQUEST['custom_fields'])) {
            $vars['custom_fields'] = $_REQUEST['custom_fields'];
        }

        $isCustomIntegration = false;

        if(strpos($vars['payment_system'], 'cstm:') === 0) {

            $handlerClass = PAYMENT_INTEGRATIONS[str_replace('cstm:', '', $vars['payment_system'])] ?? null;

            if ($handlerClass && class_exists($handlerClass)) {

                $handlerInstance = new $handlerClass();

                if (($handlerInstance instanceof PaymentHandler) === false) {
                    return get_instance()->ajaxmsg->notify('Invalid payment handler')->danger();
                }

                if(!method_exists($handlerInstance, 'getStatus') || !$handlerInstance->getStatus()) {
                    return get_instance()->ajaxmsg->notify('Payment method is currently unavailable')->danger();
                }

                if(!method_exists($handlerInstance, 'getIdentifier') || $handlerInstance->getIdentifier() !== str_replace('cstm:', '', $vars['payment_system'])) {
                    return get_instance()->ajaxmsg->notify('Payment handler identifier mismatch')->danger();
                }

                if(method_exists($handlerInstance, 'getCurrency') && $handlerInstance->getCurrency()) {
                    $vars['currency'] = $handlerInstance->getCurrency();
                }

                $isCustomIntegration = true;

            }
        }

        if (!isset($_REQUEST['recipient']) OR empty($_REQUEST['recipient'])) {
            return get_instance()->ajaxmsg->notify(get_lang('widget_donate.lang')['donate_ajax_empty_recipient'])->danger();
        } else {
            $vars["recipient"] = $_REQUEST['recipient'];
        }

        if (!isset($_REQUEST['type_id']) OR empty($_REQUEST['type_id'])) {
            return get_instance()->ajaxmsg->notify(get_lang('widget_donate.lang')['donate_ajax_empty_type_id'])->danger();
        } else {
            $vars["type_id"] = $_REQUEST['type_id'];
        }

        if (isset($this->advertising['gawpid']) AND !empty($this->advertising['gawpid'])) {

            $vars["gaid"] = $this->advertising['gawpid'];

            if (isset($_COOKIE['_ga']) AND !empty($_COOKIE['_ga'])) {
                $vars["_ga"] = $_COOKIE['_ga'];
            }

            if(get_utm('ga_session_id')) {
                $vars['ga_session_id'] = get_utm('ga_session_id');
            }

            if(!empty($this->advertising['measurement_id'])) {
                $vars['measurement_id'] = $this->advertising['measurement_id'];
            }

        }

        if (isset($this->advertising['ymid']) AND !empty($this->advertising['ymid'])) {
            if (isset($_COOKIE['_ym_uid']) AND !empty($_COOKIE['_ym_uid']))
                $vars["_ym"] = $_COOKIE['_ym_uid'];

            $vars["ymid"] = $this->advertising['ymid'];
        }

        $vars["type"] = 2;

        $payment = new Payment();
        $response = $payment->createOrder($vars);

        if ($response['ok']) {

            if (isset($response['error'])) {
                if (isset($response["response"]->input)) {
                    $send = get_instance()->ajaxmsg->notify($response['error'])->input_error($response["response"]->input)->danger();
                } else {
                    $send = get_instance()->ajaxmsg->notify($response['error'])->danger();
                }
            } else {
                if (isset($response["response"]->redirect)) {

                    if ($isCustomIntegration) {

                        if(empty($response["response"]->order_id)) {
                            return get_instance()->ajaxmsg->notify('order not created')->danger();
                        }

                        $orderData['order_id'] = (string) $response["response"]->order_id;
                        $orderData = array_merge($orderData, $vars);

                        try {
                            /** @var CheckoutResponse */
                            $checkoutResponse = $handlerInstance->checkout($orderData);
                        } catch (\Exception $e) {
                            return get_instance()->ajaxmsg->notify('Error during checkout: ' . $e->getMessage())->danger();
                        }

                        $response['response']->redirect = $checkoutResponse->redirect ?? null;

                        if (isset($checkoutResponse->post) && !empty($checkoutResponse->post)) {
                            return get_instance()->ajaxmsg->post($checkoutResponse->post)->notify($checkoutResponse->success, $checkoutResponse->redirect)->success();
                        } else {
                            return get_instance()->ajaxmsg->notify($checkoutResponse->success, $checkoutResponse->redirect)->success();
                        }

                    }

                    if (isset($response["response"]->post) AND !empty($response["response"]->post) > 0) {
                        $send = get_instance()->ajaxmsg->post($response["response"]->post)->notify((string)$response["response"]->success, (string)$response["response"]->redirect)->success();
                    } else {
                        $send = get_instance()->ajaxmsg->notify((string)$response["response"]->success, (string)$response["response"]->redirect)->success();
                    }

                } else {
                    $send = get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_login_error'])->danger();
                }
            }
        } else {
            $send = get_instance()->ajaxmsg->notify('Error: ' . $response['http_error'] . '<br>Code: ' . $response['http_code'])->danger();
        }

        return $send;

    }

    public function ajax_refresh_balance()
    {

        $api = new GlobalApi();
        $vars = array('temp');

        if (get_instance()->session->isLogin()) {

            $response = $api->refresh_balance($vars);

            if ($response['ok']) {

                if (isset($response['error'])) {
                    if (isset($response["response"]->input))
                        $send = get_instance()->ajaxmsg->notify($response['error'])->input_error($response["response"]->input)->danger();
                    else
                        $send = get_instance()->ajaxmsg->notify($response['error'])->danger();

                } else {

                    if (isset($response["response"]->data->user_data)) {

                        $data = json_encode($response["response"]->data);
                        $data = json_decode($data, true);
                        get_instance()->session->updateSessionDB($data);

                        $send = get_instance()
                                ->ajaxmsg
                                ->notify((string)$response["response"]->success)
                                ->broadcast('main_balance', get_instance()->session->getBalance('main'), 'updateBalance')
                                ->broadcast('bonus_balance', get_instance()->session->getBalance('bonus'), 'updateBalance')
                                ->success();

                    } else
                        $send = get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_login_error'])->danger();
                }
            } else {
                $send = get_instance()->ajaxmsg->notify('Error: ' . $response['http_error'] . '<br>Code: ' . $response['http_code'])->danger();
            }
        }else
            $send = get_instance()->ajaxmsg->notify(get_lang('api.lang')['session_lost'])->location('sign-in')->danger();

        return $send;
    }

}