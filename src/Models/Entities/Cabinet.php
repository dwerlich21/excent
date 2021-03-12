<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="cabinet")
 * @ORM @Entity(repositoryClass="App\Models\Repository\CabinetRepository")
 */
class Cabinet
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
     * @Column(type="string")
     */
    private string $name;

    public function getId(): int
    {
        return $this->id;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): Cabinet
    {
        $this->active = $active;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Cabinet
    {
        $this->name = $name;
        return $this;
    }

}