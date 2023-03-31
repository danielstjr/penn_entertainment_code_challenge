<?php

use App\Http\Controllers\UserController;
use Slim\App;

/**
 * This function just separates route creation into its own logical class
 */
return function (App $app) {
    $app->get('/users', [UserController::class, 'index']);
    $app->post('/users', [UserController::class, 'create']);
    $app->post('/users/{id}/earn', [UserController::class, 'earnPoints']);
    $app->post('/users/{id}/redeem', [UserController::class, 'redeemPoints']);
    $app->delete('/users/{id}', [UserController::class, 'delete']);
};