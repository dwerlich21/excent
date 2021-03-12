<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="neighborhood")
 * @ORM @Entity(repositoryClass="App\Models\Repository\NeighborhoodRepository")
 */
class Neighborhood
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
    private string $latitude;

    /**
     * @Column(type="string")
     */
    private string $longitude;

    /**
     * @ManyToOne(targetEntity="Cabinet")
     * @JoinColumn(name="cabinet", referencedColumnName="id")
     */
    private Cabinet $cabinet;

    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(Cabinet $cabinet): Neighborhood
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

    public function setName(string $name): Neighborhood
    {
        $this->name = $name;
        return $this;
    }

    public function getLatitude():string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): Neighborhood
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): Neighborhood
    {
        $this->longitude = $longitude;
        return $this;
    }
}