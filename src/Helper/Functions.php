<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use PhpEnv\EnvManager;
use App\Helper\App;


function getRootPath(): string
{
    return dirname(__DIR__, 2);

}//end getRootPath()


function env(string $key): mixed
{
    return EnvManager::get($key);

}//end env()


function getAppLogger(): LoggerInterface
{
    return App::getLogger();

}//end getAppLogger()


/**
 * Summary of logInfo
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logInfo(string $message, array $context=[]): void
{
    $logger = getAppLogger();
    $logger->info($message, $context);

}//end logInfo()


/**
 * Summary of logError
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logError(string $message, array $context=[]): void
{
    $logger = getAppLogger();
    $logger->error($message, $context);

}//end logError()


/**
 * Summary of logWarning
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logWarning(string $message, array $context=[]): void
{
    $logger = getAppLogger();
    $logger->warning($message, $context);

}//end logWarning()


/**
 * Summary of logNotice
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logNotice(string $message, array $context=[]): void
{
    $logger = getAppLogger();
    $logger->notice($message, $context);

}//end logNotice()


/**
 * Summary of logCritical
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logCritical(string $message, array $context=[]): void
{
    $logger = getAppLogger();
    $logger->critical($message, $context);

}//end logCritical()


/**
 * Summary of logAlert
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logAlert(string $message, array $context=[]): void
{
    $logger = getAppLogger();
    $logger->alert($message, $context);

}//end logAlert()


/**
 * Summary of logEmergency
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logEmergency(string $message, array $context=[]): void
{
    $logger = getAppLogger();
    $logger->emergency($message, $context);

}//end logEmergency()


/**
 * Summary of logDebug
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logDebug(string $message, array $context=[]): void
{
    $logger = getAppLogger();
    $logger->debug($message, $context);

}//end logDebug()


function getToken(ServerRequestInterface $request): ?string
{
    $authHeader = $request->getHeaderLine('Authorization');
    if (empty($authHeader)) {
        return null;
    }

    // Assuming the token is in the format "Bearer <token>".
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return $matches[1];
    }

    return null;

}//end getToken()
