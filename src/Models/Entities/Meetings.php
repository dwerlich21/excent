<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="meetings")
 * @ORM @Entity(repositoryClass="App\Models\Repository\MeetingsRepository")
 */
class Meetings
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Column(type="datetime")
     */
    private ?\DateTime $start;

    /**
     * @Column(type="datetime", nullable=true, options={"default" : null})
     */
    private ?\DateTime $end = null;

    /**
     * @Column(type="integer")
     */
    private int $adress;

    /**
     * @Column(type="text")
     */
    private string $description;

    /**
     * @Column(type="string")
     */
    private string $contact = '';

    /**
     * @Column(type="string")
     */
    private string $email = '';

    /**
     * @Column(type="text")
     */
    private string $theme;

    /**
     * @Column(type="integer")
     */
    private int $type;

    /**
     * @ManyToOne(targetEntity="Cabinet")
     * @JoinColumn(name="cabinet", referencedColumnName="id")
     */
    private Cabinet $cabinet;

    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(Cabinet $cabinet): Meetings
    {
        $this->cabinet = $cabinet;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStart(): \DateTime
    {
        return $this->start;
    }

    public function setStart(\DateTime $start): Meetings
    {
        $this->start = $start;
        return $this;
    }

    public function getAdress(): int
    {
        return $this->adress;
    }

    public function setAdress(int $adress): Meetings
    {
        $this->adress = $adress;
        return $this;
    }

    public function getAdressStr()
    {
        switch ($this->adress) {
            case 1:
                return 'Gabinete';
            case 2:
                return 'Câmara';
            case 3:
                return 'Prefeitura';
            case 4:
                return 'Externo';
            default:
                return 'Desconhecido';
        }
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Meetings
    {
        $this->description = $description;
        return $this;
    }

    public function getContact(): string
    {
        return $this->contact;
    }

    public function setContact(string $contact): Meetings
    {
        $this->contact = $contact;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Meetings
    {
        $this->email = $email;
        return $this;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): Meetings
    {
        $this->theme = $theme;
        return $this;
    }

    public function getEnd(): ?\DateTime
    {
        return $this->end;
    }

    public function setEnd(?\DateTime $end): Meetings
    {
        $this->end = $end;
        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type):Meetings
    {
        $this->type = $type;
        return $this;
    }

    public function getTypeStr()
    {
     switch ($this->type) {
         case 1:
             return 'Reunião';
         case 2:
             return 'Congresso';
         case 3:
             return 'Sessão';
         default:
             return 'Desconhecido';
     }
    }

}