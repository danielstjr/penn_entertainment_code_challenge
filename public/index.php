<?php

use DI\Bridge\Slim\Bridge;

require_once __DIR__ . '/../vendor/autoload.php';

$container = require_once __DIR__ . '/../config/bootstrap.php';

$app = Bridge::create($container);

$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

$app->run();