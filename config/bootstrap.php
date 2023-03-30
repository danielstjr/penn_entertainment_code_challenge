<?php

use DI\NotFoundException;
use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use DI\Container;

require_once __DIR__ . '/../vendor/autoload.php';

// The purpose of this file is to fill the container with relevant class mappings to dependency inject files at will
$container = new Container(require __DIR__ . '/settings.php');

// Here we associate config information for the db connection and ORM metadata files to the migration files, and return
// a factory that can server both
$container->set(DependencyFactory::class, static function (Container $container): DependencyFactory {
    // Pull doctrine based settings from the settings.php file
    $settings = $container->get('settings');
    if (!array_key_exists('doctrine', $settings)) {
        throw new NotFoundException('Doctrine config settings missing');
    }

    // Build the ORM Config first because the migration config wraps the ORM config at the dependency factory level
    $doctrineSettings = $settings['doctrine'];
    $ORMConfig = ORMSetup::createAttributeMetadataConfiguration(
        $doctrineSettings['metadata_directories'],
        $doctrineSettings['dev_mode'],
        null,
        new ArrayAdapter() // To avoid pre-optimization, the cache is a simple array driver to avoid server bloat
    );

    // Create the entity manager that retrieves ORM instances based on the config connection
    $connection = DriverManager::getConnection($doctrineSettings['connection'], $ORMConfig);
    $entityManager = new EntityManager($connection, $ORMConfig);

    // Create the wrapping migration config to handle finding and versioning migrations, then return its factory
    $migrationConfig = new ConfigurationArray($doctrineSettings['migrations']);
    return DependencyFactory::fromEntityManager($migrationConfig, new ExistingEntityManager($entityManager));
});

return $container;