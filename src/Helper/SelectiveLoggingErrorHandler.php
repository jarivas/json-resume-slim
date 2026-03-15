<?php

namespace App\Helper;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpGoneException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpTooManyRequestsException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler;
use Throwable;

class SelectiveLoggingErrorHandler extends ErrorHandler
{

    protected string $requestId;


    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        $this->requestId = ErrorResponse::createRequestId();

        return parent::__invoke(
            $request,
            $exception,
            $displayErrorDetails,
            $logErrors,
            $logErrorDetails
        );

    }//end __invoke()


    protected function writeToErrorLog(): void
    {
        if ($this->shouldSkipLogging()) {
            return;
        }

        $path = $this->request->getUri()->getPath();
        $method = $this->request->getMethod();
        $logMessage = sprintf(
            '[%s] %s %s => %d %s',
            $this->requestId,
            $method,
            $path,
            $this->statusCode,
            $this->exception->getMessage()
        );

        if ($this->logErrorDetails === true) {
            $logMessage .= PHP_EOL.$this->exception->getTraceAsString();
        }

        $this->logError($logMessage);

    }//end writeToErrorLog()


    protected function respond(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($this->statusCode);

        if ($this->exception instanceof HttpMethodNotAllowedException) {
            $allowedMethods = implode(', ', $this->exception->getAllowedMethods());
            $response = $response->withHeader('Allow', $allowedMethods);
        }

        $extraPayload = [];

        if ($this->displayErrorDetails === true) {
            $extraPayload['debug'] = [
                'type' => get_class($this->exception),
            ];
        }

        return ErrorResponse::writeJson(
            $response,
            $this->statusCode,
            ErrorResponse::messageForStatus($this->statusCode),
            $this->requestId,
            $extraPayload
        );

    }//end respond()


    protected function shouldSkipLogging(): bool
    {
        return $this->exception instanceof HttpBadRequestException
            || $this->exception instanceof HttpForbiddenException
            || $this->exception instanceof HttpGoneException
            || $this->exception instanceof HttpMethodNotAllowedException
            || $this->exception instanceof HttpNotFoundException
            || $this->exception instanceof HttpNotImplementedException
            || $this->exception instanceof HttpTooManyRequestsException
            || $this->exception instanceof HttpUnauthorizedException;

    }//end shouldSkipLogging()


}//end class
