<?php

use Doctrine\Migrations\DependencyFactory;
use App\Domain\Repositories\DatabaseUserRepository;
use App\Domain\Repositories\UserRepository;
use Psr\Container\ContainerInterface;

/**
 * The purpose of this array is to associate interfaces to implementations for repositories
 */
return [
    UserRepository::class => function (ContainerInterface $container) {
        return new DatabaseUserRepository($container->get(DependencyFactory::class)->getEntityManager());
    }
];