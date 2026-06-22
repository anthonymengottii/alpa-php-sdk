<?php

namespace Alpa\Resources;

use Alpa\HttpClient;

class Wallet
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Retorna o resumo da carteira (saldo, recebido, sacado)
     */
    public function getSummary(): array
    {
        return $this->http->get('/wallet/summary');
    }

    /**
     * Retorna o extrato da carteira com paginação
     *
     * @param int|null    $page       Número da página
     * @param int|null    $limit      Limite de itens por página
     * @param string|null $startDate  Data inicial (ISO 8601)
     * @param string|null $endDate    Data final (ISO 8601)
     */
    public function getStatement(
        ?int $page = null,
        ?int $limit = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $params = array_filter(
            [
                'page'      => $page,
                'limit'     => $limit,
                'startDate' => $startDate,
                'endDate'   => $endDate,
            ],
            fn($v) => $v !== null
        );
        $response = $this->http->get('/wallet/statement', $params ?: null);
        return [
            'data'       => $response['entries'] ?? $response['data'] ?? [],
            'pagination' => $response['pagination'] ?? ['total' => 0, 'page' => 1, 'limit' => 10],
        ];
    }
}
