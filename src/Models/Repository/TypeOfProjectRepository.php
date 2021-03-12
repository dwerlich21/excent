<?php

namespace App\Models\Repository;

use App\Models\Entities\TypeOfProject;
use App\Models\Entities\User;
use Doctrine\ORM\EntityRepository;

class TypeOfProjectRepository extends EntityRepository
{
    public function save(TypeOfProject $entity): TypeOfProject
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

    private function generateWhere($id = 0, &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND typeOfProject.id = :id";
        }
        return $where;
    }

    public function list(User $user, $id = 0, $cabinet = null, $limit = null, $offset = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT typeOfProject.id, typeOfProject.name, typeOfProject.active              
                FROM typeOfProject
                WHERE typeOfProject.cabinet = :cabinet {$where}
                ORDER BY name ASC {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal(User $user, $id = 0): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $where = $this->generateWhere($id, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(typeOfProject.id) AS total                  
                FROM typeOfProject
                WHERE typeOfProject.cabinet = :cabinet {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

}