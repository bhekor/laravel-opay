<?php

namespace Bhekor\LaravelOpay\DTOs;

final class RefundResult
{
    /**
     * @param  array{total: int|float, currency: string}|null  $refundAmount
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public readonly string $reference,
        public readonly string $originalReference,
        public readonly string $orderNo,
        public readonly string $originalOrderNo,
        public readonly string $country,
        public readonly ?array $refundAmount,
        public readonly string $orderStatus,
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
            originalReference: (string) ($data['originalReference'] ?? ''),
            orderNo: (string) ($data['orderNo'] ?? ''),
            originalOrderNo: (string) ($data['originalOrderNo'] ?? ''),
            country: (string) ($data['country'] ?? ''),
            refundAmount: is_array($data['refundAmount'] ?? null) ? $data['refundAmount'] : null,
            orderStatus: (string) ($data['orderStatus'] ?? ''),
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
            'originalReference' => $this->originalReference,
            'orderNo' => $this->orderNo,
            'originalOrderNo' => $this->originalOrderNo,
            'country' => $this->country,
            'refundAmount' => $this->refundAmount,
            'orderStatus' => $this->orderStatus,
        ];
    }
}
