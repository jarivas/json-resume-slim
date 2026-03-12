<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_logout_ok(): void
    {
        $headers = $this->getAuthHeaders();
        $response = $this->makeRequest($headers);

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Logged out successfully', $response['message']);
    }

    public function test_logout_invalid_token(): void
    {
        $headers = ['Authorization' => 'invalidtoken'];
        $response = $this->makeRequest($headers);

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Unauthorized', $response['error']);
    }

    private function makeRequest(array $headers): array
    {
        $response = $this->post('/auth/logout', [], $headers);

        return $response['result'];
    }
}