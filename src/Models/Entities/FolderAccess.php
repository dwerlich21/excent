<?php


namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="folderAccess")
 * @ORM @Entity(repositoryClass="App\Models\Repository\FolderAccessRepository")
 */
class FolderAccess
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user", referencedColumnName="id")
     */
    private User $user;

    /**
     * @ManyToOne(targetEntity="CompanyFiles")
     * @JoinColumn(name="folder", referencedColumnName="id")
     */
    private CompanyFiles $folder;

    /**
     * @Column(type="boolean")
     */
    private bool $admin;

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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): FolderAccess
    {
        $this->user = $user;
        return $this;
    }

    public function getAdmin(): bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): FolderAccess
    {
        $this->admin = $admin;
        return $this;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function getFolder(): CompanyFiles
    {
        return $this->folder;
    }

    public function setFolder(CompanyFiles $folder): FolderAccess
    {
        $this->folder = $folder;
        return $this;
    }


}