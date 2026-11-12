<?php

namespace Bhekor\LaravelOpay\DTOs;

final class CashierCreateResult
{
    /**
     * @param  array{total: int|float, currency: string}|null  $amount
     * @param  array{total: int|float, currency: string}|null  $vat
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public readonly string $reference,
        public readonly string $orderNo,
        public readonly string $cashierUrl,
        public readonly string $status,
        public readonly ?array $amount,
        public readonly ?array $vat,
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
            cashierUrl: (string) ($data['cashierUrl'] ?? ''),
            status: (string) ($data['status'] ?? ''),
            amount: is_array($data['amount'] ?? null) ? $data['amount'] : null,
            vat: is_array($data['vat'] ?? null) ? $data['vat'] : null,
            raw: $raw !== [] ? $raw : $data,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'reference' => $this->reference,
            'orderNo' => $this->orderNo,
            'cashierUrl' => $this->cashierUrl,
            'status' => $this->status,
            'amount' => $this->amount,
            'vat' => $this->vat,
        ];
    }
}
