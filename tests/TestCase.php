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


    protected function login(): array
    {
        $response = $this->post(
            '/auth/login',
            [
                'username' => env('USERNAME'),
                'password' => env('PASSWORD'),
            ]
        );

        return $response['result'];

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
