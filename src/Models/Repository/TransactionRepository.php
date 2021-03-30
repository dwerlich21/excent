<?php

namespace App\Models\Repository;

use App\Models\Entities\Transaction;
use Doctrine\ORM\EntityRepository;

class TransactionRepository extends EntityRepository
{
    public function save(Transaction $entity): Transaction
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    private function generateWhere($id = 0, $user = null, $country = null,  &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND transaction.id = :id";
        }
        if ($user) {
            $params[':user'] = $user;
            $where .= " AND transaction.user LIKE :user";
        }
        if ($country) {
            $params[':country'] = $country;
            $where .= " AND transaction.country = :country";
        }
        return $where;
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

    public function list($id = 0, $user = null, $country = null, $limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $user, $country, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT responsible.name AS responsible, users.name AS user, transaction.id, transaction.value, 
                DATE_FORMAT(transaction.date, '%d/%m/%Y') AS date, countries.name AS country, transaction.user AS userId        
                FROM transaction
                JOIN users ON users.id = transaction.user
                JOIN countries ON countries.id = transaction.country
                JOIN users AS responsible ON responsible.id = transaction.responsible
                WHERE 1 = 1 {$where}
                ORDER BY id DESC {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal(): array
    {
        $params = [];
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(transaction.id) AS total                  
                FROM transaction
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function transactionsDelete($id): array
    {
        $params = [];
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "DELETE FROM transaction
                WHERE transaction.id = {$id}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function rankingAdvisors($limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(transaction.id) AS accounts, SUM(transaction.value) AS totalCapture, 
                countries.name AS country, users.name AS user                  
                FROM transaction
                JOIN users ON users.id = transaction.user
                JOIN countries ON countries.id = transaction.country
                GROUP BY user, country, user
                ORDER BY totalCapture DESC {$limitSql}
               ";

        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
}