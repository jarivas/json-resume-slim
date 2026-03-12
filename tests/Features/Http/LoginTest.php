<?php

namespace Tests\Features\Http;

use Tests\TestCase;
use App\Model\Tokens;

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

        $token = Tokens::first([
            ['token', '=', $response['token']],
        ]);

        $this->assertNotFalse($token);
        $this->assertEquals($response['expires_at'], $token->expires_at);
    }

    public function test_login_invalid_credentials(): void
    {
        $response = $this->makeRequest([
            'username' => 'admin',
            'password' => 'wrongpassword',
        ]);

        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('404 Not Found', $response['message']);
    }

    private function makeRequest(array $data = []): array
    {
        $response = $this->post('/auth/login', $data);

        return $response['result'];
    }
}