<?php

namespace App\Controller\Interest;

use App\Controller\Controller;
use App\Service\Interest\Update as Service;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class Update extends Controller
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

        return $this->respond($response, $result);

    }//end __invoke()


}//end class
