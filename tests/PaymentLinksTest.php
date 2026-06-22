<?php

namespace Alpa\Tests;

use Alpa\Resources\PaymentLinks;
use PHPUnit\Framework\TestCase;

class PaymentLinksTest extends TestCase
{
    public function testCreateSendsAmountCents(): void
    {
        $http = new FakeHttpClient();
        $http->nextResponse = ['id' => 'lnk_1', 'url' => 'https://checkout.usealpa.com/pay/abc'];
        $res = new PaymentLinks($http);

        $result = $res->create(['title' => 'Produto Premium', 'amountCents' => 9900]);

        $this->assertSame('POST', $http->calls[0]['method']);
        $this->assertSame('/payment-links', $http->calls[0]['endpoint']);
        $this->assertSame(9900, $http->calls[0]['data']['amountCents']);
        $this->assertSame('https://checkout.usealpa.com/pay/abc', $result['url']);
    }

    public function testCreateAcceptsAmountAlias(): void
    {
        $http = new FakeHttpClient();
        $res = new PaymentLinks($http);
        $res->create(['title' => 'Legado', 'amount' => 5000]);
        $this->assertSame(5000, $http->calls[0]['data']['amountCents']);
    }

    public function testCreateThrowsOnShortTitle(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new PaymentLinks(new FakeHttpClient()))->create(['title' => 'ab', 'amountCents' => 9900]);
    }

    public function testCreateThrowsWithoutAmountOrProducts(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new PaymentLinks(new FakeHttpClient()))->create(['title' => 'Sem valor']);
    }

    public function testCheckoutUrlDomain(): void
    {
        $res = new PaymentLinks(new FakeHttpClient());
        $this->assertSame('https://checkout.usealpa.com/pay/meu-slug', $res->getCheckoutUrl('meu-slug'));
    }
}
