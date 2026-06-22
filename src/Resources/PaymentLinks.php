<?php

namespace Alpa\Resources;

use Alpa\HttpClient;

class PaymentLinks
{
    private HttpClient $http;
    
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }
    
    public function create(array $data): array
    {
        // Validação básica
        if (!isset($data['title']) || !is_string($data['title']) || strlen(trim($data['title'])) < 3) {
            throw new \InvalidArgumentException('Título deve ter pelo menos 3 caracteres');
        }
        
        // amountCents é o canônico; "amount" é alias deprecado.
        $amountCents = $data['amountCents'] ?? $data['amount'] ?? null;

        if (empty($amountCents) && empty($data['products'])) {
            throw new \InvalidArgumentException('É necessário fornecer amountCents ou products');
        }

        if (!empty($amountCents) && $amountCents < 100) {
            throw new \InvalidArgumentException('Valor mínimo é R$ 1,00 (100 centavos)');
        }

        $requestData = [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'amountCents' => $amountCents,
            'products' => $data['products'] ?? null,
            'currency' => $data['currency'] ?? 'BRL',
            'expiresAt' => $data['expiresAt'] ?? null,
            'redirectUrl' => $data['redirectUrl'] ?? null,
            'settings' => $data['settings'] ?? null,
            'status' => $data['status'] ?? 'ACTIVE',
            'metaPixelCode' => $data['metaPixelCode'] ?? null,
            'stockQuantity' => $data['stockQuantity'] ?? null,
            'stockEnabled' => $data['stockEnabled'] ?? null,
        ];
        
        // Remove valores null
        $requestData = array_filter($requestData, fn($v) => $v !== null);
        
        $response = $this->http->post('/payment-links', $requestData);
        return $response['paymentLink'] ?? $response['data'] ?? $response;
    }
    
    public function list(?array $params = null): array
    {
        $response = $this->http->get('/payment-links', $params);
        return [
            'data' => $response['paymentLinks'] ?? $response['data'] ?? [],
            'pagination' => $response['pagination'] ?? ['total' => 0, 'page' => 1, 'limit' => 10],
        ];
    }
    
    public function get(string $id): array
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('ID é obrigatório');
        }
        
        $encodedId = rawurlencode($id);
        $response = $this->http->get("/payment-links/{$encodedId}");
        return $response['paymentLink'] ?? $response['data'] ?? $response;
    }

    public function getBySlug(string $slug): array
    {
        if (empty($slug)) {
            throw new \InvalidArgumentException('Slug é obrigatório');
        }

        $encodedSlug = rawurlencode($slug);
        $response = $this->http->get("/payment-links/slug/{$encodedSlug}");
        return $response['paymentLink'] ?? $response['data'] ?? $response;
    }
    
    public function update(string $id, array $data): array
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('ID é obrigatório');
        }
        
        $updateData = [];
        
        // Validação de title (mesma lógica do create)
        if (isset($data['title'])) {
            if (!is_string($data['title']) || strlen(trim($data['title'])) < 3) {
                throw new \InvalidArgumentException('Título deve ter pelo menos 3 caracteres');
            }
            $updateData['title'] = $data['title'];
        }
        
        // Validação de description
        if (isset($data['description'])) {
            if (!is_string($data['description']) || strlen($data['description']) > 5000) {
                throw new \InvalidArgumentException('Descrição deve ser uma string com no máximo 5000 caracteres');
            }
            $updateData['description'] = $data['description'];
        }
        
        // Validação de amountCents (aceita alias deprecado "amount")
        if (isset($data['amountCents']) || isset($data['amount'])) {
            $raw = $data['amountCents'] ?? $data['amount'];
            if (!is_numeric($raw) || (int)$raw < 0) {
                throw new \InvalidArgumentException('Valor deve ser um número não negativo');
            }
            $amountCents = (int)$raw;
            if ($amountCents > 0 && $amountCents < 100) {
                throw new \InvalidArgumentException('Valor mínimo é R$ 1,00 (100 centavos)');
            }
            $updateData['amountCents'] = $amountCents;
        }
        
        // Validação de status
        if (isset($data['status'])) {
            $updateData['status'] = $data['status'];
        }
        
        // Validação de expiresAt
        if (isset($data['expiresAt'])) {
            $updateData['expiresAt'] = $data['expiresAt'];
        }
        
        // Validação de redirectUrl
        if (isset($data['redirectUrl'])) {
            if (!is_string($data['redirectUrl']) || !filter_var($data['redirectUrl'], FILTER_VALIDATE_URL)) {
                throw new \InvalidArgumentException('redirectUrl deve ser uma URL válida');
            }
            $updateData['redirectUrl'] = $data['redirectUrl'];
        }
        
        // Validação de settings
        if (isset($data['settings'])) {
            if (!is_array($data['settings'])) {
                throw new \InvalidArgumentException('settings deve ser um array');
            }
            $updateData['settings'] = $data['settings'];
        }
        
        $encodedId = rawurlencode($id);
        return $this->http->patch("/payment-links/{$encodedId}", $updateData);
    }
    
    public function delete(string $id): void
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('ID é obrigatório');
        }
        
        $encodedId = rawurlencode($id);
        $this->http->delete("/payment-links/{$encodedId}");
    }
    
    public function getCheckoutUrl(string $slug, ?string $baseUrl = null): string
    {
        $checkoutBase = $baseUrl ?? 'https://checkout.usealpa.com/pay';
        $encodedSlug = rawurlencode($slug);
        return "{$checkoutBase}/{$encodedSlug}";
    }
}
