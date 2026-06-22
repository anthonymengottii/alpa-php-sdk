<?php

namespace Alpa\Tests;

use Alpa\HttpClient;

/**
 * Test double do HttpClient — registra chamadas e devolve respostas pré-definidas.
 */
class FakeHttpClient extends HttpClient
{
    /** @var array<int, array{method:string, endpoint:string, data:mixed}> */
    public array $calls = [];
    public array $nextResponse = [];

    public function __construct()
    {
        parent::__construct('test-key', 'https://test.example.com', 'v1', 30);
    }

    public function request(string $method, string $endpoint, ?array $data = null, ?array $params = null): array
    {
        $this->calls[] = ['method' => $method, 'endpoint' => $endpoint, 'data' => $data ?? $params];
        return $this->nextResponse;
    }

    public function get(string $endpoint, ?array $params = null): array
    {
        return $this->request('GET', $endpoint, null, $params);
    }

    public function post(string $endpoint, ?array $data = null): array
    {
        return $this->request('POST', $endpoint, $data);
    }

    public function patch(string $endpoint, ?array $data = null): array
    {
        return $this->request('PATCH', $endpoint, $data);
    }

    public function delete(string $endpoint): array
    {
        return $this->request('DELETE', $endpoint);
    }
}
