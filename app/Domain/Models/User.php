<?php

namespace App\Domain\Models;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use JsonSerializable;

/**
 * Domain Class for mapping user data to a database implementation through a repository
 */
#[Entity, Table(name: 'users')]
final class User implements JsonSerializable
{
    #[Id, Column, GeneratedValue]
    private ?int $id;

    #[Column(type: Types::STRING, unique: true)]
    private string $email;

    #[Column(type: Types::STRING)]
    private string $name;

    #[Column(name: 'points_balance', type: Types::INTEGER)]
    private int $pointsBalance;

    /**
     * Constructor for a User entity
     *
     * @param string $email
     * @param string $name
     * @param int|null $id
     * @param int $pointsBalance
     */
    public function __construct(string $email, string $name, int $pointsBalance, int $id = null)
    {
        $this->email = $email;
        $this->id = $id;
        $this->name = $name;
        $this->pointsBalance = $pointsBalance;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $newEmail
     * @return void
     */
    public function setEmail(string $newEmail): void
    {
        $this->email = $newEmail;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $newName
     * @return void
     */
    public function setName(string $newName): void
    {
        $this->name = $newName;
    }

    /**
     * @return int
     */
    public function getPointsBalance(): int
    {
        return $this->pointsBalance;
    }

    /**
     * @param int $newPointsBalance
     * @return void
     */
    public function setPointsBalance(int $newPointsBalance): void
    {
        $this->pointsBalance = $newPointsBalance;
    }

    /**
     * Array representation of User data, used for serialization
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'name' => $this->getName(),
            'points_balance' => $this->getPointsBalance(),
        ];
    }
}

