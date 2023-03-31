<?php

use Doctrine\Migrations\DependencyFactory;
use Domain\Repositories\User\DatabaseUserRepository;
use Domain\Repositories\User\UserRepository;
use Psr\Container\ContainerInterface;

/**
 * The purpose of this array is to associate interfaces to implementations for repositories
 */
return [
    UserRepository::class => function (ContainerInterface $container) {
        return new DatabaseUserRepository($container->get(DependencyFactory::class));
    }
];