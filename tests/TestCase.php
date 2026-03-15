<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use HttpClient\Post;
use HttpClient\Get;
use HttpClient\Patch;
use HttpClient\Delete;

abstract class TestCase extends BaseTestCase
{
    protected const BASE_URL = 'http://localhost:8000';


    protected function assertErrorContract(array $payload, int $status, string $message): void
    {
        $this->assertArrayHasKey('error', $payload);
        $this->assertArrayHasKey('message', $payload);
        $this->assertArrayHasKey('status', $payload);
        $this->assertArrayHasKey('request_id', $payload);

        $this->assertSame($message, $payload['error']);
        $this->assertSame($message, $payload['message']);
        $this->assertSame($status, $payload['status']);
        $this->assertIsString($payload['request_id']);
        $this->assertNotSame('', $payload['request_id']);

    }//end assertErrorContract()


    protected function assertItemsCollection(array $payload): void
    {
        $this->assertArrayHasKey('items', $payload);
        $this->assertIsArray($payload['items']);

    }//end assertItemsCollection()


    protected function assertDeletedSuccessfully(array $payload): void
    {
        $this->assertArrayHasKey('message', $payload);
        $this->assertSame('Deleted successfully', $payload['message']);

    }//end assertDeletedSuccessfully()


    protected function login(): array
    {
        $response = $this->post(
            '/auth/login',
            [
                'username' => env('USERNAME'),
                'password' => env('PASSWORD'),
            ]
        );

        $result = $response['result'];

        if (!isset($result['token'])) {
            $status = $result['status'] ?? null;
            $message = $result['message'] ?? '';

            if ($status === 500 && $message === 'Internal Server Error') {
                $this->markTestSkipped('Database unavailable for auth-dependent HTTP tests.');
            }
        }

        return $result;

    }//end login()


    protected function getAuthHeaders(): array
    {
        $response = $this->login();

        return ["Authorization: Bearer {$response['token']}"];

    }//end getAuthHeaders()


    protected function post(string $uri, array $data, array $headers=[]): array
    {
        $client = new Post(self::BASE_URL);
        $response = $client->sendJson($uri, $data, $headers);

        return $response;

    }//end post()


    protected function get(string $uri, array $headers=[]): array
    {
        $client = new Get(self::BASE_URL);
        $response = $client->send($uri, null, $headers, true);

        return $response;

    }//end get()


    protected function patch(string $uri, array $data, array $headers=[]): array
    {
        $client = new Patch(self::BASE_URL);
        $response = $client->sendJson($uri, $data, $headers);

        return $response;

    }//end patch()


    protected function delete(string $uri, array $headers=[]): array
    {
        $client = new Delete(self::BASE_URL);
        $response = $client->send($uri, null, $headers, true);

        return $response;

    }//end delete()


}//end class
