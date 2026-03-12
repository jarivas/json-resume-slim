<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Controller
{


    /**
     * Summary of __invoke
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array<string, mixed> $args
     * @return ResponseInterface
     */
    abstract public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface;


    /**
     * Summary of respond
     * @param ResponseInterface $response
     * @param array<string, mixed> $data
     * @param int $status
     * @return ResponseInterface
     */
    protected function respond(
        ResponseInterface $response,
        array $data,
        int $status=200
    ): ResponseInterface {
        $json = json_encode($data, JSON_THROW_ON_ERROR);

        $response->getBody()->write($json);

        return $response->withStatus($status)
            ->withHeader('Content-Type', 'application/json');

    }//end respond()


}//end class
