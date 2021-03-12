<?php
/**
 * Created by PhpStorm.
 * User: rwerl
 * Date: 16/04/2019
 * Time: 22:52
 */

namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="recoverPassword")
 * @ORM @Entity(repositoryClass="App\Models\Repository\RecoverPasswordRepository")
 */
class RecoverPassword
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user", referencedColumnName="id")
     */
    private User $user;

    /**
     * @Column(type="datetime")
     */
    private \DateTime $created;

    /**
     * @Column(type="string")
     */
    private string $token;

    /**
     * @Column(type="boolean")
     */
    private bool $used;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): RecoverPassword
    {
        $this->user = $user;
        return $this;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): RecoverPassword
    {
        $this->created = $created;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): RecoverPassword
    {
        $this->token = $token;
        return $this;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): RecoverPassword
    {
        $this->used = $used;
        return $this;
    }

}