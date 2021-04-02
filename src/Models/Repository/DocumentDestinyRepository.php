<?php

namespace App\Models\Repository;

use App\Models\Entities\DocumentDestiny;
use Doctrine\ORM\EntityRepository;

class DocumentDestinyRepository extends EntityRepository
{
    public function save(DocumentDestiny $entity): DocumentDestiny
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    private function generateWhere($id = 0, $destiny, $title = null, $type = null,  &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND document.id = :id";
        }
        if ($title) {
            $params[':title'] = "%$title%";
            $where .= " AND document.title LIKE :title";
        }
        if ($type) {
            $params[':type'] = $type;
            $where .= " AND document.type = :type";
        }
        if ($destiny) {
            $params[':destiny'] = $destiny;
            $where .= " AND documentDestiny.destiny = :destiny";
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

    public function list($id, $destiny, $title = null, $type = null, $limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $destiny, $title, $type, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT document.title AS title, document.description AS description, 
                document.documentFile AS documentFile, documentDestiny.id, document.type AS type, documentDestiny.status,
                DATE_FORMAT(document.created, '%d/%m/%Y') AS date, TIME_FORMAT(document.created, '%H:%i') AS time
                FROM documentDestiny
                JOIN document ON document.id = documentDestiny.document
                WHERE 1 = 1 {$where}
                ORDER BY id DESC {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal($id, $destiny, $title, $type): array
    {
        $params = [];
        $where = $this->generateWhere($id, $destiny, $title, $type, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(documentDestiny.id) AS total                  
                FROM documentDestiny
                JOIN document ON document.id = documentDestiny.document
            
                WHERE 1 = 1 {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
}
