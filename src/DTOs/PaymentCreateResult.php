<?php

namespace Bhekor\LaravelOpay\DTOs;

final class PaymentCreateResult
{
    /**
     * @param  array{actionType?: string, qrCode?: string}|null  $nextAction
     * @param  array{total: int|float, currency: string}|null  $amount
     * @param  array{total: int|float, currency: string}|null  $vat
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public readonly string $reference,
        public readonly string $orderNo,
        public readonly string $status,
        public readonly ?array $nextAction,
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
            status: (string) ($data['status'] ?? ''),
            nextAction: is_array($data['nextAction'] ?? null) ? $data['nextAction'] : null,
            amount: is_array($data['amount'] ?? null) ? $data['amount'] : null,
            vat: is_array($data['vat'] ?? null) ? $data['vat'] : null,
            raw: $raw !== [] ? $raw : $data,
        );
    }

    public function isQrScan(): bool
    {
        return strtoupper((string) ($this->nextAction['actionType'] ?? '')) === 'SCAN_QR_CODE';
    }

    public function qrCode(): ?string
    {
        $qr = $this->nextAction['qrCode'] ?? null;

        return is_string($qr) && $qr !== '' ? $qr : null;
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
            'nextAction' => $this->nextAction,
            'amount' => $this->amount,
            'vat' => $this->vat,
        ];
    }
}
