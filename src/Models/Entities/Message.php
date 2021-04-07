<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="message")
 * @ORM @Entity(repositoryClass="App\Models\Repository\MessageRepository")
 */
class Message
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
     * @Column(type="boolean")
     */
    private bool $active;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user", referencedColumnName="id")
     */
    private User $user;

    /**
     * @Column(type="datetime")
     */
    private \DateTime $date;

    /**
     * @Column(type="string", nullable=true)
     */
    private ?string $documentFile = '';

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Message
    {
        $this->description = $description;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Message
    {
        $this->title = $title;
        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): Message
    {
        $this->active = $active;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Message
    {
        $this->user = $user;
        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getDocumentFile(): ?string
    {
        return $this->documentFile;
    }

    public function setDocumentFile(?string $documentFile): Message
    {
        $this->documentFile = substr($documentFile, strrpos($documentFile, '/') + 1);
        return $this;
    }
}