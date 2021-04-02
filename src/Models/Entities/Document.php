<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="document")
 * @ORM @Entity(repositoryClass="App\Models\Repository\DocumentRepository")
 */
class Document
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Column(type="string")
     */
    private string $title;

    /**
     * @Column(type="text")
     */
    private string $description;

    /**
     * @Column(type="datetime")
     */
    private \DateTime $created;

    /**
     * @Column(type="string")
     */
    private string $documentFile;

    /**
     * @ManyToOne(targetEntity="DocumentCategory")
     * @JoinColumn(name="type", referencedColumnName="id")
     */
    private DocumentCategory $type;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="responsible", referencedColumnName="id")
     */
    private User $responsible;

    
    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Document
    {
        $this->description = $description;
        return $this;
    }

    public function getDocumentFile(): string
    {
        return $this->documentFile;
    }

    public function setDocumentFile(string $documentFile): Document
    {
        $this->documentFile = substr($documentFile, strrpos($documentFile, '/') + 1);
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Document
    {
        $this->title = $title;
        return $this;
    }

    public function getType(): DocumentCategory
    {
        return $this->type;
    }

    public function setType(DocumentCategory $type): Document
    {
        $this->type = $type;
        return $this;
    }

    public function getResponsible(): User
    {
        return $this->responsible;
    }

    public function setResponsible(User $responsible): Document
    {
        $this->responsible = $responsible;
        return $this;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): Document
    {
        $this->created = $created;
        return $this;
    }


}