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
     * @ManyToOne(targetEntity="Deal")
     * @JoinColumn(name="deal", referencedColumnName="id")
     */
    private Deal $deal;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user", referencedColumnName="id")
     */
    private User $user;

    /**
     * @Column(type="boolean")
     */
    private bool $status;


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

    public function getDeal(): Deal
    {
        return $this->deal;
    }

    public function setDeal(Deal $deal): Task
    {
        $this->deal = $deal;
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

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): Task
    {
        $this->status = $status;
        return $this;
    }
}