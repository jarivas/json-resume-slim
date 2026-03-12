<?php

use App\Controller\Auth\Login;
use App\Service\Auth\Logout;
use App\Middleware\Route\Authentication;

global $app;

$app->post('/auth/login', Login::class);

$app->group('/auth', function ($group) {
    $group->post('/logout', Logout::class);
})->add(Authentication::class);