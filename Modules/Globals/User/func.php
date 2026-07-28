<?php


namespace User;


class func
{

    public $this_main = false;
    public function __construct($this_main)
    {
        /**@var $this_main \Modules\Globals\User\User*/
        $this->this_main = $this_main;
    }

    public function widget_account_list(){

        return get_instance()->fenom->fetch(
            get_tpl_file('widget_account_list_index.tpl', get_class($this->this_main)),
            array_merge(
                array(
                    'content_account_list' => $this->fragment_account_list(),
                ),
                get_lang('user.lang')
            )

        );


    }

    public function fragment_account_list(){

        if (!isset(get_instance()->session->session["user_data"]["account"]['error_exception']))
        {
            $platform = get_instance()->get_platform();
            $sid = get_instance()->get_sid();

            $server_info = isset(get_instance()->config['project']['server_info'][$platform][$sid]) ? get_instance()->config['project']['server_info'][$platform][$sid] : array();

            return get_instance()->fenom->fetch(
                get_tpl_file('widget_account_list_'.$platform.'.tpl', get_class($this->this_main)),
                array_merge(
                    array(
                        'payment_system' => get_instance()->config['payment_system'],
                        'server_info' => $server_info,


                        ),
                    get_lang('user.lang')
                )
            );

        }else{
            return get_instance()->fenom->fetch(
                get_tpl_file('widget_account_list_error.tpl', get_class($this->this_main)),
                get_lang('user.lang')
            );
        }



    }

    public function emailVerificationPopup()
    {

        if (!get_instance()->session->isLogin()) {
            return get_instance()->ajaxmsg->notify(get_lang('api.lang')['session_lost'])->location('sign-in')->danger();
        }

        if (
            (int) get_instance()->session->session["master_account"]["status"] !== 1
            && (int) get_instance()->session->session["master_account"]["email_valid"] !== 0
        ) {
            return get_instance()->ajaxmsg->notify(get_lang('email_verification.lang')['verification_not_need'])->danger();
        }

        $title = get_lang('email_verification.lang')['verification_title'];

        $content = get_instance()->fenom->fetch(
            get_tpl_file('email_verification_popup.tpl', get_class($this->this_main)),
            array_merge(
                get_lang('email_verification.lang')
            )
        );

        $footer = '';

        $send = get_instance()->ajaxmsg->popup($title, $content, $footer)->success();
        return $send;

    }

    public function emailVerificationSendCode()
    {

        if (!get_instance()->session->isLogin()) {
            return get_instance()->ajaxmsg->notify(get_lang('api.lang')['session_lost'])->danger();
        }

        if (
            (int) get_instance()->session->session["master_account"]["status"] !== 1
            && (int) get_instance()->session->session["master_account"]["email_valid"] !== 0
        ) {
            return get_instance()->ajaxmsg->notify(get_lang('email_verification.lang')['verification_not_need'])->danger();
        }

        if (!isset($_POST['email']) OR empty($_POST['email'])) {
            return get_instance()->ajaxmsg->notify(get_lang('email_verification.lang')['email_is_empty'])->danger();
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            return get_instance()->ajaxmsg->notify(get_lang('email_verification.lang')['email_is_invalid'])->danger();
        } else {
            $vars['email'] = $_POST['email'];
        }

        $user = new \ApiLib\v2\MasterAccount\User();
        $response = $user->emailVerificationSendCode($vars);

        if($response['ok']) {

                if (isset($response['error'])) {
                    return get_instance()->ajaxmsg
                        ->notify($response['error'])
                        ->variables(['retry_after' => (string) $response['response']->retry_after ?? 0])
                        ->danger();
                } else {
                    return get_instance()->ajaxmsg
                        ->notify((string)$response["response"]->success)
                        ->variables(['retry_after' => (string) $response['response']->retry_after ?? 0])
                        ->success();
                }

        } else {
            return get_instance()->ajaxmsg->notify('Error: ' . $response['http_error'] . '<br>Code: ' . $response['http_code'])->danger();
        }

    }

    public function emailVerificationConfirm()
    {

        if(!get_instance()->session->isLogin()) {
            return get_instance()->ajaxmsg->notify(get_lang('api.lang')['session_lost'])->danger();
        }

        if (
            (int) get_instance()->session->session["master_account"]["status"] !== 1
            && (int) get_instance()->session->session["master_account"]["email_valid"] !== 0
        ) {
            return get_instance()->ajaxmsg->notify(get_lang('email_verification.lang')['verification_not_need'])->danger();
        }

        if (!isset($_POST['code']) OR empty($_POST['code'])) {
            return get_instance()->ajaxmsg->notify(get_lang('email_verification.lang')['code_is_empty'])->danger();
        } elseif (!is_numeric($_POST['code']) || strlen($_POST['code']) !== 6) {
            return get_instance()->ajaxmsg->notify(get_lang('email_verification.lang')['code_is_invalid'])->danger();
        } else {
            $vars['code'] = $_POST['code'];
        }

        if (!isset($_POST['email']) OR empty($_POST['email'])) {
            return get_instance()->ajaxmsg->notify(get_lang('email_verification.lang')['email_is_empty'])->danger();
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            return get_instance()->ajaxmsg->notify(get_lang('email_verification.lang')['email_is_invalid'])->danger();
        } else {
            $vars['email'] = $_POST['email'];
        }

        $user = new \ApiLib\v2\MasterAccount\User();
        $response = $user->emailVerificationConfirm($vars);

        if($response['ok']) {

                if (isset($response['error'])) {
                    return get_instance()->ajaxmsg
                        ->notify($response['error'])
                        ->danger();
                } else {

                    if($response["response"]->data->master_account) {
                        $data = json_encode($response["response"]->data);
                        $data = json_decode($data, true);
                        get_instance()->session->updateSessionDB($data);
                    }

                    return get_instance()->ajaxmsg
                        ->notify((string) $response["response"]->success)
                        ->success();
                }

        } else {
            return get_instance()->ajaxmsg->notify('Error: ' . $response['http_error'] . '<br>Code: ' . $response['http_code'])->danger();
        }
    }

}