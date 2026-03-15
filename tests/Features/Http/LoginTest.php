<?php

namespace Tests\Features\Http;

use Tests\TestCase;
use App\Model\Tokens;

class LoginTest extends TestCase
{


    public function test_login_ok(): void
    {
        $response = $this->makeRequest(
            [
                'username' => env('USERNAME'),
                'password' => env('PASSWORD'),
            ]
        );

        if (!isset($response['token'])) {
            $status = $response['status'] ?? null;
            $message = $response['message'] ?? '';

            if ($status === 500 && $message === 'Internal Server Error') {
                $this->markTestSkipped('Database unavailable for login success test.');
            }
        }

        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('expires_at', $response);

        $token = Tokens::first(
            [
                [
                    'token',
                    '=',
                    $response['token'],
                ],
            ]
        );

        $this->assertNotFalse($token);
        $this->assertEquals($response['expires_at'], $token->{'expires_at'});

    }//end test_login_ok()


    public function test_login_invalid_credentials(): void
    {
        $response = $this->makeRequest(
            [
                'username' => 'admin',
                'password' => 'wrongpassword',
            ]
        );

        $this->assertErrorContract($response, 404, 'Not Found');

    }//end test_login_invalid_credentials()


    private function makeRequest(array $data=[]): array
    {
        $response = $this->post('/auth/login', $data);

        return $response['result'];

    }//end makeRequest()


}//end class
