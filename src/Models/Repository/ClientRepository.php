<?php

namespace App\Models\Repository;

use App\Models\Entities\Client;
use Doctrine\ORM\EntityRepository;

class ClientRepository extends EntityRepository
{
    public function save(Client $entity): Client
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
            $where .= " AND client.id = :id";
        }
        if ($name) {
            $params[':name'] = "%$name%";
            $where .= " AND client.name LIKE :name";
        }
        if ($company) {
            $params[':company'] = "%$company%";
            $where .= " AND client.company LIKE :company";
        }
        if ($status > -1) {
            $params[':status'] = $status;
            $where .= " AND client.status = :status";
        }
        return $where;
    }

    public function list($id = 0, $name = null, $company= null, $status = null, $limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $name, $company, $status, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT client.id, client.name, client.company, client.status, client.phone, client.email, 
                client.status, client.office               
                FROM client
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
        $sql = "SELECT COUNT(client.id) AS total                  
                FROM client
                WHERE 1 = 1 {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

}