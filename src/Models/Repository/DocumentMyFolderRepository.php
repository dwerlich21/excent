<?php

namespace App\Models\Repository;

use App\Models\Entities\DocumentMyFolder;
use Doctrine\ORM\EntityRepository;

class DocumentMyFolderRepository extends EntityRepository
{
    public function save(DocumentMyFolder $entity): DocumentMyFolder
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    private function generateWhere($responsible, $title = null,  &$params): string
    {
        $where = '';

        if ($title) {
            $params[':title'] = "%$title%";
            $where .= " AND documentMyFolder.title LIKE :title";
        }
        if ($responsible) {
            $params[':responsible'] = $responsible;
            $where .= " AND documentMyFolder.responsible = :responsible";
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

    public function list($responsible, $title = null, $limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($responsible, $title, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT documentMyFolder.title, documentMyFolder.documentFile, documentMyFolder.id, 
                DATE_FORMAT(documentMyFolder.created, '%d/%m/%Y') AS date, TIME_FORMAT(documentMyFolder.created, '%H:%i') AS time           
                FROM documentMyFolder
                WHERE 1 = 1 {$where}
                ORDER BY id DESC {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal($responsible, $title): array
    {
        $params = [];
        $where = $this->generateWhere($responsible, $title, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(documentMyFolder.id) AS total                  
                FROM documentMyFolder
                WHERE 1 = 1 {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function delDocument($id): array
    {
        $params = [];
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "DELETE FROM documentMyFolder
                WHERE documentMyFolder.id = {$id} 
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
}