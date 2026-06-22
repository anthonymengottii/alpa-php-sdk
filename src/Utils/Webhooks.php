<?php

namespace Alpa\Utils;

class Webhooks
{
    /**
     * Tipos de eventos de webhook assináveis na Alpa.
     * Conjunto alinhado ao backend. Envelope: { id, type, data, timestamp, subscription }.
     */
    public const EVENT_TRANSACTION_CREATED       = 'transaction.created';
    public const EVENT_TRANSACTION_UPDATED       = 'transaction.updated';
    public const EVENT_TRANSACTION_COMPLETED     = 'transaction.completed';
    public const EVENT_TRANSACTION_FAILED        = 'transaction.failed';
    public const EVENT_TRANSACTION_REFUNDED      = 'transaction.refunded';
    public const EVENT_PAYMENT_LINK_CREATED      = 'payment_link.created';
    public const EVENT_PAYMENT_LINK_UPDATED      = 'payment_link.updated';
    public const EVENT_BALANCE_UPDATED           = 'balance.updated';
    public const EVENT_SUBSCRIPTION_CANCELLED    = 'subscription.cancelled';
    public const EVENT_KYC_SUBMITTED             = 'kyc.submitted';
    public const EVENT_KYC_APPROVED              = 'kyc.approved';
    public const EVENT_KYC_REJECTED              = 'kyc.rejected';
    public const EVENT_ADVANCE_CREATED           = 'advance.created';
    public const EVENT_ADVANCE_APPROVED          = 'advance.approved';
    public const EVENT_ADVANCE_REJECTED          = 'advance.rejected';
    public const EVENT_WITHDRAWAL_REQUESTED      = 'withdrawal.requested';
    public const EVENT_WITHDRAWAL_COMPLETED      = 'withdrawal.completed';
    public const EVENT_WITHDRAWAL_FAILED         = 'withdrawal.failed';

    /**
     * Verifica a assinatura de um webhook usando HMAC SHA256 (hex).
     *
     * A Alpa envia a assinatura em hexadecimal no header X-Webhook-Signature
     * (prefixada por "sha256=").
     *
     * @param string $payload Corpo bruto da requisição
     * @param string $signature Assinatura recebida no header (com ou sem prefixo "sha256=")
     * @param string $secret Secret da assinatura de webhook
     * @return bool True se a assinatura for válida
     */
    public static function verify(string $payload, string $signature, string $secret): bool
    {
        if ($payload === '' || empty($signature) || empty($secret)) {
            return false;
        }

        $normalized = str_replace('sha256=', '', $signature);
        if ($normalized === '') {
            return false;
        }

        try {
            $expectedSignature = hash_hmac('sha256', $payload, $secret);
            return hash_equals($expectedSignature, $normalized);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Extrai a assinatura do header da requisição.
     *
     * Header canônico: x-webhook-signature. Nomes legados são aceitos como fallback.
     *
     * @param array $headers Headers da requisição
     * @return string|null A assinatura (sem prefixo "sha256=") ou null
     */
    public static function extractSignature(array $headers): ?string
    {
        $signatureHeader =
            $headers['x-webhook-signature'] ??
            $headers['X-Webhook-Signature'] ??
            $headers['x-alpa-signature'] ??
            $headers['x-upay-signature'] ??
            $headers['signature'] ??
            null;

        if (!$signatureHeader) {
            return null;
        }

        if (is_array($signatureHeader)) {
            $signatureHeader = $signatureHeader[0] ?? null;
        }

        if (!$signatureHeader) {
            return null;
        }

        // Remove prefixo "sha256=" se existir
        return str_replace('sha256=', '', $signatureHeader);
    }
}
