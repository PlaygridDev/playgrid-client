<?php

namespace Shop;


use Modules\Globals\Donations\Integrations\PaymentHandler;

use ApiLib\v2\Payment;
use ApiLib\v2\Plugins\Shop;

class func
{

    public $this_main = false;
    public $shop = array();

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
        'severpay_rub',
        'settlepay_pix',
        'settlepay_cbucvu',
        'abankcomua',
        'betatransfer',
        'wayforpay',
        'liqpay',
        'pay4game',
    );
    public $advertising = false;

    public function __construct($this_main)
    {
        $this->this_main = $this_main;
        $this->shop = &get_instance()->shop;

        if ($this->advertising === false) {
            $this->advertising = getConfig('advertising');
        }

        if (isset(get_instance()->config['payment_system']['sorting_pay'])) {
            $this->payment_list = get_instance()->config['payment_system']['sorting_pay'];
        }

        $userData = [
            'balance_total' => get_instance()->session->getTotalBalance(),
            'success_orders' => get_instance()->session->getSuccessOrders()
        ];

        foreach($this->payment_list as $system) {

            $availabilityConfigs = get_instance()->config['payment_system']['availability_settings'][$system] ?? null;

            if($availabilityConfigs) {

                $userTotal = (int) $availabilityConfigs['user_total'] ?? null;
                $userSuccessOrders = (int) $availabilityConfigs['success_orders'] ?? null;
                $condition = $availabilityConfigs['condition'] ?? null;

                if ($userTotal !== null && $userSuccessOrders !== null && $condition !== null) {
                    if ($condition === 'and') {
                        if ($userData['balance_total'] < $userTotal || $userData['success_orders'] < $userSuccessOrders) {
                            $this->payment_list = array_diff($this->payment_list, [$system]);
                        }
                    } elseif ($condition === 'or') {
                        if ($userData['balance_total'] < $userTotal && $userData['success_orders'] < $userSuccessOrders) {
                            $this->payment_list = array_diff($this->payment_list, [$system]);
                        }
                    }
                }
            }

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

    }


    public function widget_shop_no_auth(){

        $this->set_label_new();
        $sid = get_instance()->get_sid();

        $category = array();
        if (isset($this->shop['category'][$sid]))
            $category = $this->shop['category'][$sid];

        $shop = array();
        if (isset($this->shop['shop'][$sid])) {
            $shop = $this->shop['shop'][$sid];
        }

        return get_instance()->fenom->fetch(
            get_tpl_file('shop_list_no_auth.tpl', get_class($this->this_main)),
            array_merge(
                array(
                    'payment_system' => get_instance()->config['payment_system'],
                    'categorys' => $category,
                    'shops' => $shop,
                ),
                get_lang('shop.lang')
            )

        );

    }

    public function widget_item_no_auth($name_id)
    {

        if (strpos($name_id, '.') === false)
            return error_404_html();

        list(, $item_id) = explode('.', $name_id);
        $item_id = intval($item_id);

        $sid = get_instance()->get_sid();


        if (isset($this->shop['shop'][$sid][$item_id]))
            $item = $this->shop['shop'][$sid][$item_id];
        else
            return error_404_html();


        $category = array();
        if (isset($this->shop['category'][$sid]))
            $category = $this->shop['category'][$sid];

        if ($item['type'] == 'shop') {

            return get_instance()->fenom->fetch(
                get_tpl_file('widget_item_no_auth.tpl', get_class($this->this_main)),
                array_merge(
                    array(
                        'payment_system' => get_instance()->config['payment_system'],
                        'payment_list' => $this->payment_list,
                        'categorys' => $category,
                        'item' => $item,
                        'config_cabinet' => get_instance()->config['cabinet'],
                    ),
                    get_lang('shop.lang'),
                    get_lang('course.lang'),
                    get_lang('payment.lang')
                )

            );

        }else if(isset($item['type'])){

            return get_instance()->fenom->fetch(
                get_tpl_file('widget_service_no_auth.tpl', get_class($this->this_main)),
                array_merge(
                    array(
                        'payment_system' => get_instance()->config['payment_system'],
                        'categorys' => $category,
                        'item' => $item,
                        'tpl_enrollment' => $item['type']
                    ),
                    get_lang('shop.lang'),
                )

            );
        }else
            return error_404_html();
    }

    public function ajax_checkout_shop_no_auth()
    {

        if (!captcha_check()) {
            return get_instance()->ajaxmsg->notify(get_lang('signup.lang')['signup_ajax_error_captcha'])->eval_js(captcha_reload('checkout'))->danger();
        }

        $vars = array();

        if (!isset($_POST['shop_id']) OR empty($_POST['shop_id'])) {
            return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_empty_shop_id'])->danger();
        } else {
            $vars["shop_id"] = intval($_POST['shop_id']);
        }

        if (!isset($_POST['payment_system']) OR empty($_POST['payment_system'])) {
            return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_empty_payment_method'])->danger();
        } else {
            $vars["payment_system"] = $_POST['payment_system'];
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

        //тип доставки
        if (!isset($_POST['type_buy']) OR empty($_POST['type_buy']))
            return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_empty_type_buy'])->danger();
        else{

            if($_POST['type_buy'] == '#nick-name') { // Передать на персонажа по нику
                $vars["type_buy"] = 'nick_name';

                if (!isset($_POST['nick_name']) OR empty($_POST['nick_name']))
                    return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_empty_nick_name'])->danger();
                else
                    $vars["nick_name"] = $_POST['nick_name'];

            }else { //Доставить на склад МА
                $vars["type_buy"] = 'warehouse';

                if (!isset($_POST['email']) OR empty($_POST['email']))
                    return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_empty_email'])->danger();
                else
                    $vars["email"] = $_POST['email'];
            }
        }

        $sid = get_instance()->get_sid();

        if (!isset($this->shop['shop'][$sid][$vars["shop_id"]]))
            return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_shop_not_found'])->danger();
        else
            $shop = $this->shop['shop'][$sid][$vars["shop_id"]];

        if ($shop["complect"] == 0){
            if (!isset($_POST['items']) OR empty($_POST['items']))
                return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_empty_items'])->danger();
            else
                $vars["items"] = $_POST['items'];

            $vars["complect"] = $shop["complect"];
        }

        //Ставим флаг создания простого платежа
        $vars["type"] = 3;

        if (isset($this->advertising['gawpid']) AND !empty($this->advertising['gawpid'])){
            if (isset($_COOKIE['_ga']) AND !empty($_COOKIE['_ga']))
                $vars["_ga"] = $_COOKIE['_ga'];

            $vars["gaid"] = $this->advertising['gawpid'];
        }
        if (isset($this->advertising['ymid']) AND !empty($this->advertising['ymid'])) {
            if (isset($_COOKIE['_ym_uid']) AND !empty($_COOKIE['_ym_uid']))
                $vars["_ym"] = $_COOKIE['_ym_uid'];

            $vars["ymid"] = $this->advertising['ymid'];
        }

        $payment = new Payment();
        $response = $payment->createOrder($vars);

        if ($response['ok']) {

            if (isset($response['error'])) {
                if (isset($response["response"]->input))
                    $send = get_instance()->ajaxmsg->notify($response['error'])->input_error($response["response"]->input)->danger();
                else
                    $send = get_instance()->ajaxmsg->notify($response['error'])->danger();
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

                    $send = get_instance()->ajaxmsg->post($response["response"]->post)->notify((string)$response["response"]->success, (string)$response["response"]->redirect)->success();
                } else
                    $send = get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_login_error'])->danger();
            }

        } else {
            $send = get_instance()->ajaxmsg->notify('Error: ' . $response['http_error'] . '<br>Code: ' . $response['http_code'])->danger();
        }


        return $send;
    }


    public function widget_shop_advertising(){
        $sid = get_instance()->get_sid();


        if (!isset(get_instance()->config['project']['server_menu'][$sid]['shop']))
            return '';



        $category = array();
        if (isset($this->shop['category'][$sid]))
            $category = $this->shop['category'][$sid];

        $shop = array();
        if (isset($this->shop['shop'][$sid]))
            $shop = $this->shop['shop'][$sid];

        array_sort_by_column($shop, 'sale_id', SORT_DESC);

        if (count($shop) > 6){
            shuffle($shop);
            $shop = array_slice($shop, 0, 6);
        }

        return get_instance()->fenom->fetch(
            get_tpl_file('widget_shop_advertising.tpl', get_class($this->this_main)),
            array_merge(
                array(
                    'payment_system' => get_instance()->config['payment_system'],
                    'categorys' => $category,
                    'shops' => $shop,
                    'sale_ma' => get_instance()->session->getDiscount('shop')
                ),
                get_lang('shop.lang')
            )

        );
    }

    public function widget_item(){

        $name_id = get_instance()->url->segment(3);

        if (strpos($name_id, '.') === false)
            return error_404_html();

        list( ,$item_id) = explode('.', $name_id);
        $item_id = intval($item_id);

        $sid = get_instance()->get_sid();

        if (isset($this->shop['shop'][$sid][$item_id]))
            $item = $this->shop['shop'][$sid][$item_id];
        else
            return error_404_html();

        $category = array();
        if (isset($this->shop['category'][$sid]))
            $category = $this->shop['category'][$sid];


        if ($item['type'] == 'shop') {

            return get_instance()->fenom->fetch(
                get_tpl_file('widget_item.tpl', get_class($this->this_main)),
                array_merge(
                    array(
                        'payment_system' => get_instance()->config['payment_system'],
                        'categorys' => $category,
                        'item' => $item,
                        'char_list' => get_instance()->session->getGameChars(),
                        'sale_ma' => get_instance()->session->getDiscount('shop')
                    ),
                    get_lang('shop.lang')
                )

            );
        }else{

            return get_instance()->fenom->fetch(
                get_tpl_file('widget_service.tpl', get_class($this->this_main)),
                array_merge(
                    array(
                        'payment_system' => get_instance()->config['payment_system'],
                        'categorys' => $category,
                        'item' => $item,
                        'char_list' => get_instance()->session->getGameChars(),
                        'char_list_full' => get_instance()->session->getGameChars(false, true),
                        'sale_ma' => get_instance()->session->getDiscount('service'),

                        'tpl_enrollment' => $item['type']
                    ),
                    get_lang('shop.lang')
                )

            );
        }
    }


    public function widget_shop(){
        $this->set_label_new();
        $sid = get_instance()->get_sid();

        $category = array();
        if (isset($this->shop['category'][$sid]))
            $category = $this->shop['category'][$sid];

        $shop = array();
        if (isset($this->shop['shop'][$sid]))
            $shop = $this->shop['shop'][$sid];

        return get_instance()->fenom->fetch(
            get_tpl_file('shop_list.tpl', get_class($this->this_main)),
            array_merge(
                array(
                    'payment_system' => get_instance()->config['payment_system'],
                    'categorys' => $category,
                    'shops' => $shop,
                    'sale_ma' => get_instance()->session->getDiscount('shop')
                ),
                get_lang('shop.lang')
            )

        );
    }

    public function ajax_buy_shop()
    {
        $vars = array();

        if (get_instance()->session->isLogin()) {

            //ид магазина
            if (!isset($_POST['shop_id']) OR empty($_POST['shop_id']))
                return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_empty_shop_id'])->danger();
            else
                $vars["shop_id"] = intval($_POST['shop_id']);

            //тип доставки
            if (!isset($_POST['type_buy']) OR empty($_POST['type_buy']))
                return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_empty_type_buy'])->danger();
            else{

                if($_POST['type_buy'] == '#ma') { //Получить в пределах ма
                    $vars["type_buy"] = 'ma';

                    if (!isset($_POST['account_name']) OR empty($_POST['account_name']))
                        return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_empty_account_name'])->danger();
                    else
                        $vars["account_name"] = $_POST['account_name'];

                    if (!isset($_POST['char_name']) OR empty($_POST['char_name']))
                        return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_empty_char_name'])->danger();
                    else
                        $vars["char_name"] = $_POST['char_name'];

                }elseif($_POST['type_buy'] == '#nick-name') { // Передать на персонажа по нику
                    $vars["type_buy"] = 'nick_name';

                    if (!isset($_POST['nick_name']) OR empty($_POST['nick_name']))
                        return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_empty_nick_name'])->danger();
                    else
                        $vars["nick_name"] = $_POST['nick_name'];

                }else //Доставить на склад МА
                    $vars["type_buy"] = 'warehouse';

            }

            $sid = get_instance()->get_sid();

            if (!isset($this->shop['shop'][$sid][$vars["shop_id"]])) {
                return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_shop_not_found'])->danger();
            } else {
                $shop = $this->shop['shop'][$sid][$vars["shop_id"]];
            }

            if ($shop["complect"] == 0) {
                if (!isset($_POST['items']) OR empty($_POST['items'])) {
                    return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_empty_items'])->danger();
                } else {
                    $vars["items"] = $_POST['items'];
                }

                $vars["complect"] = $shop["complect"];
            }

            $shop = new Shop();
            $response = $shop->buyItem($vars);

            if ($response['ok']) {

                if (isset($response['error'])) {
                    if (isset($response["response"]->input)) {
                        $send = get_instance()->ajaxmsg->notify($response['error'])->input_error($response["response"]->input)->danger();
                    } else {
                        $send = get_instance()->ajaxmsg->notify($response['error'])->danger();
                    }

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

    public function ajax_buy_service()
    {

        $vars = array();

        if (get_instance()->session->isLogin()) {

            //ид магазина
            if (!isset($_POST['shop_id']) OR empty($_POST['shop_id'])) {
                return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_empty_shop_id'])->danger();
            } else {
                $vars["shop_id"] = intval($_POST['shop_id']);
            }


            $sid = get_instance()->get_sid();

            if (!isset($this->shop['shop'][$sid][$vars["shop_id"]])) {
                return get_instance()->ajaxmsg->notify(get_lang('shop.lang')['ajax_shop_not_found'])->danger();
            }

            if (isset($_POST['items']) OR !empty($_POST['items'])) {
                $vars["items"] = $_POST['items'];
            }

            unset($_POST['module_form'],$_POST['module'],$_POST['shop_id'],$_POST['items']);
            //перебираем входяшие данные
            foreach ($_POST as $key => $item) {
                $vars[$key] = $item;
            }

            $shop = new Shop();
            $response = $shop->buyService($vars);

            if ($response['ok']) {

                if (isset($response['error'])) {
                    if (isset($response["response"]->input)) {
                        $send = get_instance()->ajaxmsg->notify($response['error'])->input_error($response["response"]->input)->danger();
                    } else {
                        $send = get_instance()->ajaxmsg->notify($response['error'])->danger();
                    }

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

    public function set_label_new(){
        $t = @filemtime(ROOT_DIR.'/Library/configs/shop.json');
        set_cookie('shop_new', $t, strtotime("+1 year"));
    }
}