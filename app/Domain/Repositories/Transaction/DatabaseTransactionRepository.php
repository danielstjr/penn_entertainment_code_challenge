<?php

namespace App\Domain\Repositories\Transaction;

use App\Domain\Models\Transaction;
use App\Domain\Models\User;
use Doctrine\ORM\EntityManager;
use Exception;
use InvalidArgumentException;

/**
 * Repository responsible for mapping database data to Transaction Entities
 */
class DatabaseTransactionRepository implements TransactionRepository
{
    private EntityManager $entityManager;

    /**
     * Construct the repository with access to the Doctrine ORM entity manager for data persistence
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Allow the entity manager to do flush all data into the db at once, effectively enforcing DB transactions
     */
    public function __destruct()
    {
        try {
            $this->entityManager->flush();
        } catch (Exception) {
            // TODO:: Log that data couldn't be persisted, should be pretty rare, also requires Log Library
        }
    }

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
    public function create(string $description, int $pointChange, User $user): Transaction
    {
        $transaction = new Transaction($description, $pointChange, $user);
        try {
            $this->entityManager->persist($transaction);

            // Always force the db to create a new entity right away so we can return an ID to end users
            $this->entityManager->flush();
        } catch (Exception) {
            // Remap the exception here out of its implementation specific exception
            throw new Exception('Failed to save Transaction to database');
        }

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
        $transaction = $this->entityManager->getRepository(Transaction::class)->find($id);
        if ($transaction === null) {
            throw new InvalidArgumentException("Transaction with id {$id} does not exist");
        }

        try {
            $this->entityManager->remove($transaction);
            return true;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Deletes all transactions for a user from the database
     *
     * @param User $user
     * @return bool
     */
    public function deleteForUser(User $user): bool
    {
        $transactions = $this->getForUser($user);
        try {
            foreach ($transactions as $transaction) {
                $this->entityManager->remove($transaction);
            }

            return true;
        } catch (Exception) {
            return false;
        }
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
        /** @var Transaction $transaction */
        $transaction = $this->entityManager->getRepository(Transaction::class)->find($id);
        if ($transaction === null) {
            throw new InvalidArgumentException("Transaction with id {$id} could not be found");
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
        return $this->entityManager
            ->getRepository(Transaction::class)
            ->findBy(['user' => $user]);
    }
}