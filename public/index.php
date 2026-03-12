<?php

use Slim\Factory\AppFactory;
use App\Helper\ErrorRenderer;
use App\Helper\Logger;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Helper/Functions.php';

prepareEnv();

/**
 * Instantiate App
 *
 * In order for the factory to work you need to ensure you have installed
 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
 * ServerRequest creator (included with Slim PSR-7)
 */
$app = AppFactory::create();
$logger = new Logger();

$app->addBodyParsingMiddleware();

/**
  * The routing middleware should be added earlier than the ErrorMiddleware
  * Otherwise exceptions thrown from it will not be handled by the middleware
  */
$app->addRoutingMiddleware();

/**
 * Add Error Middleware
 *
 * @param bool                              $displayErrorDetails -> Should be set to false in production
 * @param bool                              $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool                              $logErrorDetails -> Display error details in error log
 * @param \Psr\Log\LoggerInterface|null     $logger -> Optional PSR-3 Logger  
 *
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true, $logger);

$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('application/json', ErrorRenderer::class);
$errorHandler->forceContentType('application/json');

// Define app routes
require getRootPath() . '/src/routes.php';

// Run app
$app->run();