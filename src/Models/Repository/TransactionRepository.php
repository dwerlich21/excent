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

    private function generateWhere($id = 0, $user = null, $country = null, &$params): string
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
        $sql = "SELECT responsible.name AS responsible, users.name AS user, transaction.id, transaction.withdrawals, 
                DATE_FORMAT(transaction.date, '%d/%m/%Y') AS date, countries.name AS country, 
                transaction.user AS userId, transaction.deposit        
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

    public function rankingMaster($start, $end): array
    {
        $params = [];
        $start = \DateTime::createFromFormat('Y-m-d H:i:s', $start);
        $startStr = $start->format('Y-m-d H:i:s');
        $end = \DateTime::createFromFormat('Y-m-d H:i:s', $end);
        $endStr = $end->format('Y-m-d H:i:s');
        $where = $this->generateWhereDashboard($startStr, $endStr, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT SUM(transaction.withdrawals) AS totalCapture, 
                SUM(transaction.deposit) AS totalDeposit,
                (SUM(transaction.deposit) - SUM(transaction.withdrawals)) AS marginIn
                FROM transaction
                WHERE 1 = 1 {$where}
                ORDER BY marginIn DESC
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function rankingAdvisors($start, $end): array
    {
        $params = [];
        $start = \DateTime::createFromFormat('Y-m-d H:i:s', $start);
        $startStr = $start->format('Y-m-d H:i:s');
        $end = \DateTime::createFromFormat('Y-m-d H:i:s', $end);
        $endStr = $end->format('Y-m-d H:i:s');
        $where = $this->generateWhereDashboard($startStr, $endStr, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(transaction.id) AS accounts, SUM(transaction.withdrawals) AS totalCapture, 
                SUM(transaction.deposit) AS totalDeposit, users.id AS userId,
                (SUM(transaction.deposit) - SUM(transaction.withdrawals)) AS marginIn, countries.flag AS country, 
                users.name AS user
                FROM transaction
                JOIN users ON users.id = transaction.user
                JOIN countries ON countries.id = transaction.country
                WHERE 1 = 1 {$where}
                GROUP BY user, country
                ORDER BY marginIn DESC
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function rankingManagersGroup($id, $start, $end): array
    {
        $params = [];
        $start = \DateTime::createFromFormat('Y-m-d H:i:s', $start);
        $startStr = $start->format('Y-m-d H:i:s');
        $end = \DateTime::createFromFormat('Y-m-d H:i:s', $end);
        $endStr = $end->format('Y-m-d H:i:s');
        $where = $this->generateWhereDashboard($startStr, $endStr, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(transaction.id) AS accounts, SUM(transaction.withdrawals) AS totalCapture, 
                SUM(transaction.deposit) AS totalDeposit,
                (SUM(transaction.deposit) - SUM(transaction.withdrawals)) AS marginIn, countries.flag AS country, users.name AS user                  
                FROM transaction
                JOIN users ON users.id = transaction.user
                JOIN countries ON countries.id = transaction.country
                WHERE users.manager = {$id} {$where}
                GROUP BY user, country
                ORDER BY marginIn DESC
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function rankingManagers($start, $end): array
    {
        $params = [];
        $start = \DateTime::createFromFormat('Y-m-d H:i:s', $start);
        $startStr = $start->format('Y-m-d H:i:s');
        $end = \DateTime::createFromFormat('Y-m-d H:i:s', $end);
        $endStr = $end->format('Y-m-d H:i:s');
        $where = $this->generateWhereDashboard($startStr, $endStr, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT
                    (SELECT manager.name FROM users AS manager WHERE manager.id =  users.manager) AS user,
                    (SELECT manager.id FROM users AS manager WHERE manager.id =  users.manager) AS userId,
                    (SELECT countries.flag FROM countries 
                     JOIN users AS manager ON countries.id = manager.country
                     WHERE manager.id =  users.manager) AS country,
                    COUNT(transaction.id) AS accounts, 
                    SUM(transaction.withdrawals) AS totalCapture, 
                    SUM(transaction.deposit) AS totalDeposit,
                    (SUM(transaction.deposit) - SUM(transaction.withdrawals)) AS marginIn                 
                FROM transaction
                JOIN users ON users.id = transaction.user
                JOIN countries ON countries.id = transaction.country
                WHERE 1 = 1 {$where}
                GROUP BY users.manager, country
                ORDER BY marginIn desc
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function generateWhereDashboard($startStr, $endStr, &$params)
    {
        $where = '';

        if ($startStr) {
            $params[':startStr'] = $startStr;
            $where .= " AND transaction.date >= :startStr";
        }
        if ($endStr) {
            $params[':endStr'] = $endStr;
            $where .= " AND transaction.date < :endStr";
        }
        return $where;
    }
}