<?php

use DI\Container;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\DumpSchemaCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\RollupCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\SyncMetadataCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Symfony\Component\Console\Command\ListCommand;

/** @var Container $container */
$container = require_once __DIR__ . '/config/bootstrap.php';

/** @var DependencyFactory $dependencyFactory */
$dependencyFactory = $container->get(DependencyFactory::class);

// Because the EntityManager used to construct the DependencyFactory was based on the ORM Config rather than a classic
// Migrations config, we need to adhoc load in the Migrations commands to be able to use them in the CLI
$migrationCommands = [
    new DumpSchemaCommand($dependencyFactory),
    new ExecuteCommand($dependencyFactory),
    new GenerateCommand($dependencyFactory),
    new LatestCommand($dependencyFactory),
    new ListCommand(),
    new MigrateCommand($dependencyFactory),
    new RollupCommand($dependencyFactory),
    new StatusCommand($dependencyFactory),
    new SyncMetadataCommand($dependencyFactory),
    new VersionCommand($dependencyFactory),
];

ConsoleRunner::run(new SingleManagerProvider($dependencyFactory->getEntityManager()), $migrationCommands);
