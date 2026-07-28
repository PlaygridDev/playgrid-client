<?php

namespace Modules\Globals\SignIn;

use ApiLib\GlobalApi;
use Modules\Globals\Security\Enum\ActionType;
use Modules\MainModulesClass;
use Session\MetadataBag;

class SignIn extends MainModulesClass
{

    public function info()
    {
        return array(
            "author" => "mmoweb",
            "game" => "Global",
            "version" => "1.0",
            "description" => array(
                'ru' => 'Модуль авторизации',
                'en' => 'Registration module',
            ),
            "url" => "https://mmoweb.biz/",
            "created" => "24.05.2019",
            "lastUpdated" => "24.05.2019",
            "class" => __CLASS__,

        );
    }

    public function onAjax()
    {

        return array(
            'signin' => function () {
                return $this->signin();
            },
            'signin_social' => function () {
                return $this->signin_social();
            },
        );

    }


    /*
     * CODE
     */

    public function signin()
    {

        $vars = array();

        if(isset($_POST['2fa']) && !empty($_POST['2fa'])) {
            $vars['2fa'] = $_POST['2fa'];
            $_POST = MetadataBag::get('2fa_signin') ?? [];
        }

        $vars['type'] = 'signin';

        $signinType = '';

        if ($_POST['type_login'] == 'phone') {

            //Проверка телефона
            if (!isset($_POST['phone']) OR empty($_POST['phone'])) {
                return get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_empty_phone'])->danger();
            } else {
                $vars["phone"] = str_replace(array(' ', '-'), '', $_POST['phone']);
            }

            //Проверка телефона
            if (!isset($_POST['phone_code']) OR empty($_POST['phone_code'])) {
                return get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_empty_phone_code'])->danger();
            } else {
                $vars["phone_code"] = $_POST['phone_code'];
            }

            $vars['type_login'] = 'phone';

            $signinType = 'phone';

        } else {

            if (!isset($_POST['email']) OR empty($_POST['email'])) {
                return get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_empty_email_phone'])->danger();
            }

            if (preg_match("/.+@.+\..+/i", $_POST['email'])) {
                $vars["email"] = $_POST['email'];
                $vars['type_login'] = 'email';
                $signinType = 'email';
            } else {
                $vars['email'] = $_POST['email'];
                $vars["login"] = $_POST['email'];
                $vars['type_login'] = 'login';
                $vars['type'] = 'signin_ig_login';
                $signinType = 'game_account';
            }

        }

        if (!array_key_exists($vars['type_login'], get_instance()->config['cabinet']['signin_type'])) {
            return get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_login_error_type'])->danger();
        }

        if (!isset($_POST['password']) OR empty($_POST['password'])) {
            return get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_empty_password'])->danger();
        } else {
            $vars["password"] = $_POST['password'];
        }

        if (!isset($_POST['sid']) OR empty($_POST['sid'])) {
            return get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_empty_sid'])->danger();
        } else {
            $vars["sid"] = $_POST['sid'];
            get_instance()->set_sid((int) $vars["sid"], false);
        }

        if (isset($_POST["remember-me"])) {
            $vars["remember-me"] = $_POST["remember-me"];
        }


        if (isset($_SESSION['promo_game']['status']) AND $_SESSION['promo_game']['status'] == 'finish') {
            $vars["promo_game"] = $_SESSION['promo_game'];
        }

        if (!captcha_check() && !isset($vars['2fa'])) {
            return get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_error_captcha'])->eval_js(captcha_reload('sign_in'))->danger();
        }

        $vars["utm"] = get_utm();

        $signin = new \ApiLib\v2\MasterAccount\SignIn();

        switch($signinType) {
            case 'email':
                $apiResponse = $signin->signInWithEmail($vars);
                break;
            case 'phone':
                $apiResponse = $signin->signInWithPhone($vars);
                break;
            case 'game_account':
                $apiResponse = $signin->signInWithGameAccount($vars);
                break;
            default:
                return get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_login_error_type'])->eval_js(captcha_reload('sign_in'))->danger();
                break;
        }

        if ($apiResponse['ok']) {

            $responseMessage = !empty($apiResponse['error'])
                            ? $apiResponse['error']
                            : $apiResponse["response"]->success ?? null;

            $statusCode = '';

            if (isset($apiResponse["response"]->status_code)) {

                $statusCode = (string) $apiResponse["response"]->status_code;

                if($statusCode === 'TWO_FACTOR_AUTH_REQUIRED') {

                    MetadataBag::set('2fa_signin', $vars);

                    /**
                     * @var \Modules\Globals\Security\Security $securityModule
                     */
                    $securityModule = get_instance()->getModule('Modules\Globals\Security\Security');

                    $twoFactorMethods = json_decode(json_encode($apiResponse["response"]->two_factor_methods ?? []), true);

                    return $securityModule->twoFactorVerificationPopup(
                        ActionType::SIGNIN,
                        $twoFactorMethods ?? []
                    );

                }
            }

            $responseMessage = get_lang('signin.lang')['signin_status_codes'][$statusCode] ?? $responseMessage;

            $response = get_instance()->ajaxmsg
                ->notify($responseMessage)
                ->variables(['retry_after' => (string) $apiResponse['response']->retry_after ?? 0]);

            $response = isset($apiResponse['error'])
                ? $response->eval_js(captcha_reload('sign_in'))->danger()
                : $response->location('/panel', 3000)->success();

            if (isset($apiResponse["response"]->data->session_data)) {
                $data = json_encode($apiResponse["response"]->data);
                $data = json_decode($data, true);
                get_instance()->session->setSessionDB($data);
                get_instance()->session->setSessionIdCookie(
                    $data['session_data']['session_id'],
                    $data['session_data']['session_end']
                );
            }

            return $response;

        } else {
            return get_instance()->ajaxmsg->notify('Error: ' . $apiResponse['http_error'] . '<br>Code: ' . $apiResponse['http_code'])->eval_js(captcha_reload('sign_in'))->danger();
        }

    }


