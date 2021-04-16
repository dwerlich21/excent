<?php


namespace App\Models\Repository;

use Doctrine\ORM\EntityRepository;


class FolderAcessRepository extends EntityRepository
{
    public function save(FolderAcess $entity): FolderAcess
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

    private function generateWhere($id = 0, $responsible = null, $name = null, &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND folderAcess.id = :id";
        }
        if ($name) {
            $params[':name'] = "%$name%";
            $where .= " AND folderAcess.name LIKE :name";
        }
        if ($responsible) {
            $params[':responsible'] = $responsible;
            $where .= " AND folderAcess.user = :responsible";
        }
        return $where;
    }

    public function list($id = 0, $responsible = null, $name = null, $limit = null, $offset = null): array
    {
        $params = [];
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $responsible, $name, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT folderAcess.id, folder.name               
                FROM folderAcess
                WHERE 1 = 1 {$where}
                ORDER BY name ASC {$limitSql}
               ";
        echo $sql;
        die(var_dump($params));
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal($id = 0, $responsible = null, $name = null): array
    {
        $params = [];
        $where = $this->generateWhere($id, $responsible, $name, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(folderAcess.id) AS total                  
                FROM folderAcess
                WHERE 1 = 1 {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

}