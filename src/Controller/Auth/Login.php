<?php

namespace App\Controller\Auth;

use App\Service\Auth\Login as LoginService;
use App\Controller\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Login extends Controller
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $service = new LoginService($request, $response, $args);

        if (!$service->validate()) {
            return $this->respond($response, ['error' => 'Invalid credentials'], 401);
        }
        
        $result = $service->execute();

        return $this->respond($response, $result);
    }
}