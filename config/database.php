<?php

use DI\NotFoundException;
use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * The purpose of this array is to map database config info in the settings to a functioning DependencyFactory. The
 * DependencyFactory allows the application to work with the Doctrine Migrations and ORM libraries
 */
return [
    DependencyFactory::class => function (ContainerInterface $container) {
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
    }
];