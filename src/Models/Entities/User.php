<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="users")
 * @ORM @Entity(repositoryClass="App\Models\Repository\UserRepository")
 */
class User
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Column(type="boolean")
     */
    private bool $active;

    /**
     * @Column(type="integer")
     */
    private int $type;

    /**
     * @Column(type="string")
     */
    private string $name;

    /**
     * @Column(type="string")
     */
    private string $password;

    /**
     * @Column(type="string", unique=true)
     */
    private string $email;

    public function getId(): int
    {
        return $this->id;
    }

    public function isActive(): int
    {
        return $this->active;
    }

    public function activeStr(): string
    {
        if (1 == $this->active) {
            return "Ativo";
        }
        return "Inativo";

    }

    public function setActive(bool $active): User
    {
        $this->active = $active;
        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): User
    {
        $this->type = $type;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }
}