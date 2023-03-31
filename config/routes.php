<?php

use Http\Controllers\UserController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * This function just separates route creation into its own logical class
 */
return function (App $app) {
    $app->group('/users', function (RouteCollectorProxy $group) {
        $group->get('', [UserController::class, 'index']);
        $group->post('', [UserController::class, 'create']);
        $group->post('/{id}/earn', [UserController::class, 'earnPoints']);
        $group->post('/{id}/redeem', [UserController::class, 'redeemPoints']);
        $group->delete('/{id}', [UserController::class, 'delete']);
    });
};