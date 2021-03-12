<?php

namespace App\Models\Repository;

use App\Models\Entities\AccessLog;
use Doctrine\ORM\EntityRepository;

class AccessLogRepository extends EntityRepository
{
    public function save(AccessLog $entity):AccessLog
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

}