<?php

namespace App\Domain\Repositories\Transaction;

use App\Domain\Models\Transaction;
use App\Domain\Models\User;
use InvalidArgumentException;

/**
 * Repository responsible for maintaining an internal collection of transactions for the sake of an individual request.
 * Primarily used for testing
 */
class InMemoryTransactionRepository implements TransactionRepository
{
    /** @var Transaction[] */
    private array $transactions;

    /**
     * @param array|null $transactions
     */
    public function __construct(?array $transactions = null)
    {
        $this->transactions = $transactions;
    }

    /**
     * Creates a Transaction entity with the function args
     *
     * @param string $description
     * @param int $pointChange
     * @param User $user
     *
     * @return Transaction
     */
    public function create(string $description, int $pointChange, User $user): Transaction
    {
        $transaction = new Transaction($description, $pointChange, $user);
        $this->transactions[] = $transaction;

        return $transaction;
    }

    /**
     * Delete the transaction represented by the given id
     *
     * @param int $id
     *
     * @return bool True if the entity could be deleted, false otherwise
     * @throws InvalidArgumentException If the id does not associate to a transaction
     */
    public function delete(int $id): bool
    {
        $transactionExists = false;
        $newTransactions = [];
        foreach($this->transactions as $loopTransaction) {
            if ($loopTransaction->getId() === $id) {
                $transactionExists = true;
                continue;
            }

            $newTransactions[] = $loopTransaction;
        }

        if (!$transactionExists) {
            throw new InvalidArgumentException("Transaction with id {$id} does not exist");
        }

        $this->transactions = $newTransactions;
        return true;
    }

    /**
     * Retrieves the Transaction associated with the given id
     *
     * @param int $id
     *
     * @return Transaction
     * @throws InvalidArgumentException If the id does associate to a transaction
     */
    public function get(int $id): Transaction
    {
        $transaction = null;
        foreach ($this->transactions as $loopTransaction) {
            if ($loopTransaction->getId() === $id) {
                $transaction = $loopTransaction;
            }
        }

        if ($transaction === null) {
            throw new InvalidArgumentException("Transaction with id {$id} does not exist");
        }

        return $transaction;
    }

    /**
     * Get all transactions for a given user
     *
     * @param User $user
     *
     * @return array
     */
    public function getForUser(User $user): array
    {
        $transactionForUser = [];
        foreach ($this->transactions as $loopTransaction) {
            if ($loopTransaction->getUser()->getId() === $user->getId()) {
                $transactionForUser[] = $loopTransaction;
            }
        }

        return $transactionForUser;
    }
}