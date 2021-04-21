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

    private function generateWhere($id = 0, $status = null, $responsible = null, $manager = null, $name = null, $country = null, &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND deal.id = :id";
        }
        if ($responsible) {
            $params[':responsible'] = $responsible;
            $where .= " AND deal.responsible = :responsible";
        }
        if ($manager) {
            $params[':manager'] = $manager;
            $where .= " AND manager = :manager";
        }
        if ($status) {
            $params[':status'] = $status;
            $where .= " AND deal.status = :status";
        }
        if ($name) {
            $params[':name'] = "%$name%";
            $where .= " AND deal.name LIKE :name";
        }
        if ($country) {
            $params[':country'] = $country;
            $where .= " AND deal.country LIKE :country";
        }
        return $where;
    }

    public function list($id = 0, $status = null, $responsible = null, $manager = null, $name = null, $country = null, $limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $status, $responsible, $manager, $name, $country, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT 
                    (SELECT manager.name FROM users AS manager WHERE manager.id =  users.manager) AS manager,
                    deal.id, deal.name, deal.company, deal.status, deal.phone, deal.email, deal.responsible, 
                    deal.status, deal.office               
                FROM deal
                JOIN users ON users.id = deal.responsible
                WHERE 1 = 1 AND deal.type = 1 {$where}
                ORDER BY name ASC {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listLead($id = 0, $name = null, $country = null, $limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhereLead($id, $name, $country, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT deal.id, deal.status, deal.phone, deal.email, deal.name, deal.email, 
                deal.status, deal.office, users.name AS user, countries.name AS country, deal.type, deal.country AS countryID               
                FROM deal
                JOIN users ON users.id = deal.responsible
                JOIN countries ON countries.id = deal.country
                WHERE 1 = 1 AND deal.type = 0 {$where}
                ORDER BY deal.name ASC {$limitSql}
               ";

        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotalLead($id = 0, $name = null, $country = null): array
    {
        $params = [];
        $where = $this->generateWhereLead($id, $name, $country, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(deal.id) AS total                  
                FROM deal
                WHERE 1 = 1 AND deal.type = 0 {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function dealDelete($id): array
    {
        $params = [];
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "DELETE FROM activityDeal WHERE activityDeal.deal = {$id};
                DELETE FROM deal 
                WHERE deal.id = {$id}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    private function generateWhereLead($id = 0, $name = null, $country = null, &$params): string
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
        if ($country) {
            $params[':country'] = $country;
            $where .= " AND deal.country LIKE :country";
        }
        return $where;
    }
}