<?php

namespace App\Helper;

use Slim\Interfaces\ErrorRendererInterface;
use Throwable;

class ErrorRenderer implements ErrorRendererInterface
{
    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
        if (!$displayErrorDetails) {
            return 'An error occurred while processing your request.';
        }

        $message = "Error: " . $exception->getMessage() . "\n";
        $message .= "File: " . $exception->getFile() . "\n";
        $message .= "Line: " . $exception->getLine() . "\n";

        return $message;
    }
}