<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="companyFiles")
 * @ORM @Entity(repositoryClass="App\Models\Repository\CompanyFilesRepository")
 */
class CompanyFiles
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
     * @Column(type="string")
     */
    private string $name;

    /**
     * @Column(type="string")
     */
    private string $folder;

    /**
     * @Column(type="datetime")
     */
    private $created;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getResponsible(): User
    {
        return $this->responsible;
    }

    public function setResponsible(User $responsible): CompanyFiles
    {
        $this->responsible = $responsible;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): CompanyFiles
    {
        $this->name = $name;
        return $this;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function setFolder(string $folder): CompanyFiles
    {
        $this->folder = $folder;
        return $this;
    }


}