<?php

namespace App\Models\Repository;

use App\Models\Entities\Document;
use Doctrine\ORM\EntityRepository;

class DocumentRepository extends EntityRepository
{
    public function save(Document $entity): Document
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    private function generateWhere($id = 0, $title = null, $type = null,  &$params): string
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

    public function list($id, $title = null, $type = null, $limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $title, $type, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT document.title, document.description, document.documentFile, document.id           
                FROM document
                WHERE 1 = 1 {$where}
                ORDER BY id DESC {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal($id, $title, $type): array
    {
        $params = [];
        $where = $this->generateWhere($id, $title, $type, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(document.id) AS total                  
                FROM document
                WHERE 1 = 1 {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function documentDelete($id): array
    {
        $params = [];
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "DELETE FROM document
                WHERE document.id = {$id}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

}