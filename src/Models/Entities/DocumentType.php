<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="documentType")
 * @ORM @Entity(repositoryClass="App\Models\Repository\DocumentTypeRepository")
 */
class DocumentType
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

    /**
     * @ManyToOne(targetEntity="Cabinet")
     * @JoinColumn(name="cabinet", referencedColumnName="id")
     */
    private Cabinet $cabinet;

    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(Cabinet $cabinet): DocumentType
    {
        $this->cabinet = $cabinet;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): DocumentType
    {
        $this->active = $active;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): DocumentType
    {
        $this->name = $name;
        return $this;
    }
}