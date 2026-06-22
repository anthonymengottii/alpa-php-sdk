<?php

namespace Alpa\Resources;

use Alpa\HttpClient;

class Withdrawals
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Lista saques
     */
    public function list(?int $page = null, ?int $limit = null, ?string $status = null): array
    {
        $params = array_filter(
            ['page' => $page, 'limit' => $limit, 'status' => $status],
            fn($v) => $v !== null
        );
        $response = $this->http->get('/withdraws', $params ?: null);
        return [
            'data'       => $response['withdraws'] ?? $response['data'] ?? [],
            'pagination' => $response['pagination'] ?? ['total' => 0, 'page' => 1, 'limit' => 10],
        ];
    }

    /**
     * Obtém um saque por ID
     */
    public function get(string $id): array
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('ID é obrigatório');
        }
        return $this->http->get("/withdraws/{$id}");
    }

    /**
     * Retorna o saldo disponível para saque
     */
    public function getBalance(): array
    {
        return $this->http->get('/withdraws/balance');
    }

    /**
     * Cria uma solicitação de saque
     */
    public function create(array $data): array
    {
        if (empty($data['amountCents']) || $data['amountCents'] < 100) {
            throw new \InvalidArgumentException('Valor mínimo é R$ 1,00 (100 centavos)');
        }
        if (empty($data['pixKey']) && empty($data['bankAccount'])) {
            throw new \InvalidArgumentException('pixKey ou bankAccount é obrigatório');
        }
        return $this->http->post('/withdraws', $data);
    }

    /**
     * Cancela um saque pendente
     */
    public function cancel(string $id): array
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('ID é obrigatório');
        }
        return $this->http->post("/withdraws/{$id}/cancel");
    }
}
