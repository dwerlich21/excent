<?php

namespace App\Models\Repository;

use App\Models\Entities\Cabinet;
use Doctrine\ORM\EntityRepository;

class CabinetRepository extends EntityRepository
{
    public function save(Cabinet $entity): Cabinet
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

    private function generateWhere($id = 0, $name = null, $active = null,&$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND directory.id = :id";
        }
        if ($name) {
            $params[':name'] = "%$name%";
            $where .= " AND directory.name LIKE :name";
        }
        if ($active) {
            $params[':active'] = $active;
            $where .= " AND directory.active = :active";
        }
        return $where;
    }

    public function list($id = 0, $name = null, $active = null, $limit = null, $offset = null): array
    {
        $params = [];$params[':cabinet'] = $cabinet;
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $name, $active, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT directory.id, directory.name, directory.active               
                FROM directory
                WHERE attendance.cabinet = :cabinet {$where}
                ORDER BY name ASC {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal($id = 0, $name = null, $active = null): array
    {
        $params = [];$params[':cabinet'] = $cabinet;
        $where = $this->generateWhere($id, $name, $active, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(directory.id) AS total                  
                FROM directory
                WHERE attendance.cabinet = :cabinet {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

}