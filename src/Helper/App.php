<?php

namespace App\Helper;

use App\Helper\SelectiveLoggingErrorHandler;
use Slim\Factory\AppFactory;
use App\Helper\Logger;
use PhpEnv\EnvManager;
use Slim\App as SlimApp;

class App
{

    /**
     * Summary of app
     * @var SlimApp<\Psr\Container\ContainerInterface|null>
     */
    protected static SlimApp $app;

    protected static Logger $logger;


    public static function prepareEnv(): void
    {
        $rootDir = getRootPath();
        $envPath = "$rootDir/.env";
        if (!file_exists($envPath)) {
            copy("$rootDir/.env.example", $envPath);
        }

        $data = EnvManager::parse($envPath);

        EnvManager::setArray($data);

    }//end prepareEnv()


    public static function prepare(): void
    {
        self::$logger = new Logger();
        self::$app = AppFactory::create();
        self::configureErrorHandling();
        self::addMiddleware();
        self::registerRoutes();
        self::$app->run();

    }//end prepare()


    /**
     * Summary of getApp
     * @return SlimApp<\Psr\Container\ContainerInterface|null>
     */
    public static function getApp(): SlimApp
    {
        return self::$app;

    }//end getApp()


    public static function getLogger(): Logger
    {
        return empty(self::$logger) ? new Logger() : self::$logger;

    }//end getLogger()


    protected static function configureErrorHandling(): void
    {
        $appEnv = strtolower((string) (env('APP_ENV') ?? 'production'));
        $displayErrorDetails = env('APP_DISPLAY_ERROR_DETAILS');

        $displayErrorDetails = ($displayErrorDetails === null) ? ($appEnv !== 'production') : filter_var($displayErrorDetails, FILTER_VALIDATE_BOOL);

        $logErrorDetails = $displayErrorDetails;

        $errorMiddleware = self::$app->addErrorMiddleware(
            $displayErrorDetails,
            true,
            $logErrorDetails,
            self::$logger
        );

        $errorHandler = new SelectiveLoggingErrorHandler(
            self::$app->getCallableResolver(),
            self::$app->getResponseFactory(),
            self::$logger
        );

        $errorMiddleware->setDefaultErrorHandler($errorHandler);
        $errorHandler->forceContentType('application/json');

    }//end configureErrorHandling()


    protected static function addMiddleware(): void
    {
        self::$app->addBodyParsingMiddleware();
        self::$app->addRoutingMiddleware();

    }//end addMiddleware()


    protected static function registerRoutes(): void
    {
        require getRootPath().'/src/routes.php';

    }//end registerRoutes()


}//end class
