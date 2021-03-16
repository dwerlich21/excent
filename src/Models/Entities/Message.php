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


}