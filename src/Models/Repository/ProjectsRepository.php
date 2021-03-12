<?php

namespace App\Models\Repository;

use App\Models\Entities\Cabinet;
use App\Models\Entities\Projects;
use App\Models\Entities\User;
use Doctrine\ORM\EntityRepository;

class ProjectsRepository extends EntityRepository
{
    public function save(Projects $entity): Projects
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

    private function generateWhere($id = 0, $type = null, $status = null, $responsible = null, $start = null, $end = null, &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND projects.id = :id";
        }
        if ($type) {
            $params[':type'] = $type;
            $where .= " AND projects.type = :type";
        }
        if ($status) {
            $params[':status'] = $status;
            $where .= " AND projects.status = :status";
        }
        if ($responsible) {
            $params[':responsible'] = $responsible;
            $where .= " AND projects.responsible = :responsible";
        }
        $start = \DateTime::createFromFormat('d/m/Y', $start);
        $end = \DateTime::createFromFormat('d/m/Y', $end);
        if ($start) {
            $params[':start'] = $start->format('Y-m-d');
            $where .= " AND projects.date >= :start";
        }
        if ($end) {
            $params[':end'] = $end->format('Y-m-d');
            $where .= " AND projects.date <= :end";
        }
        return $where;
    }

    public function list(User $user, $id = 0, $type = null, $status = null, $responsible = null, $start = null, $end = null, $limit = null, $offset = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id,  $type, $status, $responsible, $start, $end, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT projects.id, DATE_FORMAT(projects.date, '%d/%m/%Y') as date, projects.subjectMatter, CASE projects.status WHEN 1 THEN 'Tramitando' 
                WHEN '2' THEN 'Arquivado' WHEN '3' THEN 'Aprovado' ELSE 'Desconhecido' END AS status, 
                t.name, users.name AS user, projects.projectFile                
                FROM projects
                JOIN typeOfProject as t ON projects.type = t.id
                JOIN users ON projects.responsible = users.id
                WHERE projects.cabinet = :cabinet {$where}
                ORDER BY id DESC {$limitSql}
               ";

        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal(User $user, $id = 0,  $type = null, $status = null, $responsible = null, $start = null, $end = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $where = $this->generateWhere($id,  $type, $status, $responsible, $start, $end, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(projects.id) AS total                  
                FROM projects
                JOIN typeOfProject as t ON projects.type = t.id
                JOIN users ON projects.responsible = users.id
                WHERE projects.cabinet = :cabinet {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function graphicProjectByType(User $user, $id = 0, $type = null, $status = null, $responsible = null, $start = null, $end = null): array
    {
        $params = []; $params[':cabinet'] = $user->getCabinet()->getId();
        $where = $this->generateWhere($id, $type, $status, $responsible, $start, $end, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(projects.id) AS total, type.name AS type
                FROM projects
                JOIN typeOfProject AS type ON type.id = projects.type
                WHERE projects.cabinet = :cabinet {$where}
                GROUP BY type
                ORDER BY total DESC;
                ";

        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function graphicProjectByStatus(User $user, $id = 0, $type = null, $status = null, $responsible = null, $start = null, $end = null): array
    {
        $params = []; $params[':cabinet'] = $user->getCabinet()->getId();
        $where = $this->generateWhere($id, $type, $status, $responsible, $start, $end, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(projects.id) AS total, CASE projects.status WHEN 1 THEN 'Tramitando' 
                WHEN '2' THEN 'Arquivado' WHEN '3' THEN 'Aprovado' ELSE 'Desconhecido' END AS status
                FROM projects
                WHERE projects.cabinet = :cabinet {$where}
                GROUP BY status 
                ORDER BY total DESC;
                ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

}