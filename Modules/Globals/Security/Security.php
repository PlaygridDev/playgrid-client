<?php

namespace Modules\Globals\Security;

use AjaxMsg;
use Modules\MainModulesClass;
use User;
use Modules\Globals\Security\Enum\ActionType;
use Session\MetadataBag;

class Security extends MainModulesClass
{

    private array $securitySettings;
    private array $locales;

    private User $user;
    private AjaxMsg $ajaxMsg;

    public function __construct()
    {

        $this->securitySettings = getConfig('project')['security'] ?? [];
        $this->locales = get_lang('security.lang') ?? [];
        $this->user = get_instance()->session;
        $this->ajaxMsg = get_instance()->ajaxmsg;

    }

    public static function getActionDefinition(string $action)
    {
        switch ($action) {
            case ActionType::SIGNIN:
                return [
                    'module' => 'signin',
                    'module_form' => 'Modules\\Globals\\SignIn\\SignIn',
                    'auth' => false,
                ];
            case ActionType::TWO_FA_DISABLE:
                return [
                    'module' => 'disable_two_factor_auth_confirm',
                    'module_form' => "Modules\\Globals\\Settings\\Settings",
                    'auth' => true,
                ];
            default:
                return null;
        }
    }

    public function info()
    {
        return array(
            "author" => "mmoweb",
            "game" => "Global",
            "version" => "1.0",
            "description" => array(
                'ru' => 'Безопасность',
                'en' => 'Security',
            ),
            "url" => "https://mmoweb.biz/",
            "created" => "26.07.2026",
            "lastUpdated" => "26.07.2026",
            "class" => __CLASS__,

        );
    }

    public function onAjax()
    {
        return array(
          'two_factor_verification_popup' => function() { return $this->twoFactorVerificationPopup(); },
          'start_two_factor_verification' => function() { return $this->startTwoFactorVerification(); },
        );
    }

    public function twoFactorVerificationPopup(string $action = '', array $methods = [])
    {

        $payload = $_POST;

        if (empty($payload['action']) || !ActionType::isValid($payload['action'])) {

            if (empty($action)) {
                return $this->ajaxMsg
                    ->notify($this->getLocale('two_factor_verification_invalid_action'))
                    ->danger();
            }

            $payload['action'] = $action;
        }

        $action = $payload['action'];

        if(!$this->user->isLogin() && self::getActionDefinition($action)['auth'] === true) {
            return get_instance()->ajaxmsg->notify(get_lang('api.lang')['session_lost'])->location('sign-in')->danger();
        }

        $user2FAMethods = [];

        if(self::getActionDefinition($action)['auth'] && $this->user->isLogin()) {

            if(!$this->user->get2FAStatus()) {
                return $this->ajaxMsg->notify($this->getLocale('two_factor_auth_not_enabled'))->danger();
            }

            $user2FAMethods = $this->user->get2FAMethods();

        } else {

            if(!empty($methods)) {
                foreach($methods as $method) {
                    if(in_array($method['method'], ['email', 'phone', 'totp'])) {
                        $user2FAMethods[] = $method['method'];
                    }
                }
            }

        }

        $methods = [];
        foreach($user2FAMethods as $method) {
            $methods[$method] = [
                'label' => $this->getLocale('two_factor_auth_method_labels')[$method] ?? $method,
                'send_required' => $method !== 'totp',
            ];
        }

        $title = $this->getLocale('two_factor_verification_popup_title');

        $content = get_instance()->fenom->fetch(
            get_tpl_file('2fa_verification_popup.tpl', get_class($this)),
            array_merge(
                $this->getLocales(),
                [
                    'methods' => json_encode(
                            $methods,
                            JSON_UNESCAPED_UNICODE
                            | JSON_UNESCAPED_SLASHES
                            | JSON_HEX_AMP
                            | JSON_HEX_QUOT
                            | JSON_HEX_TAG
                        ),
                    'action' => $action,
                    'module' => self::getActionDefinition($action)['module'],
                    'module_form' => json_encode(
                        self::getActionDefinition($action)['module_form'],
                        JSON_UNESCAPED_UNICODE
                    ),
                ]
            )
        );

        $footer = '';

        return $this->ajaxMsg->popup($title, $content, $footer)->success();

    }


    private function startTwoFactorVerification()
    {

        $post = $_POST;

        if (!isset($post['method']) || empty($post['method'])) {
            return get_instance()->ajaxmsg->notify($this->getLocale('two_factor_verification_invalid_method'))->danger();
        } else {
            $payload['method'] = $post['method'];
        }

        if (!isset($post['action']) || empty($post['action']) || !ActionType::isValid($post['action'])) {
            return get_instance()->ajaxmsg->notify($this->getLocale('two_factor_verification_invalid_action'))->danger();
        } else {
            $payload['action'] = $post['action'];
        }


        if($this->getActionDefinition($payload['action'])['auth'] && $this->user->isLogin()) {
            if(!$this->user->get2FAStatus()) {
                return $this->ajaxMsg->notify($this->getLocale('two_factor_auth_not_enabled'))->danger();
            }
        }

        if($payload['action'] === ActionType::SIGNIN) {
            $payload['signin'] = MetadataBag::get('2fa_signin') ?? [];
        }

        $security = new \ApiLib\v2\MasterAccount\Security();
        $apiResponse = $security->startTwoFactorVerification($payload);

        if($apiResponse['ok']) {

                $responseMessage = !empty($apiResponse['error'])
                                ? $apiResponse['error']
                                : $apiResponse["response"]->success ?? null;

                $statusCode = (string) $apiResponse['response']->status_code;
                $responseMessage = $this->getLocale('two_factor_verification_status_codes')[$statusCode] ?? $responseMessage;

                $response = get_instance()->ajaxmsg
                    ->notify($responseMessage)
                    ->variables(['retry_after' => (string) $apiResponse['response']->retry_after ?? 0]);

                $response = isset($apiResponse['error'])
                    ? $response->danger()
                    : $response->success();

                return $response;

        } else {
            return get_instance()->ajaxmsg->notify('Error: ' . $apiResponse['http_error'] . '<br>Code: ' . $apiResponse['http_code'])->danger();
        }

    }

    private function getSettings()
    {
        return $this->securitySettings;
    }

    private function getLocale(string $key)
    {
        return $this->locales[$key] ?? '';
    }

    private function getLocales()
    {
        return $this->locales;
    }

}