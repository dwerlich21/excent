<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="cabinetDetails")
 * @ORM @Entity(repositoryClass="App\Models\Repository\CabinetDetailsRepository")
 */
class CabinetDetails
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Column(type="string")
     */
    private string $email;

    /**
     * @Column(type="string")
     */
    private string $city;

    /**
     * @Column(type="text")
     */
    private string $header;

    /**
     * @Column(type="text")
     */
    private string $footer;

    /**
     * @Column(type="text")
     */
    private string $birthdayMessage;

    /**
     * @ManyToOne(targetEntity="Cabinet")
     * @JoinColumn(name="cabinet", referencedColumnName="id")
     */
    private Cabinet $cabinet;

    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(Cabinet $cabinet): CabinetDetails
    {
        $this->cabinet = $cabinet;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): CabinetDetails
    {
        $this->email = $email;
        return $this;
    }

    public function getHeader():string
    {
        return str_replace('..//uploads', BASEURL.'/uploads', $this->header);
    }

    public function setHeader(string $header): CabinetDetails
    {
        $this->header = $header;
        return $this;
    }

    public function getFooter(): string
    {
        return str_replace('..//uploads', BASEURL.'/uploads', $this->footer);
    }

    public function setFooter(string $footer): CabinetDetails
    {
        $this->footer = $footer;
        return $this;
    }

    public function getBirthdayMessage(): string
    {
        return str_replace('..//uploads', BASEURL.'/uploads', $this->birthdayMessage);
    }

    public function setBirthdayMessage(string $birthdayMessage): CabinetDetails
    {
        $this->birthdayMessage = $birthdayMessage;
        return $this;
    }

    public function getCity():string
    {
        return $this->city;
    }

    public function setCity(string $city): CabinetDetails
    {
        $this->city = $city;
        return $this;
    }


}