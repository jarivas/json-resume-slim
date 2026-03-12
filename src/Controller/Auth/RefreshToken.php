<?php

namespace App\Controller\Auth;

use App\Service\Auth\RefreshToken as Service;
use App\Controller\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RefreshToken extends Controller
{


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $service = new Service($request, $response, $args);

        $result = $service->execute();

        return $this->respond($response, $result);

    }//end __invoke()


}//end class
