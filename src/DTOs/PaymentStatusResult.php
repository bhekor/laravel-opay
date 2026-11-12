<?php

namespace Bhekor\LaravelOpay\DTOs;

final class PaymentStatusResult
{
    public const STATUS_INITIAL = 'INITIAL';

    public const STATUS_PENDING = 'PENDING';

    public const STATUS_SUCCESS = 'SUCCESS';

    public const STATUS_FAIL = 'FAIL';

    public const STATUS_CLOSE = 'CLOSE';

    /**
     * @param  array{total: int|float, currency: string}|null  $amount
     * @param  array{total: int|float, currency: string}|null  $vat
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public readonly string $reference,
        public readonly string $orderNo,
        public readonly string $status,
        public readonly ?array $amount,
        public readonly ?array $vat,
        public readonly ?string $failureCode,
        public readonly ?string $failureReason,
        public readonly mixed $createTime,
        public readonly array $raw,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $raw
     */
    public static function fromApiData(array $data, array $raw = []): self
    {
        return new self(
            reference: (string) ($data['reference'] ?? ''),
            orderNo: (string) ($data['orderNo'] ?? ''),
            status: strtoupper((string) ($data['status'] ?? '')),
            amount: is_array($data['amount'] ?? null) ? $data['amount'] : null,
            vat: is_array($data['vat'] ?? null) ? $data['vat'] : null,
            failureCode: isset($data['failureCode']) ? (string) $data['failureCode'] : null,
            failureReason: isset($data['failureReason']) ? (string) $data['failureReason'] : null,
            createTime: $data['createTime'] ?? null,
            raw: $raw !== [] ? $raw : $data,
        );
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isPending(): bool
    {
        return in_array($this->status, [self::STATUS_INITIAL, self::STATUS_PENDING], true);
    }

    public function isFailed(): bool
    {
        return in_array($this->status, [self::STATUS_FAIL, self::STATUS_CLOSE], true);
    }

    /**
     * Amount in major units (e.g. NGN) when API returns minor units.
     */
    public function amountMajor(): ?float
    {
        if ($this->amount === null || ! isset($this->amount['total'])) {
            return null;
        }

        return round(((float) $this->amount['total']) / 100, 2);
    }

    public function currency(): ?string
    {
        return isset($this->amount['currency']) ? (string) $this->amount['currency'] : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'reference' => $this->reference,
            'orderNo' => $this->orderNo,
            'status' => $this->status,
            'amount' => $this->amount,
            'vat' => $this->vat,
            'failureCode' => $this->failureCode,
            'failureReason' => $this->failureReason,
            'createTime' => $this->createTime,
        ];
    }
}
