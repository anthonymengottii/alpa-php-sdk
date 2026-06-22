<?php

namespace Alpa\Tests;

use Alpa\Resources\Transactions;
use PHPUnit\Framework\TestCase;

class TransactionsTest extends TestCase
{
    public function testCreateSendsFlatClientFields(): void
    {
        $http = new FakeHttpClient();
        $http->nextResponse = ['id' => 'tx_1', 'pixCopiaECola' => '000201...'];
        $res = new Transactions($http);

        $result = $res->create([
            'product' => 'Curso',
            'amountCents' => 19900,
            'paymentMethod' => 'PIX',
            'clientEmail' => 'joao@example.com',
        ]);

        $this->assertSame('/transactions', $http->calls[0]['endpoint']);
        $this->assertSame('joao@example.com', $http->calls[0]['data']['clientEmail']);
        $this->assertSame('000201...', $result['pixCopiaECola']);
    }

    public function testCreateThrowsWithoutClientEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Transactions(new FakeHttpClient()))->create(['product' => 'X', 'amountCents' => 19900]);
    }

    public function testCreateThrowsBelowMinimum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Transactions(new FakeHttpClient()))->create(['product' => 'X', 'amountCents' => 50, 'clientEmail' => 'a@b.com']);
    }

    public function testRefundSendsAmountCents(): void
    {
        $http = new FakeHttpClient();
        $res = new Transactions($http);
        $res->refund('tx_1', 5000);
        $this->assertSame('/transactions/tx_1/refund', $http->calls[0]['endpoint']);
        $this->assertSame(5000, $http->calls[0]['data']['amountCents']);
    }
}
