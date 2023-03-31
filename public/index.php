<?php

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Domain\Repositories\User\UserRepository;
use Psr\Container\ContainerInterface;

require_once __DIR__ . '/../vendor/autoload.php';

$builder = new ContainerBuilder();

$builder->addDefinitions(require __DIR__ . '/../config/settings.php');
$builder->addDefinitions(require __DIR__ . '/../config/database.php');
$builder->addDefinitions(require __DIR__ . '/../config/repositories.php');

$container = $builder->build();

$app = Bridge::create($container);

$container->set(\Http\Controllers\UserController::class, function (ContainerInterface $container) {
    return new \Http\Controllers\UserController($container->get(UserRepository::class));
});

$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

$app->run();