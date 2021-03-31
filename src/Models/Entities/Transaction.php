<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="transaction")
 * @ORM @Entity(repositoryClass="App\Models\Repository\TransactionRepository")
 */
class Transaction
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Column(type="float", nullable=true)
     */
    private ?float $withdrawals = 0;

    /**
     * @Column(type="float", nullable=true)
     */
    private ?float $deposit = 0;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="responsible", referencedColumnName="id")
     */
    private User $responsible;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user", referencedColumnName="id")
     */
    private User $user;

    /**
     * @ManyToOne(targetEntity="Countries")
     * @JoinColumn(name="country", referencedColumnName="id")
     */
    private Countries $country;

    /**
     * @Column(type="datetime")
     */
    private \DateTime $date;

    public function __construct()
    {
        $this->date = new \DateTime();
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getWithdrawals(): ?float
    {
        return $this->withdrawals;
    }

    public function setWithdrawals(?float $withdrawals): Transaction
    {
        $this->withdrawals = $withdrawals;
        return $this;
    }

    public function getResponsible(): User
    {
        return $this->responsible;
    }

    public function setResponsible(User $responsible): Transaction
    {
        $this->responsible = $responsible;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Transaction
    {
        $this->user = $user;
        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getCountry(): Countries
    {
        return $this->country;
    }

    public function setCountry(Countries $country): Transaction
    {
        $this->country = $country;
        return $this;
    }

    public function getDeposit():?float
    {
        return $this->deposit;
    }

    public function setDeposit(?float $deposit): Transaction
    {
        $this->deposit = $deposit;
        return $this;
    }


}