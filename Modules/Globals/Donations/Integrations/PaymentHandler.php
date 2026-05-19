<?php

namespace Modules\Globals\Donations\Integrations;

abstract class PaymentHandler
{

    private bool $status;
    private string $identifier = '';
    private array $description;
    private string $currency;
    private string $orderId;
    private int $sortOrder;
    private string $successUrl;
    private string $failUrl;
    private string $webhookUrl;
    private array $config;
    private array $allowedIps;

    /**
     * Токен для аутентификации при взаимодействии с v2 Payment API.
     */
    const APPLICATION_TOKEN = '';

    public function setStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setDescription(array $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(string $lang = 'en'): string
    {
        return $this->description[$lang] ?? $this->description['en'] ?? 'Donate';
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setSuccessUrl(string $url): self
    {
        $this->successUrl = $url;
        return $this;
    }

    public function getSuccessUrl(): string
    {
        return $this->successUrl;
    }

    public function SetOrderId(string $orderId): self
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function setFailUrl(string $url): self
    {
        $this->failUrl = $url;
        return $this;
    }

    public function getFailUrl(): string
    {
        return $this->failUrl;
    }

    public function setWebhookUrl(string $url): self
    {
        $this->webhookUrl = $url;
        return $this;
    }

    public function getWebhookUrl(): string
    {
        return $this->webhookUrl;
    }

    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    public function getConfig(string $key)
    {
        return $this->config[$key] ?? null;
    }

    public function setAllowedIps(array $ips): self
    {
        $this->allowedIps = $ips;
        return $this;
    }

    public function getAllowedIps(): array
    {
        return $this->allowedIps;
    }

    public function isIpAllowed(string $ip): bool
    {
        $allowedIps = $this->getAllowedIps();
        if (empty($allowedIps)) {
            return true; // Если список разрешенных IP пуст, разрешаем все IP
        }
        return in_array($ip, $allowedIps);
    }

}