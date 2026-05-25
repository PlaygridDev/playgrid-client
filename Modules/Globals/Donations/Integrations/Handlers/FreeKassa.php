<?php

namespace Modules\Globals\Donations\Integrations\Handlers;

use Modules\Globals\Donations\Integrations\DTO\CheckoutResponse;
use Modules\Globals\Donations\Integrations\PaymentHandler;
use Modules\Globals\Donations\Integrations\HandlerInterface;
use Modules\Globals\Donations\Integrations\Exceptions\PaymentWebhookException;

/**
 * ===================================================================================================================
 *
 * Внимание!
 * Данный код является примером реализации и не гарантирует корректную интеграцию FreeKassa без дополнительной настройки и доработки.
 * Это шаблон, который можно использовать в качестве основы для интеграции новых способов оплаты.
 *
 *====================================================================================================================
*/
class FreeKassa extends PaymentHandler implements HandlerInterface
{

    /**
     * Статус доступности платежного метода.
     * Если false, метод не будет отображаться
     * пользователям и не будет обрабатывать платежи.
     */
    private static bool $status = false;

    /**
     * Уникальный идентификатор платежного метода,
     * используемый для его распознавания в системе.
     * Должен быть уникальным среди всех интеграций.
     */
    private static string $identifier = 'freekassa';

    /**
     * Описание заказа, которое будет передаваться в платежную систему и отображаться пользователю при оплате.
     * Ассоциативный массив, где ключ - код языка (например, 'en', 'ru'), а значение - описание на этом языке.
     */
    private static array $orderDescription = [
        'ru' => 'Пожертвование на развитие проекта',
        'en' => 'Donation for project development',
        // Add more languages as needed
    ];

    /**
     * Код валюты для платежа
     * например: 'RUB' | 'USD' | 'EUR' и т.д.
     */
    private static string $currency = 'RUB';

    /**
     * На каком месте в списке доступных платежных систем будет отображаться этот метод.
     */
    private static int $sortOrder = 1;

    /**
     * URL для перенаправления пользователей в случае успешной оплаты.
     * Этот параметр можно использовать при создании ордера, используйте getSuccessUrl() для получения этого URL в процессе обработки платежа.
     */
    private static string $successUrl = '';

    /**
     * URL для перенаправления пользователей в случае неудачной оплаты или отмены платежа.
     * Этот параметр можно использовать при создании ордера, используйте getFailUrl() для получения этого URL в процессе обработки платежа.
     */
    private static string $failUrl = '';

    /**
     * URL для получения уведомлений (вебхуков).
     * Этот параметр можно использовать при создании ордера, используйте getWebhookUrl() для получения этого URL в процессе обработки платежа.
     */
    private static string $webhookUrl = '';

    /**
     * Ассоциативный массив для хранения конфигурационных параметров, необходимых для интеграции с FreeKassa.
     */
    private static array $config = [
        'merchant_id'   => '',  // merchant ID
        'secret_key'    => '',  // secret key
        'secret_key2'   => '',  // secret key #2 (used for webhook signature)
    ];

    /**
     * Массив разрешенных IP-адресов для входящих вебхуков от FreeKassa.
     * Если IP-адрес отправителя не входит в этот список, вебхук будет отклонен для обеспечения безопасности.
     * Для отключения проверки IP-адресов, оставьте массив пустым (не рекомендуется).
     */
    private static array $allowedIps = [
        // '127.0.0.1',
        // '127.0.0.2'
    ];

    public function __construct()
    {
        $this->setStatus(self::$status);
        $this->setIdentifier(self::$identifier);
        $this->setSortOrder(self::$sortOrder);
        $this->setDescription(self::$orderDescription);
        $this->setCurrency(self::$currency);
        $this->setSuccessUrl(self::$successUrl);
        $this->setFailUrl(self::$failUrl);
        $this->setWebhookUrl(self::$webhookUrl);
        $this->setConfig(self::$config);
        $this->setAllowedIps(self::$allowedIps);
    }

