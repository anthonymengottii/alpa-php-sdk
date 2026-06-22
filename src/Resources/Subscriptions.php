<?php

namespace Alpa\Resources;

use Alpa\HttpClient;

class Subscriptions
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Lista assinaturas
     */
    public function list(?int $page = null, ?int $limit = null, ?string $status = null): array
    {
        $params = array_filter(
            ['page' => $page, 'limit' => $limit, 'status' => $status],
            fn($v) => $v !== null
        );
        $response = $this->http->get('/subscriptions', $params ?: null);
        return [
            'data'       => $response['subscriptions'] ?? $response['data'] ?? [],
            'pagination' => $response['pagination'] ?? ['total' => 0, 'page' => 1, 'limit' => 10],
        ];
    }

    /**
     * Cria uma assinatura
     */
    public function create(array $data): array
    {
        if (empty($data['planId'])) {
            throw new \InvalidArgumentException('planId é obrigatório');
        }
        if (isset($data['client']) && empty($data['client']['email'])) {
            throw new \InvalidArgumentException('Email do cliente é obrigatório');
        }
        return $this->http->post('/subscriptions', $data);
    }

    /**
     * Cancela uma assinatura
     */
    public function cancel(string $id): array
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('ID é obrigatório');
        }
        return $this->http->request('PATCH', "/subscriptions/{$id}/cancel");
    }

    /**
     * Pausa uma assinatura
     */
    public function pause(string $id): array
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('ID é obrigatório');
        }
        return $this->http->request('PATCH', "/subscriptions/{$id}/pause");
    }

    /**
     * Retoma uma assinatura pausada
     */
    public function resume(string $id): array
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('ID é obrigatório');
        }
        return $this->http->request('PATCH', "/subscriptions/{$id}/resume");
    }

    /**
     * Tenta reprocessar o pagamento de uma assinatura em atraso
     */
    public function retry(string $id): array
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('ID é obrigatório');
        }
        return $this->http->post("/subscriptions/{$id}/retry");
    }

    /**
     * Retorna métricas de assinaturas (MRR, churn, totais)
     */
    public function getMetrics(): array
    {
        return $this->http->get('/subscriptions/metrics');
    }
}
