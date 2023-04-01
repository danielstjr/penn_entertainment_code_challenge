<?php

namespace App\Domain\Models;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use JsonSerializable;

/**
 * Domain Class for mapping point balance transaction data to a database implementation through a repository
 */
#[Entity, Table(name: 'transactions')]
final class Transaction implements JsonSerializable
{
    #[Id, Column, GeneratedValue]
    private ?int $id;

    #[Column(type: Types::STRING)]
    private string $description;

    #[Column(name: 'point_change', type: Types::INTEGER)]
    private int $pointChange;

    #[ManyToOne]
    private User $user;

    /**
     * Constructor for a Transaction entity
     *
     * @param string $description
     * @param int $pointChange
     * @param User $user
     * @param int|null $id
     */
    public function __construct(string $description, int $pointChange, User $user, int $id = null)
    {
        $this->description = $description;
        $this->id = $id;
        $this->pointChange = $pointChange;
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $newDescription
     * @return void
     */
    public function setDescription(string $newDescription): void
    {
        $this->description = $newDescription;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * This is primarily here for consistency, and to assist the ORM in setting after save
     *
     * @param int $newId
     * @return void
     */
    public function setId(int $newId): void
    {
        $this->id = $newId;
    }

    /**
     * @return int
     */
    public function getPointChange(): int
    {
        return $this->pointChange;
    }

    /**
     * @param int $newPointChange
     * @return void
     */
    public function setPointChange(int $newPointChange): void
    {
        $this->pointChange = $newPointChange;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $newUser): void
    {
        $this->user = $newUser;
    }

    /**
     * Array representation of Transaction data, used for serialization
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'user_id' => $this->user->getId(),
            'description' => $this->getDescription(),
            'point_change' => $this->getPointChange(),
        ];
    }
}

