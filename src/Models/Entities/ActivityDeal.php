<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="activityDeal")
 * @ORM @Entity(repositoryClass="App\Models\Repository\ActivityDealRepository")
 */
class ActivityDeal
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
     * @Column(type="integer")
     */
    private int $type;

    /**
     * @Column(type="string")
     */
    private string $activity;

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

    public function setDate(\DateTime $date): ActivityDeal
    {
        $this->date = $date;
        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): ActivityDeal
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): ActivityDeal
    {
        $this->description = $description;
        return $this;
    }

    public function getDeal(): Deal
    {
        return $this->deal;
    }

    public function setDeal(Deal $deal): ActivityDeal
    {
        $this->deal = $deal;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): ActivityDeal
    {
        $this->user = $user;
        return $this;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): ActivityDeal
    {
        $this->status = $status;
        return $this;
    }

    public function getActivity(): string
    {
        return $this->activity;
    }

    public function setActivity(string $activity): ActivityDeal
    {
        $this->activity = $activity;
        return $this;
    }


}