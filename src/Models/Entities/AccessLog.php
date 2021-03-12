<?php
/**
 * Created by PhpStorm.
 * User: rwerl
 * Date: 16/04/2019
 * Time: 22:52
 */

namespace App\Models\Entities;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity @Table(name="accessLog")
 * @ORM @Entity(repositoryClass="App\Models\Repository\AccessLogRepository")
 */
class AccessLog
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user", referencedColumnName="id")
     * @var User
     */
    private $user;

    /**
     * @Column(type="string")
     * @var string
     */
    private $ip;

    /**
     * @Column(type="string")
     * @var string
     */
    private $device;

    /**
     * @Column(type="string")
     * @var string
     */
    private $version;

    /**
     * @Column(type="string")
     * @var string
     */
    private $so;

    /**
     * @Column(type="datetime")
     * @var \DateTime
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

    public function setUser(User $user): AccessLog
    {
        $this->user = $user;
        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): AccessLog
    {
        $this->ip = $ip;
        return $this;
    }

    public function getDevice(): string
    {
        return $this->device;
    }

    public function setDevice(string $device): AccessLog
    {
        $this->device = $device;
        return $this;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }


    public function getSo(): string
    {
        return $this->so;
    }

    public function setSo(string $so): AccessLog
    {
        $this->so = $so;
        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): AccessLog
    {
        $this->version = $version;
        return $this;
    }

}