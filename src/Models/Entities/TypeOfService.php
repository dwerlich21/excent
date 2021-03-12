<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="typeOfService")
 * * @ORM @Entity(repositoryClass="App\Models\Repository\TypeOfServiceRepository")
 */
class TypeOfService
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
     * @ManyToOne(targetEntity="Cabinet")
     * @JoinColumn(name="cabinet", referencedColumnName="id")
     */
    private Cabinet $cabinet;

    /**
     * @Column(type="boolean")
     */
    private bool $active;

    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(Cabinet $cabinet): TypeOfService
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

    public function setName(string $name): TypeOfService
    {
        $this->name = $name;
        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): TypeOfService
    {
        $this->active = $active;
        return $this;
    }


}