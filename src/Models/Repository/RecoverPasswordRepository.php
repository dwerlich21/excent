<?php

namespace App\Models\Repository;

use App\Models\Entities\RecoverPassword;
use Doctrine\ORM\EntityRepository;

class RecoverPasswordRepository extends EntityRepository
{
    public function save(RecoverPassword $entity):RecoverPassword
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

}