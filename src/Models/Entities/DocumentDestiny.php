<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="documentDestiny")
 * @ORM @Entity(repositoryClass="App\Models\Repository\DocumentDestinyRepository")
 */
class DocumentDestiny
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;


    /**
     * @Column(type="boolean")
     */
    private bool $status;

    /**
     * @ManyToOne(targetEntity="Document")
     * @JoinColumn(name="document", referencedColumnName="id")
     */
    private Document $document;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="destiny", referencedColumnName="id")
     */
    private User $destiny;


    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus():bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): DocumentDestiny
    {
        $this->status = $status;
        return $this;
    }

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function setDocument(Document $document): DocumentDestiny
    {
        $this->document = $document;
        return $this;
    }

    public function getDestiny(): User
    {
        return $this->destiny;
    }

    public function setDestiny(User $destiny): DocumentDestiny
    {
        $this->destiny = $destiny;
        return $this;
    }


}