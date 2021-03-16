<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="task")
 * @ORM @Entity(repositoryClass="App\Models\Repository\TaskRepository")
 */
class Task
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Column(type="date")
     */
    private \DateTime $date;

    /**
     * @Column(type="time", nullable=true)
     */
    private ?\DateTime $time = null;

    /**
     * @Column(type="integer")
     */
    private int $action;

    /**
     * @Column(type="string", nullable=true)
     */
    private ?string $description = null;

    /**
     * @ManyToOne(targetEntity="Client")
     * @JoinColumn(name="client", referencedColumnName="id")
     */
    private Client $client;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user", referencedColumnName="id")
     */
    private User $user;


    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): Task
    {
        $this->date = $date;
        return $this;
    }

    public function getAction(): int
    {
        return $this->action;
    }

    public function setAction(int $action): Task
    {
        $this->action = $action;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Task
    {
        $this->description = $description;
        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): Task
    {
        $this->client = $client;
        return $this;
    }

    public function getTime(): ?\DateTime
    {
        return $this->time;
    }

    public function setTime(?\DateTime $time): Task
    {
        $this->time = $time;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Task
    {
        $this->user = $user;
        return $this;
    }


}