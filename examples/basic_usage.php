<?php

/**
 * Exemplo básico de uso do SDK Alpa PHP
 */

require __DIR__ . '/../vendor/autoload.php';

use Alpa\AlpaClient;
use Alpa\Utils\Exceptions\AlpaValidationError;
use Alpa\Utils\Exceptions\AlpaError;

$alpa = new AlpaClient(getenv('ALPA_API_KEY') ?: 'sua_api_key_aqui');

try {
    // Criar um link de pagamento
    $link = $alpa->paymentLinks->create([
        'title' => 'Produto Premium',
        'amountCents' => 9900, // R$ 99,00
        'description' => 'Acesso vitalício',
    ]);
    echo "Link criado: {$link['id']}\n";
    echo "Checkout: " . ($link['url'] ?? '-') . "\n";

    // Criar uma transação PIX
    $tx = $alpa->transactions->create([
        'product' => 'Curso Online',
        'amountCents' => 19900,
        'paymentMethod' => 'PIX',
        'clientName' => 'João Silva',
        'clientEmail' => 'joao@example.com',
        'clientDocument' => '12345678900',
    ]);
    echo "Transação: {$tx['id']}\n";
    echo "PIX copia e cola: " . ($tx['pixCopiaECola'] ?? '-') . "\n";

    // Validar cupom
    $validation = $alpa->coupons->validate([
        'code' => 'DESCONTO10',
        'amountCents' => 19900,
    ]);
    if (!empty($validation['valid'])) {
        echo "Desconto: R$ " . ($validation['discountCents'] / 100) . "\n";
    }
} catch (AlpaValidationError $e) {
    echo "Erro de validação: {$e->getMessage()}\n";
} catch (AlpaError $e) {
    echo "Erro {$e->status}: {$e->getMessage()}\n";
}
