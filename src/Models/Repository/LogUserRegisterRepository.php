<?php

namespace App\Models\Repository;

use App\Models\Entities\LogUserRegister;
use Doctrine\ORM\EntityRepository;

class LogUserRegisterRepository extends EntityRepository
{
    public function save(LogUserRegister $entity):LogUserRegister
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

}