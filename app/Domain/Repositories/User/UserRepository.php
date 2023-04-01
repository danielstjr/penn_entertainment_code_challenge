<?php

namespace App\Domain\Repositories\User;

use App\Domain\Models\User;
use Exception;
use InvalidArgumentException;

/**
 * Interface that specifies functions for mapping the User Domain Model to its Data Layer retrieval
 */
interface UserRepository
{
    /**
     * Create a new user instance for the given email, name, and point balance
     *
     * @param string $email
     * @param string $name
     * @param int $pointBalance
     *
     * @return User
     * @throws Exception If the user could not be created
     * @throws InvalidArgumentException If the email is non-unique, or the points balance is less than 0
     */
    public function create(string $email, string $name, int $pointBalance = 0): User;

    /**
     * Delete the User represented by the given id
     *
     * @param int $id
     *
     * @return bool True if the data was deleted, false if it wasn't
     * @throws InvalidArgumentException If the id doesn't associate to a user
     */
    public function delete(int $id): bool;

    /**
     * Retrieves a User Domain Model instance by ID
     *
     * @param int $id
     *
     * @return User
     * @throws InvalidArgumentException When the user cannot be found
     */
    public function get(int $id): User;

    /**
     * Retrieves all stored user data and maps each to a User Domain Model
     *
     * @return User[]
     * @throws Exception When any of the users failed to be retrieved, mapped, and returned
     */
    public function getAll(): array;

    /**
     * Updates the given user to have the new point balance
     *
     * @param User $user
     * @param int $newBalance
     *
     * @return bool
     */
    public function updatePoints(User $user, int $newBalance): bool;
}