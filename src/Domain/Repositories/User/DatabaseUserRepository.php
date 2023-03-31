<?php

namespace Domain\Repositories\User;

use Doctrine\ORM\EntityManager;
use Domain\Models\User;
use Exception;
use Psr\Log\InvalidArgumentException;

/**
 * Repository class that initializes User Domain models from the Doctrine Entity Manager
 */
class DatabaseUserRepository implements UserRepository
{
    private EntityManager $entityManager;

    /**
     * Construct the class with an associated Doctrine Entity Manager to interact with the Doctrine ORM
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Utilize Doctrine ORM's EntityManager to save a User instance to the database
     *
     * @param string $email Unique Email address for the given user
     * @param string $name Name to be associated with the user
     * @param int $pointBalance Total points associated with the user through earning and after redemption
     *
     * @throws InvalidArgumentException If the email is non-unique, or the points balance is less than 0
     * @throws Exception If the user could not be created
     * @return User
     */
    public function create(string $email, string $name, int $pointBalance = 0): User
    {
        // TODO:: Implement user creation
        return new User('', '', '');
    }

    /**
     * Utilize Doctrine's ORM Entity Manager to delete a User instance from the database specified by the given id
     *
     * @param int $id
     *
     * @throws InvalidArgumentException If the id doesn't exist in the database
     * @return bool
     */
    public function delete(int $id): bool
    {
        // TODO:: Implement user deletion
        return true;
    }

    /**
     * Utilize Doctrine's ORM to retrieve a User instance from the database
     *
     * @param int $id Unique User identifier
     *
     * @throws Exception When the user cannot be found, mapped, and returned
     * @return User Domain model retrieved from its database representation
     */
    public function get(int $id): User
    {
        // TODO:: Implement single user retrieval
        return new User('', '');
    }

    /**
     * Utilize Doctrine's ORM to retrieve all available User instances from the database
     *
     * @throws Exception When any of the users failed to be retrieved, mapped, and returned
     * @return array Array of all user instances mapped to a Domain Model
     */
    public function getAll(): array
    {
        // TODO:: Implement all users retrieval
        return [];
    }

    /**
     * Utilize Doctrine's ORM to store the new balance for the given user in the database
     *
     * @param User $user
     * @param int $newBalance
     * @return bool True if the balance is successfully stored, false if it wasn't
     */
    public function updatePoints(User $user, int $newBalance): bool
    {
        // TODO:: Implement updating point count
        return true;
    }
}