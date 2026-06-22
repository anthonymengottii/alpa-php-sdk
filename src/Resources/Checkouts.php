<?php

namespace Alpa\Resources;

use Alpa\HttpClient;

class Checkouts
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Lista checkouts
     */
    public function list(?int $page = null, ?int $limit = null): array
    {
        $params = array_filter(
            ['page' => $page, 'limit' => $limit],
            fn($v) => $v !== null
        );
        $response = $this->http->get('/checkouts', $params ?: null);
        return [
            'data'       => $response['checkouts'] ?? $response['data'] ?? [],
            'pagination' => $response['pagination'] ?? ['total' => 0, 'page' => 1, 'limit' => 10],
        ];
    }

    /**
     * Cria um checkout
     */
    public function create(array $data): array
    {
        if (empty(trim($data['name'] ?? ''))) {
            throw new \InvalidArgumentException('Nome do checkout é obrigatório');
        }
        return $this->http->post('/checkouts', $data);
    }

    /**
     * Obtém um checkout por ID
     */
    public function get(string $id): array
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('ID é obrigatório');
        }
        return $this->http->get("/checkouts/{$id}");
    }

    /**
     * Atualiza um checkout
     */
    public function update(string $id, array $data): array
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('ID é obrigatório');
        }
        return $this->http->request('PUT', "/checkouts/{$id}", $data);
    }

    /**
     * Deleta um checkout
     */
    public function delete(string $id): void
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('ID é obrigatório');
        }
        $this->http->delete("/checkouts/{$id}");
    }
}
