<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="institution")
 * @ORM @Entity(repositoryClass="App\Models\Repository\InstitutionRepository")
 */
class Institution
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Column(type="string")
     */
    private string $name;

    /**
     * @Column(type="string")
     */
    private string $responsible;

    /**
     * @Column(type="string")
     */
    private string $phone;

    /**
     * @Column(type="string")
     */
    private string $email;

    /**
     * @Column(type="string")
     */
    private string $zipCode;

    /**
     * @Column(type="string")
     */
    private string $ufId;

    /**
     * @Column(type="string")
     */
    private string $cityId;

    /**
     * @Column(type="string")
     */
    private string $neighborhood;

    /**
     * @Column(type="string")
     */
    private string $adress;

    /**
     * @ManyToOne(targetEntity="Cabinet")
     * @JoinColumn(name="cabinet", referencedColumnName="id")
     */
    private Cabinet $cabinet;

    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(Cabinet $cabinet): Institution
    {
        $this->cabinet = $cabinet;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Institution
    {
        $this->name = $name;
        return $this;
    }

    public function getResponsible(): string
    {
        return $this->responsible;
    }

    public function setResponsible(string $responsible): Institution
    {
        $this->responsible = $responsible;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): Institution
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Institution
    {
        $this->email = $email;
        return $this;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): Institution
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getUfId(): string
    {
        return $this->ufId;
    }

    public function setUfId(string $ufId): Institution
    {
        $this->ufId = $ufId;
        return $this;
    }

    public function getCityId(): string
    {
        return $this->cityId;
    }

    public function setCityId(string $cityId): Institution
    {
        $this->cityId = $cityId;
        return $this;
    }

    public function getNeighborhood(): string
    {
        return $this->neighborhood;
    }

    public function setNeighborhood(string $neighborhood): Institution
    {
        $this->neighborhood = $neighborhood;
        return $this;
    }

    public function getAdress(): string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): Institution
    {
        $this->adress = $adress;
        return $this;
    }


}