    public function signin_social()
    {
        $api = new GlobalApi();
        $vars = array();

        $vars['type'] = 'signin_social';


        if (!isset($_POST['token']) OR empty($_POST['token']))
            return get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_empty_email_phone'])->danger();
        else
            $vars["token"] = $_POST['token'];


        $vars['host'] = $_SERVER['HTTP_HOST'];

        if (isset($_SESSION['promo_game']['status']) AND $_SESSION['promo_game']['status'] == 'finish')
            $vars["promo_game"] = $_SESSION['promo_game'];

        //Передаем UTM метки
        $vars["utm"] = get_utm();

        $response = $api->signin($vars);



        if ($response['ok']) {

            if (isset($response['error'])) {
                if (isset($response["response"]->input))
                    $send = get_instance()->ajaxmsg->notify($response['error'])->input_error($response["response"]->input)->danger();
                else
                    $send = get_instance()->ajaxmsg->notify($response['error'])->danger();

            } else {

                if (isset($response["response"]->data->session_data)){

                    $data = json_encode($response["response"]->data);
                    $data = json_decode($data, true);
                    get_instance()->session->setSessionDB($data);
                    get_instance()->session->setSessionIdCookie(
                        $data['session_data']['session_id'],
                        $data['session_data']['session_end']
                    );

                    $send = get_instance()->ajaxmsg->notify( (string) $response["response"]->success, '/panel')->success();

                }else
                    $send = get_instance()->ajaxmsg->notify(get_lang('signin.lang')['signin_ajax_login_error'])->danger();

            }

        } else {
            $send = get_instance()->ajaxmsg->notify('Error: ' . $response['http_error'] . '<br>Code: ' . $response['http_code'])->danger();
        }

        return $send;
    }

}