<?php

use Modules\Globals\Donations\Integrations\PaymentHandler;

class PaymentController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function webhook(string $paymentSystem)
    {

        header('Content-Type: application/json');

        if(!defined('PAYMENT_INTEGRATIONS') || empty(PAYMENT_INTEGRATIONS) || !key_exists($paymentSystem, PAYMENT_INTEGRATIONS)) {

            http_response_code(404);

            exit(json_encode([
                'status' => 'error',
                'message' => 'Payment system not supported'
            ]));

        }

        if(class_exists(PAYMENT_INTEGRATIONS[$paymentSystem])) {
            $handlerClass = PAYMENT_INTEGRATIONS[$paymentSystem];
            $handler = new $handlerClass();
        } else {
            http_response_code(500);
            exit(json_encode([
                'status' => 'error',
                'message' => 'Payment handler class not found'
            ]));
        }

        $ipAddress = get_ip();

        if (!$handler->isIpAllowed($ipAddress)) {
            http_response_code(403);
            exit(json_encode([
                'status' => 'error',
                'message' => 'Forbidden: IP address not allowed'
            ]));
        }

        if(!$handler->getStatus()) {
            http_response_code(409);
            exit(json_encode([
                'status' => 'error',
                'message' => 'Payment system is currently unavailable'
            ]));
        }

        $webhookResult = $handler->webhook();

        if ($webhookResult) {

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => API_URL . '/v2/payment/order/complete',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 3,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    'payment_system' => 'cstm:'.$paymentSystem,
                    'order_id' => $handler->getOrderId(),
                ]),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . PaymentHandler::APPLICATION_TOKEN
                ),
            ));

            $response = json_decode(curl_exec($curl));
            if(json_last_error()) {
                http_response_code(500);
                exit(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to parse API response: ' . json_last_error_msg()]
                ));
            }

            $resultCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            $err = curl_errno($curl);
            curl_close($curl);

            if ($err) {
                http_response_code(500);
                exit(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to complete order: errno ' . $err
                ]));
            }

            if($resultCode < 200 || $resultCode >= 300) {
                http_response_code($resultCode);
                exit(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to complete order: API responded with status code ' . $resultCode,
                    'response' => $response
                ]));
            }

            http_response_code(200);
            exit(json_encode([
                'status' => 'success',
                'response' => $response
            ]));

        } else {
            http_response_code(500);
            exit(json_encode([
                'status' => 'error',
                'message' => 'Failed to process webhook'
            ]));
        }

    }

}