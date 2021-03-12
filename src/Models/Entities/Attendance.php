<?php


namespace App\Models\Entities;

use App\Helpers\Utils;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @Entity @Table(name="attendance")
 * @ORM @Entity(repositoryClass="App\Models\Repository\AttendanceRepository")
 */
class Attendance
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ManyToOne(targetEntity="TypeOfService")
     * @JoinColumn(name="type", referencedColumnName="id")
     */
    private TypeOfService $type;

    /**
     * @ManyToOne(targetEntity="Institution")
     * @JoinColumn(name="institution", referencedColumnName="id")
     */
    private Institution $institution;

    /**
     * @ManyToOne(targetEntity="Neighborhood")
     * @JoinColumn(name="neighborhood", referencedColumnName="id")
     */
    private Neighborhood $neighborhood;

    /**
     * @Column(type="datetime")
     */
    private \DateTime $date;

    /**
     * @Column(type="string")
     */
    private string $reporting;

    /**
     * @Column(type="string")
     */
    private string $email;

    /**
     * @Column(type="string", nullable=true)
     */
    private ?string $attendanceFile = '';

    /**
     * @Column(type="text")
     */
    private string $description;

    /**
     * @Column(type="integer")
     */
    private int $status;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="responsible", referencedColumnName="id")
     */
    private User $responsible;

    /**
     * @ManyToOne(targetEntity="Cabinet")
     * @JoinColumn(name="cabinet", referencedColumnName="id")
     */
    private Cabinet $cabinet;

    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(Cabinet $cabinet): Attendance
    {
        $this->cabinet = $cabinet;
        return $this;
    }


    public function getId()
    {
        return $this->id;
    }

    public function getType(): TypeOfService
    {
        return $this->type;
    }

    public function setType(TypeOfService $type): Attendance
    {
        $this->type = $type;
        return $this;
    }

    public function getInstitution(): Institution
    {
        return $this->institution;
    }

    public function setInstitution(Institution $institution): Attendance
    {
        $this->institution = $institution;
        return $this;
    }

    public function getNeighborhood(): Neighborhood
    {
        return $this->neighborhood;
    }

    public function setNeighborhood(Neighborhood $neighborhood): Attendance
    {
        $this->neighborhood = $neighborhood;
        return $this;
    }

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getReporting(): string
    {
        return $this->reporting;
    }

    public function setReporting(string $reporting): Attendance
    {
        $this->reporting = $reporting;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Attendance
    {
        $this->email = $email;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Attendance
    {
        $this->description = $description;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): Attendance
    {
        $this->status = $status;
        return $this;
    }

    public function getResponsible(): User
    {
        return $this->responsible;
    }

    public function setResponsible(User $responsible): Attendance
    {
        $this->responsible = $responsible;
        return $this;
    }

    public function getAttendanceFile(): ?string
    {
        return $this->attendanceFile;
    }

    public function setAttendanceFile(?string $attendanceFile): Attendance
    {
        $this->attendanceFile = substr($attendanceFile, strrpos($attendanceFile, '/') + 1);
        return $this;
    }

    public function getStatusStr()
    {
        switch ($this->status) {
            case 1:
                return'Pendente';
            case 2:
                return'Resolvido';
            case 3:
                return'Respondido/Não Atendido';
            default:
                return 'Desconhecido';
        }
    }

}