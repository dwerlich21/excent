<?php

namespace App\Models\Repository;

use App\Models\Entities\Deal;
use Doctrine\ORM\EntityRepository;

class DealRepository extends EntityRepository
{
    public function save(Deal $entity): Deal
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

    private function generateWhere($id = 0, $name = null, $company= null, $status = null, &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND deal.id = :id";
        }
        if ($name) {
            $params[':name'] = "%$name%";
            $where .= " AND deal.name LIKE :name";
        }
        if ($company) {
            $params[':company'] = "%$company%";
            $where .= " AND deal.company LIKE :company";
        }
        if ($status > -1) {
            $params[':status'] = $status;
            $where .= " AND deal.status = :status";
        }
        return $where;
    }

    public function list($id = 0, $name = null, $company= null, $status = null, $limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $name, $company, $status, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT deal.id, deal.name, deal.company, deal.status, deal.phone, deal.email, 
                deal.status, deal.office               
                FROM deal
                WHERE 1 = 1 {$where}
                ORDER BY name ASC {$limitSql}
               ";

        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal($id = 0, $name = null, $company= null, $status = null): array
    {
        $params = [];
        $where = $this->generateWhere($id, $name, $company, $status, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(deal.id) AS total                  
                FROM deal
                WHERE 1 = 1 {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

}