    /**
     * Выполняет подготовку платежа и возвращает данные
     * для перенаправления пользователя на платёжный шлюз.
     *
     * @param array{
     *     order_id: string,
     *     sum: string,
     *     payment_system: string,
     *     currency: string,
     *     gaid: string,
     *     _ga: string,
     *     ga_session_id: string,
     *     type: int
     * } $orderData Данные платёжного заказа и параметры сессии.
     *
     * @return CheckoutResponse Содержит URL перенаправления и параметры для переадресации пользователя.
     */
    public function checkout(array $orderData): CheckoutResponse
    {

        // проверяем статус метода оплаты, если он отключен - выбрасываем исключение
        if(!$this->getStatus()) {
            throw new \Exception('FreeKassa payment method is currently unavailable.');
        }

        // проверяем валидность суммы платежа, если сумма не указана или меньше или равна нулю - выбрасываем исключение
        if(isset($orderData['sum']) && $orderData['sum'] <= 0) {
            throw new \Exception('Invalid payment amount.');
        }

        // проверяем ордер id, если он не указан - выбрасываем исключение
        if(empty($orderData['order_id'])) {
            throw new \Exception('Order ID is required for checkout.');
        }

        // формируем параметры для отправки на платёжный шлюз
        $params = [
            'm' => $this->getConfig('merchant_id'),
            'currency' => $this->getCurrency(),
            'oa' => $orderData['amount'],
            'o' => $orderData['order_id'],
            's' => md5(
                $this->getConfig('merchant_id')
                .':'.$orderData['sum']
                .':'.$this->getConfig('secret_key')
                .':'.$this->getCurrency()
                .':'.$orderData['order_id']
            )
        ];

        // формируем URL для перенаправления пользователя на платёжный шлюз с необходимыми параметрами
        $redirectUrl = 'https://pay.fk.money/?'.http_build_query($params);

        // возвращаем объект CheckoutResponse с URL для перенаправления пользователя
        return new CheckoutResponse($redirectUrl);

    }

    /**
     * Метод для обработки входящих вебхуков от FreeKassa.
     * Если данные валидны, метод возвращает true, иначе - false. В случае ошибок выбрасывает исключения для логирования и отладки.
     * @return bool Возвращает true при успешной обработке вебхука, false при неудаче.
     * @throws PaymentWebhookException при возникновении ошибок в процессе обработки вебхука
     */
    public function webhook(): bool
    {

        // Примеры вариантов получения данных - GET, POST, raw input.
        // $payload = $_GET;
        /**
         * $payload = file_get_contents('php://input');
         * $payload = json_decode($payload, true);
         */

        // для FreeKassa данные обычно приходят в виде POST-запроса, поэтому используем $_POST для получения данных вебхука
        $payload = $_POST;

        try {

            if(empty($payload)) {
                throw new PaymentWebhookException('No data received in webhook request');
            }

            // проверяем наличие всех необходимых параметров в payload, если какой-то параметр отсутствует - выбрасываем исключение
            if(empty($payload['MERCHANT_ORDER_ID']) || empty($payload['intid']) || empty($payload['AMOUNT']) || empty($payload['SIGN'])) {
                throw new PaymentWebhookException('Missing required parameters in webhook request');
            }

            // вычисляем подпись на основе payload
            $signature = $this->getWebhookSignature($payload);

            // сравниваем вычисленную подпись с той, что пришла в запросе
            if(!hash_equals($signature, $payload['SIGN'])) {
                throw new PaymentWebhookException('Invalid webhook signature');
            }

            // если все проверки пройдены успешно, устанавливаем ID заказа для дальнейшей передачи в API
            $this->SetOrderId($payload['MERCHANT_ORDER_ID']);

            // устанавливаем внутренний ID платежа - payment_id
            if(!empty($payload['intid'])) {
                $this->setPaymentId($payload['intid']);
            }

            return true;

        } catch (PaymentWebhookException $e) {
            error_log("PaymentWebhookException: " . $e->getMessage());
            log_write('payment_errors', $e->getMessage());
            return false;
        }

    }

    /**
     * Вычисляет подпись запроса входящего вебхука для обеспечения безопасности и подтверждения подлинности уведомлений о платежах.
     * @param array $data контейнер для данных которые необходимо использовать для проверки.
     */
    public function getWebhookSignature(array $data): string
    {
        return md5($this->getConfig('merchant_id')
            .':'.$data['AMOUNT']
            .':'.$this->getConfig('secret_key2')
            .':'.$data['MERCHANT_ORDER_ID']
        );
    }

}