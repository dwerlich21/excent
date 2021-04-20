<?php


namespace App\Models\Repository;


use App\Models\Entities\CompanyFiles;
use Doctrine\ORM\EntityRepository;

class CompanyFilesRepository extends EntityRepository
{
    public function save(CompanyFiles $entity): CompanyFiles
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    private function generateWhere($responsible, $name = null,  &$params): string
    {
        $where = '';

        if ($name) {
            $params[':name'] = "%$name%";
            $where .= " AND companyFiles.name LIKE :name";
        }
        if ($responsible) {
            $params[':responsible'] = $responsible;
            $where .= " AND companyFiles.responsible = :responsible";
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

    public function list($responsible, $name = null, $limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($responsible, $name, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT companyFiles.name, companyFiles.folder, companyFiles.id, 
                DATE_FORMAT(companyFiles.created, '%d/%m/%Y') AS date, TIME_FORMAT(companyFiles.created, '%H:%i') AS time           
                FROM companyFiles
                WHERE 1 = 1 {$where}
                ORDER BY id DESC {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal($responsible, $name): array
    {
        $params = [];
        $where = $this->generateWhere($responsible, $name, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(companyFiles.id) AS total                  
                FROM companyFiles
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
        $sql = "DELETE FROM companyFiles
                WHERE companyFiles.id = {$id} 
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
}