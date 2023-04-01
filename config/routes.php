<?php

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Slim\App;

/**
 * This function just separates route creation into its own logical class
 */
return function (App $app) {
    $app->get('/users', [UserController::class, 'index']);
    $app->post('/users', [UserController::class, 'create']);
    $app->delete('/users/{id}', [UserController::class, 'delete']);

    $app->post('/users/{id}/earn', [TransactionController::class, 'earnPoints']);
    $app->post('/users/{id}/redeem', [TransactionController::class, 'redeemPoints']);
};