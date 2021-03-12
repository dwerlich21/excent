<?php

namespace App\Models\Repository;

use App\Models\Entities\Contacts;
use App\Models\Entities\User;
use Doctrine\ORM\EntityRepository;

class ContactsRepository extends EntityRepository
{
    public function save(Contacts $entity): Contacts
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

    private function generateWhere($id = 0, $name = null, $email = null, $type = null, &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND contacts.id = :id";
        }
        if ($email) {
            $params[':email'] = "%$email%";
            $where .= " AND contacts.email LIKE :email";
        }
        if ($name) {
            $params[':name'] = "%$name%";
            $where .= " AND contacts.name LIKE :name";
        }
        if ($type) {
            $params[':typeID'] = $type;
            $where .= " AND t.id = :typeID";
        }
        return $where;
    }

    public function list(User $user, $id = 0, $name = null, $email = null, $type = null, $limit = null,  $offset = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $name, $email, $type, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT contacts.id, contacts.name, contacts.email, contacts.phone, contacts.cityId, t.name AS type, 
                DATE_FORMAT(contacts.dateOfBirth, '%d/%m/%Y') AS date, contacts.zipCode, contacts.ufId, contacts.neighborhood, contacts.cabinet,
                contacts.adress, t.id AS typeID          
                FROM contacts
                JOIN typeContact AS t ON contacts.typeContact = t.id
                WHERE contacts.cabinet = :cabinet {$where}
                ORDER BY name ASC {$limitSql}
               ";

        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal(User $user, $id = 0, $name = null, $email = null, $type = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $where = $this->generateWhere($id, $name, $email, $type, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(contacts.id) AS total
                FROM contacts
                JOIN typeContact AS t ON contacts.typeContact = t.id  
                WHERE contacts.cabinet = :cabinet {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

}