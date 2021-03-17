<?php

namespace App\Models\Repository;

use App\Models\Entities\Task;
use App\Models\Entities\User;
use Doctrine\ORM\EntityRepository;

class TaskRepository extends EntityRepository
{
    public function save(Task $entity): Task
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

    private function generateWhere($id = 0, $date,  &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND task.id = :id";
        }
        $date = \DateTime::createFromFormat('d/m/Y', $date);
        if ($date) {
            $params[':date'] = $date;
            $where .= " AND task.date = :date";
        }
        return $where;
    }

    public function listDashboardNotNull($id = 0, User $user, $date, $limit = null, $offset = null): array
    {
        $params = [];$params[':user'] = $user->getId();
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $date, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT TIME_FORMAT(task.time, '%H:%i') AS time, client.name AS client, task.id, task.action           
                FROM task
                JOIN client ON client.id = task.client
                WHERE task.time IS NOT NULL AND task.user = :user AND task.status = 1 {$where} 
                ORDER BY time ASC {$limitSql}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listDashboardNull($id = 0, User $user, $date, $limit = null, $offset = null): array
    {
        $params = []; $params[':user'] = $user->getId();
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $date, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT task.time, task.client, task.id, task.action, client.name AS client           
                FROM task
                JOIN client ON client.id = task.client
                WHERE task.time IS NULL AND task.user = :user AND task.status = 1 {$where} 
                ORDER BY id DESC {$limitSql}
               ";

        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

}