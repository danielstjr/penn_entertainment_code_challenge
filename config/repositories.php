<?php

use App\Domain\Repositories\Transaction\DatabaseTransactionRepository;
use App\Domain\Repositories\Transaction\TransactionRepository;
use App\Domain\Repositories\User\DatabaseUserRepository;
use App\Domain\Repositories\User\UserRepository;
use Doctrine\Migrations\DependencyFactory;
use Psr\Container\ContainerInterface;

/**
 * The purpose of this array is to associate interfaces to implementations for repositories
 */
return [
    UserRepository::class => function (ContainerInterface $container) {
        return new DatabaseUserRepository($container->get(DependencyFactory::class)->getEntityManager());
    },
    TransactionRepository::class => function (ContainerInterface $container) {
        return new DatabaseTransactionRepository($container->get(DependencyFactory::class)->getEntityManager());
    }
];