<?php

namespace App\Controller\Work;

use App\Controller\Controller;
use App\Service\Work\Create as Service;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class Create extends Controller
{


    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $service = new Service($request, $response, $args);

        if (!$service->validate()) {
            throw new HttpNotFoundException($request);
        }

        $result = $service->execute();

        return $this->respond($response, $result, 201);

    }//end __invoke()


}//end class
