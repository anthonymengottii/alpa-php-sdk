<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="logo/light.png">
    <source media="(prefers-color-scheme: light)" srcset="logo/dark.png">
    <img src="logo/dark.png" alt="Alpa" height="60">
  </picture>
</p>

# Alpa PHP SDK

SDK oficial da Alpa para PHP. Integre PIX, cartão de crédito e boleto. Compatível com PHP 8.1+, sem dependências além de cURL e JSON.

## 📦 Instalação

O SDK ainda não está publicado no Packagist. Instale via repositório VCS:

```bash
composer config repositories.alpa vcs https://github.com/anthonymengottii/alpa-php-sdk
composer require alpa/alpa-php:dev-main
```

## 🚀 Início rápido

```php
use Alpa\AlpaClient;

$alpa = new AlpaClient($_ENV['ALPA_API_KEY']);
```

> O ambiente (desenvolvimento ou produção) é determinado pela **chave** usada. O mesmo `baseUrl` (`https://alpa-sistema-api.onrender.com`) atende ambos.

### Criar um Link de Pagamento

```php
$link = $alpa->paymentLinks->create([
    'title'       => 'Produto Premium',
    'amountCents' => 9900, // R$ 99,00
    'description' => 'Acesso vitalício',
]);

echo 'Checkout: ' . $alpa->paymentLinks->getCheckoutUrl($link['slug']); // https://checkout.usealpa.com/pay/abc123
```

### Criar uma Transação PIX

```php
$tx = $alpa->transactions->create([
    'product'        => 'Curso PHP',
    'amountCents'    => 19900,
    'paymentMethod'  => 'PIX',
    'clientName'     => 'João Silva',
    'clientEmail'    => 'joao@example.com',
    'clientDocument' => '12345678900',
]);

echo 'PIX copia e cola: ' . $tx['pixCopiaECola'];
```

### Validar Cupom

```php
$result = $alpa->coupons->validate([
    'code'        => 'DESCONTO10',
    'amountCents' => 19900,
]);

if ($result['valid']) {
    echo 'Desconto: R$ ' . ($result['discountCents'] / 100);
}
```

## 🔔 Webhooks

A Alpa assina cada webhook com **HMAC-SHA256 (hex)** no header `X-Webhook-Signature`. O envelope tem o formato `{ id, type, data, timestamp, subscription }`.

```php
$signature = $alpa->extractWebhookSignature(getallheaders()); // lê x-webhook-signature
$payload   = file_get_contents('php://input');
$secret    = $_ENV['ALPA_WEBHOOK_SECRET'];

if (!$signature || !$alpa->verifyWebhookSignature($payload, $signature, $secret)) {
    http_response_code(401);
    exit('Assinatura inválida');
}

$event = json_decode($payload, true);
if ($event['type'] === 'transaction.completed') {
    // liberar produto / enviar email
}
```

### Eventos disponíveis

`transaction.created`, `transaction.updated`, `transaction.completed`, `transaction.failed`, `transaction.refunded`, `payment_link.created`, `payment_link.updated`, `balance.updated`, `subscription.cancelled`, `kyc.submitted`, `kyc.approved`, `kyc.rejected`, `advance.created`, `advance.approved`, `advance.rejected`, `withdrawal.requested`, `withdrawal.completed`, `withdrawal.failed`.

Constantes em `Alpa\Utils\Webhooks::EVENT_*`.

## 🔧 Configuração

```php
$alpa = new AlpaClient(
    apiKey:  'sua_api_key',                              // Obrigatório
    baseUrl: 'https://alpa-sistema-api.onrender.com',    // Opcional
    version: 'v1',                                       // Opcional
    timeout: 30                                          // Opcional (segundos)
);
```

## 🛠️ Tratamento de Erros

```php
use Alpa\Utils\Exceptions\AlpaValidationError;
use Alpa\Utils\Exceptions\AlpaError;

try {
    $alpa->paymentLinks->create(['title' => 'Test', 'amountCents' => 9900]);
} catch (AlpaValidationError $e) {
    echo 'Erro de validação: ' . $e->getMessage();
} catch (AlpaError $e) {
    echo "Erro {$e->status}: " . $e->getMessage();
}
```

## 📚 Recursos disponíveis

`paymentLinks`, `transactions`, `products`, `coupons`, `clients`, `subscriptions`, `checkouts`, `offers`, `withdrawals`, `wallet`.

## 🧪 Testes

```bash
composer install
php vendor/bin/phpunit
```

## 🔗 Links úteis

- [Documentação](https://docs.usealpa.com)
- [Dashboard](https://app.usealpa.com)
- [Suporte](mailto:suporte@usealpa.com)
- [Repositório](https://github.com/anthonymengottii/alpa-php-sdk)

## 📝 Licença

MIT
