<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="deal")
 * @ORM @Entity(repositoryClass="App\Models\Repository\DealRepository")
 */
class Deal
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Column(type="string")
     */
    private string $phone;

    /**
     * @Column(type="string", nullable=true)
     */
    private ?string $company = null;

    /**
     * @Column(type="integer")
     */
    private int $status;

    /**
     * @Column(type="string")
     */
    private string $name;

    /**
     * @Column(type="string", nullable=true)
     */
    private ?string $office = null;

    /**
     * @Column(type="string", unique=true)
     */
    private string $email;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="responsible", referencedColumnName="id", nullable=true)
     */
    private ?User $responsible = null;

    /**
     * @ManyToOne(targetEntity="Countries")
     * @JoinColumn(name="country", referencedColumnName="id")
     */
    private Countries $country;


    public function getId(): int
    {
        return $this->id;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): Deal
    {
        $this->phone = $phone;
        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): Deal
    {
        $this->company = $company;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Deal
    {
        $this->name = $name;
        return $this;
    }

    public function getOffice(): ?string
    {
        return $this->office;
    }

    public function setOffice(?string $office): Deal
    {
        $this->office = $office;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Deal
    {
        $this->email = $email;
        return $this;
    }

    public function getResponsible(): ?User
    {
        return $this->responsible;
    }

    public function setResponsible(?User $responsible): Deal
    {
        $this->responsible = $responsible;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): Deal
    {
        $this->status = $status;
        return $this;
    }

    public function getCountry(): Countries
    {
        return $this->country;
    }

    public function setCountry(Countries $country): Deal
    {
        $this->country = $country;
        return $this;
    }


}