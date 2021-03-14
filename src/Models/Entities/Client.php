<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="client")
 * @ORM @Entity(repositoryClass="App\Models\Repository\ClientRepository")
 */
class Client
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
     * @Column(type="string")
     */
    private string $company;

    /**
     * @Column(type="integer")
     */
    private int $status;

    /**
     * @Column(type="string")
     */
    private string $name;

    /**
     * @Column(type="string")
     */
    private string $office;

    /**
     * @Column(type="string", unique=true)
     */
    private string $email;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="responsible", referencedColumnName="id")
     */
    private User $responsible;


    public function getId(): int
    {
        return $this->id;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): Client
    {
        $this->phone = $phone;
        return $this;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): Client
    {
        $this->company = $company;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Client
    {
        $this->name = $name;
        return $this;
    }

    public function getOffice(): string
    {
        return $this->office;
    }

    public function setOffice(string $office): Client
    {
        $this->office = $office;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Client
    {
        $this->email = $email;
        return $this;
    }

    public function getResponsible(): User
    {
        return $this->responsible;
    }

    public function setResponsible(User $responsible): Client
    {
        $this->responsible = $responsible;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): Client
    {
        $this->status = $status;
        return $this;
    }


}