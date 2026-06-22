<?php

namespace Alpa\Resources;

use Alpa\HttpClient;

class Offers
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    // ─── Order Bumps ─────────────────────────────────────────────────────────

    /**
     * Lista order bumps de um checkout
     */
    public function listOrderBumps(string $checkoutId): array
    {
        if (empty($checkoutId)) {
            throw new \InvalidArgumentException('checkoutId é obrigatório');
        }
        $response = $this->http->get("/checkouts/{$checkoutId}/order-bumps");
        return $response['orderBumps'] ?? $response['data'] ?? (array) $response;
    }

    /**
     * Cria um order bump em um checkout
     */
    public function createOrderBump(string $checkoutId, array $data): array
    {
        if (empty($checkoutId)) {
            throw new \InvalidArgumentException('checkoutId é obrigatório');
        }
        if (empty($data['productId'])) {
            throw new \InvalidArgumentException('productId é obrigatório');
        }
        return $this->http->post("/checkouts/{$checkoutId}/order-bumps", $data);
    }

    /**
     * Atualiza um order bump
     */
    public function updateOrderBump(string $checkoutId, string $bumpId, array $data): array
    {
        if (empty($checkoutId)) {
            throw new \InvalidArgumentException('checkoutId é obrigatório');
        }
        if (empty($bumpId)) {
            throw new \InvalidArgumentException('bumpId é obrigatório');
        }
        return $this->http->request('PUT', "/checkouts/{$checkoutId}/order-bumps/{$bumpId}", $data);
    }

    /**
     * Remove um order bump
     */
    public function deleteOrderBump(string $checkoutId, string $bumpId): void
    {
        if (empty($checkoutId)) {
            throw new \InvalidArgumentException('checkoutId é obrigatório');
        }
        if (empty($bumpId)) {
            throw new \InvalidArgumentException('bumpId é obrigatório');
        }
        $this->http->delete("/checkouts/{$checkoutId}/order-bumps/{$bumpId}");
    }

    // ─── Upsell ──────────────────────────────────────────────────────────────

    /**
     * Obtém o upsell de um checkout
     */
    public function getUpsell(string $checkoutId): ?array
    {
        if (empty($checkoutId)) {
            throw new \InvalidArgumentException('checkoutId é obrigatório');
        }
        $response = $this->http->get("/checkouts/{$checkoutId}/upsell");
        return $response ?: null;
    }

    /**
     * Cria ou atualiza o upsell de um checkout
     */
    public function upsertUpsell(string $checkoutId, array $data): array
    {
        if (empty($checkoutId)) {
            throw new \InvalidArgumentException('checkoutId é obrigatório');
        }
        if (empty($data['productId'])) {
            throw new \InvalidArgumentException('productId é obrigatório');
        }
        return $this->http->post("/checkouts/{$checkoutId}/upsell", $data);
    }

    /**
     * Remove o upsell de um checkout
     */
    public function deleteUpsell(string $checkoutId): void
    {
        if (empty($checkoutId)) {
            throw new \InvalidArgumentException('checkoutId é obrigatório');
        }
        $this->http->delete("/checkouts/{$checkoutId}/upsell");
    }

    // ─── Downsell ─────────────────────────────────────────────────────────────

    /**
     * Cria ou atualiza o downsell de um checkout
     */
    public function upsertDownsell(string $checkoutId, array $data): array
    {
        if (empty($checkoutId)) {
            throw new \InvalidArgumentException('checkoutId é obrigatório');
        }
        if (empty($data['productId'])) {
            throw new \InvalidArgumentException('productId é obrigatório');
        }
        return $this->http->post("/checkouts/{$checkoutId}/upsell/downsell", $data);
    }

    /**
     * Remove o downsell de um checkout
     */
    public function deleteDownsell(string $checkoutId): void
    {
        if (empty($checkoutId)) {
            throw new \InvalidArgumentException('checkoutId é obrigatório');
        }
        $this->http->delete("/checkouts/{$checkoutId}/upsell/downsell");
    }
}
