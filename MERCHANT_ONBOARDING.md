# OPay Merchant Onboarding Checklist

Local package development does **not** require Go Live approval.

## Confirmed

- [x] Merchant account created; KYC submitted (awaiting Go Live).
- [x] Staging API: `https://testapi.opaycheckout.com`
- [x] Live API: `https://liveapi.opaycheckout.com`
- [x] Auth: Public Key for cashier create; HMAC-SHA512 secret for status/refund/transaction create; HMAC-SHA3-512 for callbacks.
- [x] Preferred pay method: `OpayWalletNg` + `evokeOpay=true` (not QR-first `OpayWalletNgQR`).
- [x] Published Standard Nigeria local fee: **1.5% capped at ₦2,000** ([pricing](https://opaycheckout.com/pricing.html)).

## Still required from OPay / dashboard

- [ ] Sandbox Merchant ID, Public Key, Secret Key.
- [ ] Production credentials after Go Live.
- [ ] Confirmation that **`OpayWalletNg`** is enabled (not QR-only).
- [ ] Confirmation that **`evokeOpay`** works for web and in-app WebView.
- [ ] Webhook URL in dashboard (or per-request `callbackUrl`).
- [ ] Standard vs Customized terms; settlement cycle; limits; refund SLA.
- [ ] Confirm callback `amount` unit against sandbox fixtures.

## Host callback URL

`POST {APP_URL}/api/v1/webhooks/opay/transaction-completed`

Store credentials only in `.env`. Never commit real keys.
