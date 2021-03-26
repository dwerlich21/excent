<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="countries")
 */
class Countries
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
    private string $alpha2;

    /**
     * @Column(type="string")
     */
    private string $alpha3;


    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAlpha2(): string
    {
        return $this->alpha2;
    }

    public function getAlpha3(): string
    {
        return $this->alpha3;
    }

}