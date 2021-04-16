<?php
//
//
//namespace App\Models\Entities;
//
//use Doctrine\ORM\EntityRepository;
//use Doctrine\ORM\Mapping as ORM;
//
///**
// * @Entity @Table(name="folderAcess")
// * @ORM @Entity(repositoryClass="App\Models\Repository\FolderAcessRepository")
// */
//class FolderAcess
//{
//    /**
//     * @Id @GeneratedValue @Column(type="integer")
//     */
//    private ?int $id = null;
//
//    /**
//     * @ManyToOne(targetEntity="User")
//     * @JoinColumn(name="user", referencedColumnName="id")
//     */
//    private User $user;
//
//    /**
//     * @ManyToOne(targetEntity="Folders")
//     * @JoinColumn(name="folder", referencedColumnName="id")
//     */
//    private Folders $folder;
//
//    /**
//     * @Column(type="boolean")
//     */
//    private bool $admin;
//
//    /**
//     * @Column(type="datetime")
//     */
//    private $created;
//
//    public function __construct()
//    {
//        $this->created = new \DateTime();
//    }
//
//    public function getId(): int
//    {
//        return $this->id;
//    }
//
//    public function getUser(): User
//    {
//        return $this->user;
//    }
//
//    public function setUser(User $user): FolderAcess
//    {
//        $this->user = $user;
//        return $this;
//    }
//
//    public function getAdmin(): bool
//    {
//        return $this->admin;
//    }
//
//    public function setAdmin(bool $admin): FolderAcess
//    {
//        $this->admin = $admin;
//        return $this;
//    }
//
//    public function getCreated(): \DateTime
//    {
//        return $this->created;
//    }
//
//    public function getFolder(): Folders
//    {
//        return $this->folder;
//    }
//
//    public function setFolder(Folders $folder): FolderAcess
//    {
//        $this->folder = $folder;
//        return $this;
//    }
//
//
//}