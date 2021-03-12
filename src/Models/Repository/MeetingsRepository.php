<?php

namespace App\Models\Repository;

use App\Models\Entities\Meetings;
use App\Models\Entities\User;
use Doctrine\ORM\EntityRepository;

class MeetingsRepository extends EntityRepository
{
    public function save(Meetings $entity): Meetings
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

    private function generateWhere($id = 0, $start = null, $adress = null, $theme = null, &$params): string
    {
        $where = '';
        if ($start) {
            $params[':start'] = "%$start%";
            $where .= " AND meetings.start LIKE :start";
        }
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND meetings.id = :id";
        }
        if ($adress) {
            $params[':adress'] = "%$adress%";
            $where .= " AND meetings.adress LIKE :adress";
        }
        if ($theme) {
            $params[':theme'] = "%$theme%";
            $where .= " AND meetings.theme LIKE :theme";
        }
        return $where;
    }

    public function list(User $user, $id = 0, $start = null, $adress = null, $theme = null, $limit = null, $offset = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $start, $adress, $theme, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT meetings.id, DATE_FORMAT(meetings.start, '%d/%m/%Y') as start, meetings.theme,
                CASE meetings.adress WHEN 1 THEN 'Gabinete' WHEN 2 THEN 'Câmara' WHEN 3 THEN 'Prefeitura' 
                WHEN 4 THEN 'Externo' ELSE 'Desconhecido' END AS adress              
                FROM meetings
                WHERE meetings.cabinet = :cabinet {$where}
                ORDER BY id DESC {$limitSql}
               ";

        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal(User $user, $id = 0, $start = null, $adress = null, $theme = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $where = $this->generateWhere($id, $start, $adress, $theme, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(meetings.id) AS total                  
                FROM meetings
                WHERE meetings.cabinet = :cabinet {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }


}