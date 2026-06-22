<?php

namespace Alpa;

use Alpa\HttpClient;
use Alpa\Resources\PaymentLinks;
use Alpa\Resources\Transactions;
use Alpa\Resources\Products;
use Alpa\Resources\Clients;
use Alpa\Resources\Coupons;
use Alpa\Resources\Subscriptions;
use Alpa\Resources\Checkouts;
use Alpa\Resources\Offers;
use Alpa\Resources\Withdrawals;
use Alpa\Resources\Wallet;
use Alpa\Utils\Webhooks;

class AlpaClient
{
    private HttpClient $http;

    public PaymentLinks $paymentLinks;
    public Transactions $transactions;
    public Products $products;
    public Clients $clients;
    public Coupons $coupons;
    public Subscriptions $subscriptions;
    public Checkouts $checkouts;
    public Offers $offers;
    public Withdrawals $withdrawals;
    public Wallet $wallet;
    
    public function __construct(
        string $apiKey,
        ?string $baseUrl = null,
        string $version = 'v1',
        int $timeout = 30
    ) {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException('API Key é obrigatória');
        }
        
        $this->http = new HttpClient(
            $apiKey,
            $baseUrl ?? 'https://alpa-sistema-api.onrender.com',
            $version,
            $timeout
        );
        
        // Inicializa recursos
        $this->paymentLinks  = new PaymentLinks($this->http);
        $this->transactions  = new Transactions($this->http);
        $this->products      = new Products($this->http);
        $this->clients       = new Clients($this->http);
        $this->coupons       = new Coupons($this->http);
        $this->subscriptions = new Subscriptions($this->http);
        $this->checkouts     = new Checkouts($this->http);
        $this->offers        = new Offers($this->http);
        $this->withdrawals   = new Withdrawals($this->http);
        $this->wallet        = new Wallet($this->http);
    }
    
    /**
     * Verifica a assinatura de um webhook
     * 
     * @param string $payload Corpo da requisição
     * @param string $signature Assinatura recebida no header
     * @param string $secret Secret da API key
     * @return bool True se a assinatura for válida
     */
    public function verifyWebhookSignature(
        string $payload,
        string $signature,
        string $secret
    ): bool {
        return Webhooks::verify($payload, $signature, $secret);
    }
    
    /**
     * Extrai a assinatura do header da requisição
     * 
     * @param array $headers Headers da requisição
     * @return string|null A assinatura ou null se não encontrada
     */
    public function extractWebhookSignature(array $headers): ?string
    {
        return Webhooks::extractSignature($headers);
    }
}
