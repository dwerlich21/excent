<?php

namespace App\Models\Repository;

use App\Models\Entities\Task;
use App\Models\Entities\User;
use Doctrine\ORM\EntityRepository;

class TaskRepository extends EntityRepository
{
    public function save(Task $entity): Task
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    private function generateLimit($limit = null, $offset = null): string
    {
        $limitSql = '';
        if ($limit) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $limitSql = " LIMIT {$limit} OFFSET {$offset}";
        }
        return $limitSql;
    }



}