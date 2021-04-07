<?php

namespace App\Models\Repository;

use App\Models\Entities\Message;
use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    public function save(Message $entity): Message
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    private function generateWhere($id = 0, $title = null, $active = null,  &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND message.id = :id";
        }
        if ($title) {
            $params[':title'] = "%$title%";
            $where .= " AND message.title LIKE :title";
        }
        if ($active > -1) {
            $params[':active'] = $active;
            $where .= " AND message.active = :active";
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

    public function list($id = 0, $title = null, $active = null, $limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $title, $active, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT message.title, message.description, message.id, message.active, users.name AS user, 
                DATE_FORMAT(message.date, '%d/%m/%Y') AS date, TIME_FORMAT(message.date, '%H:%i') AS time         
                FROM message
                JOIN users ON users.id = message.user
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
        $sql = "SELECT COUNT(message.id) AS total                  
                FROM message
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function messageDelete($id): array
    {
        $params = [];
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "DELETE FROM message
                WHERE message.id = {$id}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
}