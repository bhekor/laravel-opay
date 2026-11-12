<?php

namespace Bhekor\LaravelOpay\DTOs;

final class CallbackPayload
{
    /**
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public readonly string $reference,
        public readonly string $transactionId,
        public readonly string $status,
        public readonly string $amount,
        public readonly string $currency,
        public readonly bool $refunded,
        public readonly string $timestamp,
        public readonly string $token,
        public readonly ?string $instrumentType,
        public readonly ?string $fee,
        public readonly ?string $feeCurrency,
        public readonly ?string $displayedFailure,
        public readonly ?string $channel,
        public readonly ?string $country,
        public readonly array $raw,
    ) {}

    /**
     * @param  array<string, mixed>  $callbackBody
     */
    public static function fromCallbackBody(array $callbackBody): self
    {
        $payload = is_array($callbackBody['payload'] ?? null) ? $callbackBody['payload'] : [];

        return new self(
            reference: (string) ($payload['reference'] ?? ''),
            transactionId: (string) ($payload['transactionId'] ?? ''),
            status: strtoupper((string) ($payload['status'] ?? '')),
            amount: (string) ($payload['amount'] ?? ''),
            currency: (string) ($payload['currency'] ?? ''),
            refunded: (bool) ($payload['refunded'] ?? false),
            timestamp: (string) ($payload['timestamp'] ?? ''),
            token: (string) ($payload['token'] ?? ''),
            instrumentType: isset($payload['instrumentType']) ? (string) $payload['instrumentType'] : null,
            fee: isset($payload['fee']) ? (string) $payload['fee'] : null,
            feeCurrency: isset($payload['feeCurrency']) ? (string) $payload['feeCurrency'] : null,
            displayedFailure: isset($payload['displayedFailure']) ? (string) $payload['displayedFailure'] : null,
            channel: isset($payload['channel']) ? (string) $payload['channel'] : null,
            country: isset($payload['country']) ? (string) $payload['country'] : null,
            raw: $callbackBody,
        );
    }

    public function isSuccessful(): bool
    {
        return in_array($this->status, ['SUCCESS', 'SUCCESSFUL'], true);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'reference' => $this->reference,
            'transactionId' => $this->transactionId,
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'refunded' => $this->refunded,
            'timestamp' => $this->timestamp,
            'token' => $this->token,
            'instrumentType' => $this->instrumentType,
            'fee' => $this->fee,
            'feeCurrency' => $this->feeCurrency,
            'displayedFailure' => $this->displayedFailure,
            'channel' => $this->channel,
            'country' => $this->country,
        ];
    }
}
