<?php

namespace App\Models\Repository;

use App\Models\Entities\Neighborhood;
use App\Models\Entities\User;
use Doctrine\ORM\EntityRepository;

class NeighborhoodRepository extends EntityRepository
{
    public function save(Neighborhood $entity): Neighborhood
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

    private function generateWhere($id = 0, $name = null, &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND neighborhood.id = :id";
        }
        if ($name) {
            $params[':name'] = "%$name%";
            $where .= " AND neighborhood.name LIKE :name";
        }
        return $where;
    }

    public function list(User $user, $id = 0, $name = null, $limit = null, $offset = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $name, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT neighborhood.id, neighborhood.name, neighborhood.latitude, neighborhood.longitude               
                FROM neighborhood
                WHERE neighborhood.cabinet = :cabinet {$where}
                ORDER BY name ASC {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal(User $user, $id = 0, $name = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $where = $this->generateWhere($id, $name, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(neighborhood.id) AS total                  
                FROM neighborhood
                WHERE neighborhood.cabinet = :cabinet {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

}