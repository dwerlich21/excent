<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="contacts")
 * @ORM @Entity(repositoryClass="App\Models\Repository\ContactsRepository")
 */
class Contacts
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
     * @Column(type="datetime")
     */
    private \DateTime $dateOfBirth;

    /**
     * @Column(type="string")
     */
    private string $email;

    /**
     * @Column(type="string")
     */
    private string $phone;

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
    private string $adress;

    /**
     * @Column(type="string")
     */
    private string $neighborhood;

    /**
     * @ManyToOne(targetEntity="TypeContact")
     * @JoinColumn(name="typeContact", referencedColumnName="id")
     */
    private TypeContact $typeContact;

    /**
     * @ManyToOne(targetEntity="Cabinet")
     * @JoinColumn(name="cabinet", referencedColumnName="id")
     */
    private Cabinet $cabinet;

    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(Cabinet $cabinet): Contacts
    {
        $this->cabinet = $cabinet;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): Contacts
    {
        $this->name = $name;
        return $this;
    }

    public function getDateOfBirth(): \DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTime $dateOfBirth): Contacts
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email): Contacts
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone): Contacts
    {
        $this->phone = $phone;
        return $this;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): Contacts
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getUfId(): string
    {
        return $this->ufId;
    }

    public function setUfId(string $ufId): Contacts
    {
        $this->ufId = $ufId;
        return $this;
    }

    public function getCityId(): string
    {
        return $this->cityId;
    }

    public function setCityId(string $cityId): Contacts
    {
        $this->cityId = $cityId;
        return $this;
    }

    public function getAdress(): string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): Contacts
    {
        $this->adress = $adress;
        return $this;
    }

    public function getNeighborhood(): string
    {
        return $this->neighborhood;
    }

    public function setNeighborhood(string $neighborhood): Contacts
    {
        $this->neighborhood = $neighborhood;
        return $this;
    }

    public function getTypeContact(): TypeContact
    {
        return $this->typeContact;
    }

    public function setTypeContact(TypeContact $typeContact): Contacts
    {
        $this->typeContact = $typeContact;
        return $this;
    }


}