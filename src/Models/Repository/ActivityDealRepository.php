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

    private function generateWhere($deal, &$params): string
    {
        $where = '';
        if ($deal) {
            $params[':deal'] = $deal;
            $where .= " AND activityDeal.deal = :deal";
        }
        return $where;
    }

    public function list($deal = null, $limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($deal,  $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT activityDeal.id, activityDeal.type, activityDeal.activity, activityDeal.date AS dateTime,
                users.name AS user, DATE_FORMAT(activityDeal.date, '%d/%m/%Y') AS date, TIME_FORMAT(activityDeal.date, '%H:%i') 
                AS time, activityDeal.description               
                FROM activityDeal
                JOIN users ON users.id = activityDeal.user
                WHERE 1 = 1 {$where}
                ORDER BY dateTime DESC {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal($deal = null): array
    {
        $params = [];
        $where = $this->generateWhere($deal, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(activityDeal.id) AS total                  
                FROM activityDeal
                WHERE 1 = 1 {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    private function generateWhereTask($id = 0, string $date = null, &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND activityDeal.id = :id";
        }
        if ($date) {
            $params[':date'] = "%$date%";
            $where .= " AND activityDeal.date LIKE :date";
        }
        return $where;
    }

    private function generateWhereVerify($date = null, &$params): string
    {
        $where = '';
        if ($date) {
            $params[':date'] = "%$date%";
            $where .= " AND activityDeal.date LIKE :date";
        }
        return $where;
    }

    public function listDashboard($id = 0, User $user, $date = null, $limit = null, $offset = null): array
    {
        $params = [];
        $date = \DateTime::createFromFormat('Y-m-d', $date);
        $dateStr= $date->format('Y-m-d');
        $params[':user'] = $user->getId();
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhereTask($id, $dateStr, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(activityDeal.id) AS total, activityDeal.type, activityDeal.status           
                FROM activityDeal 
                WHERE activityDeal.user =  :user
                AND activityDeal.status = 1 
                AND activityDeal.type > 1 {$where}
                GROUP BY type {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function totalCalendar($id = 0, User $user, $date = null): array
    {
        $params = [];
        $params[':user'] = $user->getId();
        $where = $this->generateWhereTask($id, $date, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(activityDeal.id) AS total          
                FROM activityDeal 
                WHERE activityDeal.user =  :user
                AND activityDeal.status = 1 
                AND activityDeal.type > 1 {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function activityVerify(User $user, $date): array
    {
        $params = [];
        $params[':user'] = $user->getId();
        $where = $this->generateWhereVerify($date, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(activityDeal.id) AS total          
                FROM activityDeal 
                WHERE activityDeal.user =  :user
                AND activityDeal.status = 1 {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }
}