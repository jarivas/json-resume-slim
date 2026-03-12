<?php

use App\Helper\Logger;
use PhpEnv\EnvManager;
use Psr\Log\LoggerInterface;

function getRootPath(): string
{
    return dirname(__DIR__, 2);
}

function prepareEnv(): void
{
    $rootDir = getRootPath();
    $envPath = "$rootDir/.env";
    if (!file_exists($envPath)) {
        copy("$rootDir/.env.example", $envPath);
    }

    $data = EnvManager::parse($envPath);

    EnvManager::setArray($data);
}

function env(string $key): mixed
{
    return EnvManager::get($key);
}

function getAppLogger(): LoggerInterface
{
    global $logger;

    if (isset($logger) && $logger instanceof LoggerInterface) {
        return $logger;
    }

    return new Logger();
}

function logHelper(): LoggerInterface
{
    return getAppLogger();
}

/**
 * Summary of logInfo
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logInfo(string $message, array $context = []): void
{
    $logger = getAppLogger();
    $logger->info($message, $context);
}

/**
 * Summary of logError
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logError(string $message, array $context = []): void
{
    $logger = getAppLogger();
    $logger->error($message, $context);
}

/**
 * Summary of logWarning
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logWarning(string $message, array $context = []): void
{
    $logger = getAppLogger();
    $logger->warning($message, $context);
}

/**
 * Summary of logNotice
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logNotice(string $message, array $context = []): void
{
    $logger = getAppLogger();
    $logger->notice($message, $context);
}

/**
 * Summary of logCritical
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logCritical(string $message, array $context = []): void
{
    $logger = getAppLogger();
    $logger->critical($message, $context);
}

/**
 * Summary of logAlert
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logAlert(string $message, array $context = []): void
{
    $logger = getAppLogger();
    $logger->alert($message, $context);
}

/**
 * Summary of logEmergency
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logEmergency(string $message, array $context = []): void
{
    $logger = getAppLogger();
    $logger->emergency($message, $context);
}

/**
 * Summary of logDebug
 * @param string $message
 * @param array<mixed> $context
 * @return void
 */
function logDebug(string $message, array $context = []): void
{
    $logger = getAppLogger();
    $logger->debug($message, $context);
}