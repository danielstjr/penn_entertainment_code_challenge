<?php

namespace App\Domain\Repositories;

use App\Domain\Models\User;
use InvalidArgumentException;

/**
 * In Memory implementation of the UserRepository, initial use case for testing
 */
class InMemoryUserRepository implements UserRepository
{
    private array $users;

    /**
     * Take a list of users for the in memory user repository or default
     *
     * @param array|null $users
     */
    public function __construct(?array $users = null)
    {
        $this->users = $users ?? [
            new User('user1@danielstone.dev', 'User One', 1, 1),
            new User('user2@danielstone.dev', 'User Two', 2, 2),
            new User('user3@danielstone.dev', 'User Three', 3, 3),
            new User('user4@danielstone.dev', 'User Four', 4, 4),
        ];
    }

    /**
     * Create and add a user to the internal array
     *
     * @param string $email
     * @param string $name
     * @param int $pointBalance
     *
     * @return User
     */
    public function create(string $email, string $name, int $pointBalance = 0): User
    {
        $user = new User($email, $name, count($this->users) + 1, $pointBalance);
        $this->users[] = $user;

        return $user;
    }

    /**
     * Remove the user from the internal array
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        $userExists = false;
        $newUserArray = [];
        foreach ($this->users as $user) {
            if ($user->getId() === $id) {
                $userExists = true;
                continue;
            }

            $newUserArray[] = $user;
        }

        if (!$userExists) {
            throw new InvalidArgumentException('User id not in array');
        }

        $this->users = $newUserArray;

        return true;
    }

    /**
     * Find the user in the internal array
     *
     * @param int $id
     *
     * @throws InvalidArgumentException When the $id is not found in the internal user array
     * @return User
     */
    public function get(int $id): User
    {
        foreach($this->users as $user) {
            if ($user->getId() === $id) {
                return $user;
            }
        }

        throw new InvalidArgumentException('User could not be found');
    }

    /**
     * Return the full user array
     *
     * @return User[]
     */
    public function getAll(): array
    {
        return $this->users;
    }

    /**
     * Find the existing internal user and update it
     *
     * @param User $user
     * @param int $newBalance
     *
     * @return bool
     */
    public function updatePoints(User $user, int $newBalance): bool
    {
        foreach ($this->users as $loopUser) {
            if ($loopUser->getId() === $user->getId()) {
                $loopUser->setPointsBalance($newBalance);
                break;
            }
        }

        return true;
    }
}