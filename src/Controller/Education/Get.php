<?php

namespace App\Controller\Education;

use App\Controller\Controller;
use App\Service\Education\Get as Service;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Get extends Controller
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
