<?php


namespace App\Models\Entities;

use App\Helpers\Utils;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @Entity @Table(name="docs")
 * @ORM @Entity(repositoryClass="App\Models\Repository\DocsRepository")
 */
class Docs
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Column(type="string")
     */
    private string $number = '';

    /**
     * @ManyToOne(targetEntity="DocumentType")
     * @JoinColumn(name="type", referencedColumnName="id")
     */
    private DocumentType $type;

    /**
     * @ManyToOne(targetEntity="Attendance")
     * @JoinColumn(name="attendance", referencedColumnName="id", nullable=true)
     */
    private ?Attendance $attendance = null;

    /**
     * @ManyToOne(targetEntity="Institution")
     * @JoinColumn(name="institution", referencedColumnName="id", nullable=true)
     */
    private ?Institution $institution = null;

    /**
     * @ManyToOne(targetEntity="Cabinet")
     * @JoinColumn(name="cabinet", referencedColumnName="id")
     */
    private Cabinet $cabinet;

    /**
     * @ManyToOne(targetEntity="Meetings")
     * @JoinColumn(name="meetings", referencedColumnName="id", nullable=true)
     */
    private ?Meetings $meeting = null;

    /**
     * @Column(type="string")
     */
    private string $subjectMatter;

    /**
     * @Column(type="datetime")
     */
    private \DateTime $date;

    /**
     * @Column(type="string")
     */
    private string $recipient;

    /**
     * @Column(type="text")
     */
    private string $description;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="responsible", referencedColumnName="id")
     */
    private User $responsible;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getResponsible(): User
    {
        return $this->responsible;
    }

    public function setResponsible(User $responsible): Docs
    {
        $this->responsible = $responsible;
        return $this;
    }

    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(Cabinet $cabinet): Docs
    {
        $this->cabinet = $cabinet;
        return $this;
    }


    public function getId()
    {
        return $this->id;
    }

    public function getType(): DocumentType
    {
        return $this->type;
    }

    public function setType(DocumentType $type): Docs
    {
        $this->type = $type;
        return $this;
    }

    public function getInstitution(): ?Institution
    {
        return $this->institution;
    }

    public function setInstitution(?Institution $institution): Docs
    {
        $this->institution = $institution;
        return $this;
    }

    public function getSubjectMatter():string
    {
        return $this->subjectMatter;
    }

    public function setSubjectMatter(string $subjectMatter): Docs
    {
        $this->subjectMatter = $subjectMatter;
        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function setRecipient(string $recipient): Docs
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function getMeeting(): ?Meetings
    {
        return $this->meeting;
    }

    public function setMeeting(?Meetings $meeting): Docs
    {
        $this->meeting = $meeting;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Docs
    {
        $this->description = $description;
        return $this;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): Docs
    {
        $this->number = $number;
        return $this;
    }

    public function getAttendance(): ?Attendance
    {
        return $this->attendance;
    }

    public function setAttendance(?Attendance $attendance): Docs
    {
        $this->attendance = $attendance;
        return $this;
    }


}