<?php

namespace Modules\Globals\Donations\Integrations;

use Donations\func;
use Modules\Globals\Donations\Integrations\DTO\CheckoutResponse;

interface HandlerInterface
{
    public function __construct();

    public function setStatus(bool $status): PaymentHandler;
    public function getStatus(): bool;

    public function setIdentifier(string $identifier): PaymentHandler;
    public function getIdentifier(): string;

    public function setDescription(array $orderDescription): PaymentHandler;
    public function getDescription(string $lang = 'en'): string;

    public function setCurrency(string $currency): PaymentHandler;
    public function getCurrency(): string;

    public function setSuccessUrl(string $url): PaymentHandler;
    public function getSuccessUrl(): string;

    public function setFailUrl(string $url): PaymentHandler;
    public function getFailUrl(): string;

    public function setWebhookUrl(string $url): PaymentHandler;
    public function getWebhookUrl(): string;

    public function setSortOrder(int $sortOrder): PaymentHandler;
    public function getSortOrder(): int;

    public function setOrderId(string $orderId): PaymentHandler;
    public function getOrderId(): string;

    public function setAllowedIps(array $ips): PaymentHandler;
    public function getAllowedIps(): array;
    public function isIpAllowed(string $ip): bool;

    public function checkout(array $orderData): CheckoutResponse;
    public function webhook(): bool;

    public function getWebhookSignature(array $data): string;

}