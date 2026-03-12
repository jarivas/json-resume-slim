<?php

use SqlModels\DbConnectionInfo;
use SqlModels\GenerationMysql;
use SqlModels\Dbms;

require 'index.php';

$dbInfo = new DbConnectionInfo(
    type: Dbms::Mysql,
    dbHost: env('MARIADB_HOST'),
    dbName: env('MARIADB_DATABASE'),
    dbUsername: env('MARIADB_USER'),
    dbPassword: env('MARIADB_PASSWORD'),
    dbPort: env('MARIADB_PORT'),
);

$targetDir = getRootPath() . '/src/Model';
$namespace = "App\\Model";
$generator = new GenerationMysql($dbInfo, $targetDir, $namespace);

$result = $generator->process();

if (is_string($result)) {
    throw new RuntimeException("Model generation failed: $result");
}