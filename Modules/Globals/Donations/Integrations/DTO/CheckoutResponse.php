<?php

namespace Modules\Globals\Donations\Integrations\DTO;

class CheckoutResponse
{

    public string $redirect;
    public ?array $post;
    public string $success;

    public function __construct(string $redirectUrl, ?array $post = null)
    {

        $this->redirect = $redirectUrl;
        $this->post = null;

        $this->success = 'Redirect to payment gateway';

        if(!empty($post)) {
            foreach($post as &$postValue) {
                $postValue = (string) $postValue;
            }
            $this->post = $post;
        }

    }

}