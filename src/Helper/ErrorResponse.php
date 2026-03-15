<?php

namespace App\Helper;

use JsonException;
use Psr\Http\Message\ResponseInterface;

class ErrorResponse
{


    public static function createRequestId(): string
    {
        return bin2hex(random_bytes(12));

    }//end createRequestId()


    public static function messageForStatus(int $statusCode): string
    {
        return match ($statusCode) {
            400     => 'Bad Request',
            401     => 'Unauthorized',
            403     => 'Forbidden',
            404     => 'Not Found',
            405     => 'Method Not Allowed',
            410     => 'Gone',
            429     => 'Too Many Requests',
            default => 'Internal Server Error',
        };

    }//end messageForStatus()


    /**
     * @param array<string, mixed> $extra
     */
    public static function writeJson(
        ResponseInterface $response,
        int $statusCode,
        string $message,
        string $requestId,
        array $extra=[]
    ): ResponseInterface {
        $payload = [
            'error'      => $message,
            'message'    => $message,
            'status'     => $statusCode,
            'request_id' => $requestId,
        ];

        $payload = array_merge($payload, $extra);

        try {
            $json = json_encode($payload, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $json = '{"error":"Internal Server Error","message":"Unexpected error","status":500}';
            $statusCode = 500;
        }

        $response->getBody()->write($json);

        return $response->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-Request-Id', $requestId);

    }//end writeJson()


}//end class
