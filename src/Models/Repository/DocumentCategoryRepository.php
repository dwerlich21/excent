<?php

namespace App\Models\Repository;

use App\Models\Entities\DocumentCategory;
use Doctrine\ORM\EntityRepository;

class DocumentCategoryRepository extends EntityRepository
{
    public function save(DocumentCategory $entity): DocumentCategory
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

    public function list($limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT documentCategory.id, documentCategory.nameCategory, documentCategory.active          
                FROM documentCategory
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
        $sql = "SELECT COUNT(documentCategory.id) AS total                  
                FROM documentCategory
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function categoryDelete($id): array
    {
        $params = [];
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "DELETE FROM documentCategory
                WHERE documentCategory.id = {$id}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

}