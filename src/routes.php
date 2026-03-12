<?php

use App\Helper\App;
use App\Controller\Auth\Login;
use App\Controller\Auth\Logout;
use App\Controller\Auth\RefreshToken;
use App\Middleware\Route\Authentication;

$app = App::getApp();

$app->post('/auth/login', Login::class);

$app->group(
    '/auth',
    function ($group) {
    $group->post('/logout', Logout::class);
    $group->post('/refresh-token', RefreshToken::class);
    }
)->add(Authentication::class);
