<?php

namespace App\Domain\Repositories\Transaction;

use App\Domain\Models\Transaction;
use App\Domain\Models\User;
use Exception;
use InvalidArgumentException;

/**
 * Interface used to specify functions necessary for Transaction management in general and with regards to a user
 */
interface TransactionRepository
{
    /**
     * Creates a Transaction entity with the function args
     *
     * @param string $description
     * @param int $pointChange
     * @param User $user
     *
     * @return Transaction
     * @throws Exception If the transaction cannot be created
     */
    public function create(string $description, int $pointChange, User $user): Transaction;

    /**
     * Delete the transaction represented by the given id
     *
     * @param int $id
     *
     * @return bool True if the entity could be deleted, false otherwise
     * @throws InvalidArgumentException If the id does not associate to a transaction
     */
    public function delete(int $id): bool;

    /**
     * Retrieves the Transaction associated with the given id
     *
     * @param int $id
     *
     * @return Transaction
     * @throws InvalidArgumentException If the id does associate to a transaction
     */
    public function get(int $id): Transaction;

    /**
     * Get all transactions for a given user
     *
     * @param User $user
     *
     * @return array
     */
    public function getForUser(User $user): array;

    /**
     * Delete all transactions for a given user
     *
     * @param User $user
     *
     * @return true
     */
    public function deleteForUser(User $user): bool;
}