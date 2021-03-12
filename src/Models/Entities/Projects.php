<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
/**
 * @Entity @Table(name="projects")
 * @ORM @Entity(repositoryClass="App\Models\Repository\ProjectsRepository")
 */
class Projects
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="responsible", referencedColumnName="id")
     */
    private User $responsible;

    /**
     * @ManyToOne(targetEntity="TypeOfProject")
     * @JoinColumn(name="type", referencedColumnName="id")
     */
    private TypeOfProject $type;

    /**
     * @Column(type="datetime")
     */
    private \DateTime $date;

    /**
     * @Column(type="string")
     */
    private string $subjectMatter;

    /**
     * @Column(type="integer")
     */
    private int $status;

    /**
     * @Column(type="string", nullable=true)
     */
    private ?string $link = null;

    /**
     * @Column(type="string", nullable=true)
     */
    private ?string $projectFile;

    /**
     * @Column(type="text")
     */
    private string $description;

    /**
     * @Column(type="string", nullable=true)
     */
    private ?string $number = null;

    /**
     * @ManyToOne(targetEntity="Cabinet")
     * @JoinColumn(name="cabinet", referencedColumnName="id")
     */
    private Cabinet $cabinet;

    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(Cabinet $cabinet): Projects
    {
        $this->cabinet = $cabinet;
        return $this;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getResponsible(): User
    {
        return $this->responsible;
    }

    public function setResponsible(User $responsible): Projects
    {
        $this->responsible = $responsible;
        return $this;
    }

    public function getType(): TypeOfProject
    {
        return $this->type;
    }

    public function setType(TypeOfProject $type): Projects
    {
        $this->type = $type;
        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getSubjectMatter(): string
    {
        return $this->subjectMatter;
    }

    public function setSubjectMatter(string $subjectMatter): Projects
    {
        $this->subjectMatter = $subjectMatter;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): Projects
    {
        $this->status = $status;
        return $this;
    }

    public function getStatusStr(): string
    {
        switch ($this->status) {
            case 1:
                return 'Tramitando';
            case 2:
                return 'Arquivado';
            case 3:
                return 'Aprovado';
            default:
                return 'Desconhecido';
        }
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): Projects
    {
        $this->link = $link;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Projects
    {
        $this->description = $description;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): Projects
    {
        $this->number = $number;
        return $this;
    }

    public function getProjectFile(): ?string
    {
        return $this->projectFile;
    }

    public function setProjectFile(?string $projectFile): Projects
    {
        $this->projectFile = substr($projectFile, strrpos($projectFile, '/') + 1);
        return $this;
    }


}