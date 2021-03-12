<?php

namespace App\Models\Repository;

use App\Models\Entities\Institution;
use App\Models\Entities\User;
use Doctrine\ORM\EntityRepository;

class InstitutionRepository extends EntityRepository
{
    public function save(Institution $entity): Institution
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

    private function generateWhere($id = 0, $name = null, $email = null, $responsible = null, &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND institution.id = :id";
        }
        if ($name) {
            $params[':name'] = "%$name%";
            $where .= " AND institution.name LIKE :name";
        }
        if ($email) {
            $params[':email'] = "%$email%";
            $where .= " AND institution.email LIKE :email";
        }
        if ($responsible) {
            $params[':responsible'] = "%$responsible%";
            $where .= " AND institution.responsible LIKE :responsible";
        }
        return $where;
    }

    public function list(User $user, $id = 0, $name = null, $email = null, $responsible = null,  $limit = null, $offset = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $name, $email, $responsible, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT institution.id, institution.name, institution.email, institution.responsible, institution.phone,
                institution.zipCode, institution.adress, institution.ufId, institution.cityId, institution.neighborhood               
                FROM institution
                WHERE institution.cabinet = :cabinet {$where}
                ORDER BY name ASC {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal(User $user, $id = 0, $name = null, $email = null, $responsible = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $where = $this->generateWhere($id, $name, $email, $responsible, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(institution.id) AS total                  
                FROM institution
                WHERE institution.cabinet = :cabinet {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

}