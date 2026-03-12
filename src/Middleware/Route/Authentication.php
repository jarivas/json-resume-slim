<?php

namespace App\Middleware\Route;

use App\Helper\App;
use App\Model\Tokens;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Authentication implements MiddlewareInterface
{


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->validate($request)) {
            return $this->errorResponse();
        }

        // Proceed with the next middleware.
        return $handler->handle($request);

    }//end process()


    protected function validate(ServerRequestInterface $request): bool
    {
        $token = getToken($request);

        if ($token === null) {
            return false;
        }

        $model = Tokens::first(
            [
                [
                    'token',
                    '=',
                    $token,
                ],
                [
                    'expires_at',
                    '>',
                    date('Y-m-d H:i:s'),
                ],
            ]
        );

        return $model instanceof Tokens;

    }//end validate()


    protected function errorResponse(): ResponseInterface
    {
        $response = App::getApp()->getResponseFactory()->createResponse(401);
        $data = ['error' => 'Unauthorized'];
        $json = json_encode($data, JSON_THROW_ON_ERROR);

        $response->getBody()->write($json);

        return $response->withStatus(401)
            ->withHeader('Content-Type', 'application/json');

    }//end errorResponse()


}//end class
