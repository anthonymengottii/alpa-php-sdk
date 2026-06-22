<?php

namespace Alpa\Tests;

use Alpa\Utils\Webhooks;
use PHPUnit\Framework\TestCase;

class WebhooksTest extends TestCase
{
    public function testVerifyValidWithPrefix(): void
    {
        $secret = 'test-secret';
        $payload = '{"type":"transaction.completed"}';
        $sig = hash_hmac('sha256', $payload, $secret);
        $this->assertTrue(Webhooks::verify($payload, "sha256={$sig}", $secret));
    }

    public function testVerifyValidWithoutPrefix(): void
    {
        $secret = 'test-secret';
        $payload = '{"a":1}';
        $sig = hash_hmac('sha256', $payload, $secret);
        $this->assertTrue(Webhooks::verify($payload, $sig, $secret));
    }

    public function testVerifyInvalid(): void
    {
        $this->assertFalse(Webhooks::verify('{}', 'bad', 'secret'));
    }

    public function testVerifyEmptyInputs(): void
    {
        $this->assertFalse(Webhooks::verify('', 'sig', 'secret'));
        $this->assertFalse(Webhooks::verify('{}', '', 'secret'));
        $this->assertFalse(Webhooks::verify('{}', 'sig', ''));
    }

    public function testExtractCanonicalHeader(): void
    {
        $this->assertSame('abc', Webhooks::extractSignature(['x-webhook-signature' => 'sha256=abc']));
    }

    public function testExtractLegacyFallback(): void
    {
        $this->assertSame('legacy', Webhooks::extractSignature(['x-upay-signature' => 'legacy']));
    }

    public function testExtractReturnsNullWhenMissing(): void
    {
        $this->assertNull(Webhooks::extractSignature(['content-type' => 'application/json']));
    }

    public function testEventConstantsAligned(): void
    {
        $this->assertSame('transaction.completed', Webhooks::EVENT_TRANSACTION_COMPLETED);
        $this->assertSame('balance.updated', Webhooks::EVENT_BALANCE_UPDATED);
        $this->assertFalse(defined(Webhooks::class . '::EVENT_TRANSACTION_PAID'));
    }
}
