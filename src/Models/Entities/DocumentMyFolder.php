<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="documentMyFolder")
 * @ORM @Entity(repositoryClass="App\Models\Repository\DocumentMyFolderRepository")
 */
class DocumentMyFolder
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
     * @Column(type="datetime")
     */
    private \DateTime $created;

    /**
     * @Column(type="string")
     */
    private string $documentFile;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="responsible", referencedColumnName="id")
     */
    private User $responsible;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDocumentMyFolder(): string
    {
        return $this->documentFile;
    }

    public function setDocumentFile(string $documentFile): DocumentMyFolder
    {
        $this->documentFile = substr($documentFile, strrpos($documentFile, '/') + 1);
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): DocumentMyFolder
    {
        $this->title = $title;
        return $this;
    }

    public function getResponsible(): User
    {
        return $this->responsible;
    }

    public function setResponsible(User $responsible): DocumentMyFolder
    {
        $this->responsible = $responsible;
        return $this;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }
}