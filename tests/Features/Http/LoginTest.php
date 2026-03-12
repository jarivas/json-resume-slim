<?php

namespace Tests\Features\Http;

use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login_ok(): void
    {
        $response = $this->makeRequest([
            'username' => env('USERNAME'),
            'password' => env('PASSWORD'),
        ]);

        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('expires_at', $response);
    }

    public function test_login_invalid_credentials(): void
    {
        $response = $this->makeRequest([
            'username' => 'admin',
            'password' => 'wrongpassword',
        ]);

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Invalid credentials', $response['error']);
    }

    private function makeRequest(array $data = []): array
    {
        $response = $this->post('/auth/login', $data);

        return $response['result'];
    }
}