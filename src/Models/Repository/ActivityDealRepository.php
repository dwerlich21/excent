<?php

namespace App\Models\Repository;

use App\Models\Entities\ActivityDeal;
use App\Models\Entities\User;
use Doctrine\ORM\EntityRepository;

class ActivityDealRepository extends EntityRepository
{
    public function save(ActivityDeal $entity): ActivityDeal
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

    private function generateWhere($id = 0, $deal = null, &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND activityDeal.id = :id";
        }
        if ($deal) {
            $params[':deal'] = $deal;
            $where .= " AND activityDeal.deal = :deal";
        }
        return $where;
    }

    public function list($id = 0, $deal = null, $limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $deal,  $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT activityDeal.id, activityDeal.type, activityDeal.activity, 
                users.name AS user, DATE_FORMAT(activityDeal.date, '%d/%m/%Y') AS date, TIME_FORMAT(activityDeal.time, '%H:%m') 
                AS time, activityDeal.description               
                FROM activityDeal
                JOIN users ON users.id = activityDeal.user
                WHERE 1 = 1 {$where}
                ORDER BY id DESC {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal($id = 0, $deal = null): array
    {
        $params = [];
        $where = $this->generateWhere($id, $deal, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(activityDeal.id) AS total                  
                FROM activityDeal
                WHERE 1 = 1 {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

}