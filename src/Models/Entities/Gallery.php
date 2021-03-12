<?php


namespace App\Models\Entities;

use App\Helpers\Utils;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @Entity @Table(name="gallery")
 * @ORM @Entity(repositoryClass="App\Models\Repository\GalleryRepository")
 */
class Gallery
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Column(type="string")
     */
    private string $imgFile;

    /**
     * @ManyToOne(targetEntity="Cabinet")
     * @JoinColumn(name="cabinet", referencedColumnName="id")
     */
    private Cabinet $cabinet;

    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(Cabinet $cabinet): Gallery
    {
        $this->cabinet = $cabinet;
        return $this;
    }


    public function getId()
    {
        return $this->id;
    }

    public function getImgFile(): string
    {
        return $this->imgFile;
    }

    public function setImgFile(string $imgFile): Gallery
    {
        $this->imgFile = substr($imgFile, strrpos($imgFile, '/') + 1);
        return $this;
    }
}