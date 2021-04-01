<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="documentCategory")
 * @ORM @Entity(repositoryClass="App\Models\Repository\DocumentCategoryRepository")
 */
class DocumentCategory
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Column(type="string")
     */
    private string $nameCategory;

    /**
     * @Column(type="boolean")
     */
    private bool $active;


    public function getId(): int
    {
        return $this->id;
    }

    public function getNameCategory(): string
    {
        return $this->nameCategory;
    }

    public function setNameCategory(string $nameCategory): DocumentCategory
    {
        $this->nameCategory = $nameCategory;
        return $this;
    }

    public function getActive():bool
    {
        return $this->active;
    }

    public function setActive(bool $active): DocumentCategory
    {
        $this->active = $active;
        return $this;
    }


}