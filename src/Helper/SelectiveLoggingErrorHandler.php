<?php

namespace App\Helper;

use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpGoneException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpTooManyRequestsException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler;

class SelectiveLoggingErrorHandler extends ErrorHandler
{


    protected function writeToErrorLog(): void
    {
        if ($this->shouldSkipLogging()) {
            return;
        }

        parent::writeToErrorLog();

    }//end writeToErrorLog()


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
