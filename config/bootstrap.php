<?php

use DI\ContainerBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Purpose of this class is to wrap up all the config files into the container so multiple files can get a fully built
 * container
 */
$builder = new ContainerBuilder();

$builder->addDefinitions(require __DIR__ . '/../config/settings.php');
$builder->addDefinitions(require __DIR__ . '/../config/database.php');
$builder->addDefinitions(require __DIR__ . '/../config/repositories.php');

return $builder->build();