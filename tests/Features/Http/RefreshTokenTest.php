<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class RefreshTokenTest extends TestCase
{
    public function test_refresh_token_ok(): void
    {
        $headers = $this->getAuthHeaders();
        $response = $this->makeRequest($headers);

        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('expires_at', $response);
    }

    public function test_refresh_token_invalid_token(): void
    {
        $headers = ['Authorization' => 'invalidtoken'];
        $response = $this->makeRequest($headers);

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Unauthorized', $response['error']);
    }

    public function test_refresh_token_missing_token(): void
    {
        $response = $this->makeRequest([]);

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Unauthorized', $response['error']);
    }

    private function makeRequest(array $headers): array
    {
        $response = $this->post('/auth/refresh-token', [], $headers);

        return $response['result'];
    }
}