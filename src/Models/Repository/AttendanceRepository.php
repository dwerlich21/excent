<?php

namespace App\Models\Repository;

use App\Models\Entities\Attendance;
use App\Models\Entities\User;
use Doctrine\ORM\EntityRepository;

class AttendanceRepository extends EntityRepository
{
    public function save(Attendance $entity): Attendance
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

    private function generateWhere($id = 0,  $type = null, $responsible = null, $status = null, $neighborhood = null,
                                   $institution = null, $start = null, $end = null, &$params): string
    {
        $where = '';
        if ($id) {
            $params[':id'] = $id;
            $where .= " AND attendance.id = :id";
        }
        if ($type) {
            $params[':type'] = $type;
            $where .= " AND attendance.type = :type";
        }
        if ($responsible) {
            $params[':responsible'] = $responsible;
            $where .= " AND attendance.responsible = :responsible";
        }
        if ($status) {
            $params[':status'] = $status;
            $where .= " AND attendance.status = :status";
        }
        if ($neighborhood) {
            $params[':neighborhood'] = $neighborhood;
            $where .= " AND attendance.neighborhood = :neighborhood";
        }
        if ($institution) {
            $params[':institution'] = $institution;
            $where .= " AND attendance.institution = :institution";
        }
        $start = \DateTime::createFromFormat('d/m/Y', $start);
        $end = \DateTime::createFromFormat('d/m/Y', $end);
        if ($start) {
            $params[':start'] = $start->format('Y-m-d');
            $where .= " AND attendance.date >= :start";
        }
        if ($end) {
            $params[':end'] = $end->format('Y-m-d');
            $where .= " AND attendance.date <= :end";
        }
        return $where;
    }

    public function list(User $user, $id = 0, $type = null, $responsible = null, $status = null, $neighborhood = null,
                         $institution = null, $start = null, $end = null, $limit = null, $offset = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $limitSql = $this->generateLimit($limit, $offset);
        $where = $this->generateWhere($id, $type, $responsible, $status, $neighborhood, $institution, $start, $end, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT attendance.id, DATE_FORMAT(attendance.date, '%d/%m/%Y') AS date, CASE attendance.status WHEN 1 THEN 'Pendente' 
                WHEN '2' THEN 'Resolvido' WHEN '3' THEN 'Respondido/Não Atendido' ELSE 'Desconhecido' END AS status, neighborhood.name AS neighborhood,
                t.name AS type, users.name AS user, attendance.description, i.name AS institution, attendance.attendanceFile                
                FROM attendance
                JOIN typeOfService AS t ON attendance.type = t.id
                JOIN users ON attendance.responsible = users.id
                JOIN neighborhood ON attendance.neighborhood = neighborhood.id
                JOIN institution AS i ON attendance.institution = i.id
                WHERE attendance.cabinet = :cabinet {$where}
                ORDER BY id DESC {$limitSql}
               ";

        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function listTotal(User $user, $id = 0, $type = null, $responsible = null, $status = null, $neighborhood = null,
                              $institution = null, $start = null, $end = null): array
    {
        $params = [];$params[':cabinet'] = $user->getCabinet()->getId();
        $where = $this->generateWhere($id, $type, $responsible, $status, $neighborhood, $institution, $start, $end, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(attendance.id) AS total                  
                FROM attendance
                JOIN typeOfService as t ON attendance.type = t.id
                JOIN users ON attendance.responsible = users.id
                JOIN neighborhood ON attendance.neighborhood = neighborhood.id
                JOIN institution AS i ON attendance.institution = i.id
                WHERE attendance.cabinet = :cabinet {$where}
               ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function graphicAttendance(User $user, $id = 0, $type = null, $responsible = null, $status = null, $neighborhood = null,
                                      $institution = null, $start = null, $end = null): array
    {
        $params = []; $params[':cabinet'] = $user->getCabinet()->getId();
        $where = $this->generateWhere($id, $type, $responsible, $status, $neighborhood, $institution, $start, $end, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(attendance.id) AS total, type.name AS type
                FROM attendance
                JOIN typeOfService AS type ON type.id = attendance.type
                WHERE attendance.cabinet = :cabinet {$where}
                GROUP BY type
                ORDER BY total DESC;
                ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function graphicAttendanceByInstitution(User $user, $id = 0, $type = null, $responsible = null, $status = null, $neighborhood = null,
                                                   $institution = null, $start = null, $end = null): array
    {
        $params = []; $params[':cabinet'] = $user->getCabinet()->getId();
        $where = $this->generateWhere($id, $type, $responsible, $status, $neighborhood, $institution, $start, $end, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(attendance.id) AS total, institution.name AS institution
                FROM attendance
                JOIN institution ON institution.id = attendance.institution
                WHERE attendance.cabinet = :cabinet {$where}
                GROUP BY institution
                ORDER BY total DESC;
                ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function graphicAttendanceByNeighborhood(User $user, $id = 0, $type = null, $responsible = null, $status = null, $neighborhood = null,
                                                    $institution = null, $start = null, $end = null): array
    {
        $params = []; $params[':cabinet'] = $user->getCabinet()->getId();
        $where = $this->generateWhere($id, $type, $responsible, $status, $neighborhood, $institution, $start, $end, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(attendance.id) AS total, neighborhood.name AS neighborhood
                FROM attendance
                JOIN neighborhood ON neighborhood.id = attendance.neighborhood
                WHERE attendance.cabinet = :cabinet {$where}
                GROUP BY neighborhood
                ORDER BY total DESC;
                ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function graphicAttendanceByStatus(User $user, $id = 0, $type = null, $responsible = null, $status = null, $neighborhood = null,
                                              $institution = null, $start = null, $end = null): array
    {
        $params = []; $params[':cabinet'] = $user->getCabinet()->getId();
        $where = $this->generateWhere($id, $type, $responsible, $status, $neighborhood, $institution, $start, $end, $params);
        $pdo = $this->getEntityManager()->getConnection()->getWrappedConnection();
        $sql = "SELECT COUNT(attendance.id) AS total, CASE attendance.status WHEN 1 THEN 'Pendente' 
                WHEN '2' THEN 'Resolvido' WHEN '3' THEN 'Respondido/Não Atendido' ELSE 'Desconhecido' END AS status
                FROM attendance
                WHERE attendance.cabinet = :cabinet {$where}
                GROUP BY status
                ORDER BY total DESC;
                ";
        $sth = $pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }
}