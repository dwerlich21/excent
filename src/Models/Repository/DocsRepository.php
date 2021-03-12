<?php

namespace App\Models\Repository;

use App\Models\Entities\Docs;
use App\Models\Entities\User;
use Doctrine\ORM\EntityRepository;

class DocsRepository extends EntityRepository
{
    public function save(Docs $entity): Docs
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

    private function generateWhere($id = 0, $type = null, $responsible = null, &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND docs.id = :id";
        }
        if ($type) {
            $params[':type'] = $type;
            $where .= " AND docs.type = :type";
        }
        if ($responsible) {
            $params[':responsible'] = $responsible;
            $where .= " AND docs.responsible = :responsible";
        }
        return $where;
    }

    public function list(User $user, $id = 0, $type = null, $responsible = null, $limit = null, $offset = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $type, $responsible, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT docs.id, DATE_FORMAT(docs.date, '%d/%m/%Y') AS date, d.name AS type, users.name AS user, 
                docs.description, docs.subjectMatter, docs.number                 
                FROM docs
                JOIN documentType AS d ON docs.type = d.id
                JOIN users ON docs.responsible = users.id
                WHERE docs.cabinet = :cabinet {$where}
                ORDER BY id DESC {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function number(User $user, $type, $year)
{
    $params = []; $params[':cabinet'] = $user->getCabinet()->getId();
    $where = $this->generateNumber($type, $year, $params);
    $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
    $sql = "SELECT count(docs.id) AS total
            FROM docs
            WHERE docs.cabinet = :cabinet {$where}
            ";
    $sth = $pdo->prepare($sql);
    $sth->execute($params);
    return $sth->fetch(\PDO::FETCH_ASSOC);
}

    private function generateNumber($type, $year, &$params): string
    {
        $where = '';
        if ($year) {
            $params[':year'] = "%$year%";
            $where .= " AND docs.date LIKE :year";
        }
        if ($type) {
            $params[':type'] = $type;
            $where .= " AND docs.type = :type";
        }
        return $where;
    }

    public function listTotal(User $user, $id = 0, $type = null, $responsible = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $where = $this->generateWhere($id, $type, $responsible, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(docs.id) AS total                  
                FROM docs
                WHERE docs.cabinet = :cabinet {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function graphicNumberDocs(User $user): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(docs.id) AS total, type.name AS type
                FROM docs
                JOIN documentType AS type ON type.id = docs.type
                WHERE docs.cabinet = :cabinet 
                GROUP BY type
                ORDER BY total DESC;
                ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

}