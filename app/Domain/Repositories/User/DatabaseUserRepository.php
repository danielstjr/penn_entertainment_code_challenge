<?php

namespace App\Domain\Repositories\User;

use App\Domain\Models\User;
use Doctrine\ORM\EntityManager;
use Exception;
use InvalidArgumentException;

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
     * @return User
     * @throws Exception If the user could not be created
     * @throws InvalidArgumentException If the email is non-unique, or the points balance is less than 0
     */
    public function create(string $email, string $name, int $pointBalance = 0): User
    {
        $existingUsers = $this->entityManager->getRepository(User::class)->findBy(['email' => $email]);
        if (!empty($existingUsers)) {
            throw new InvalidArgumentException('Duplicate Email Address');
        }

        $user = new User($email, $name, $pointBalance, null);
        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (Exception) {
            // Remap the exception here out of its implementation specific exception
            throw new Exception('Failed to save User to database');
        }

        return $user;
    }

    /**
     * Utilize Doctrine's ORM Entity Manager to delete a User instance from the database specified by the given id
     *
     * @param int $id
     *
     * @return bool
     * @throws InvalidArgumentException If the id doesn't exist in the database
     */
    public function delete(int $id): bool
    {
        $existingUser = $this->get($id);
        try {
            $this->entityManager->remove($existingUser);
            $this->entityManager->flush();
            return true;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Utilize Doctrine's ORM to retrieve a User instance from the database
     *
     * @param int $id Unique User identifier
     *
     * @return User Domain model retrieved from its database representation
     * @throws InvalidArgumentException When the user cannot be found, mapped, and returned
     */
    public function get(int $id): User
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if ($user === null) {
            throw new InvalidArgumentException("Could not find user with id {$id}");
        }

        return $user;
    }

    /**
     * Utilize Doctrine's ORM to retrieve all available User instances from the database
     *
     * @return User[] Array of all user instances mapped to a Domain Model
     * @throws Exception When any of the users failed to be retrieved, mapped, and returned
     */
    public function getAll(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
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
        $user->setPointsBalance($newBalance);
        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (Exception) {
            // Remap the error here to hide implementation details
            return false;
        }

        return true;
    }
}