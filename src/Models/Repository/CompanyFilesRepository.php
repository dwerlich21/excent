<?php


namespace App\Models\Repository;


use App\Models\Entities\CompanyFiles;
use Doctrine\ORM\EntityRepository;

class CompanyFilesRepository extends EntityRepository
{
    public function save(CompanyFiles $entity): CompanyFiles
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }
}