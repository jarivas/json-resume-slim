<?php

namespace App\Middleware\Route;

use App\Helper\App;
use App\Helper\ErrorResponse;
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
        $requestId = ErrorResponse::createRequestId();

        return ErrorResponse::writeJson(
            $response,
            401,
            ErrorResponse::messageForStatus(401),
            $requestId
        );

    }//end errorResponse()


}//end class
