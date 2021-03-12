<?php

namespace App\Models\Repository;

use App\Models\Entities\Gallery;
use Doctrine\ORM\EntityRepository;

class GalleryRepository extends EntityRepository
{
    public function save(Gallery $entity): Gallery
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

}