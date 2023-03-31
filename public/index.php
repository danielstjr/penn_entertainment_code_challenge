<?php

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$builder = new ContainerBuilder();

$builder->addDefinitions(require __DIR__ . '/../config/settings.php');
$builder->addDefinitions(require __DIR__ . '/../config/database.php');
$builder->addDefinitions(require __DIR__ . '/../config/repositories.php');

$container = $builder->build();

$app = Bridge::create($container);

$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

$app->run();