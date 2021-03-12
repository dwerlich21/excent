<?php

namespace App\Models\Repository;

use App\Models\Entities\CabinetDetails;
use Doctrine\ORM\EntityRepository;

class CabinetDetailsRepository extends EntityRepository
{
    public function save(CabinetDetails $entity): CabinetDetails
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

}