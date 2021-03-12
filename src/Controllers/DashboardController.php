<?php


namespace App\Controllers;

use App\Helpers\Utils;
use App\Models\Entities\Attendance;
use App\Models\Entities\Docs;
use App\Models\Entities\Projects;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DashboardController extends Controller
{

    public function graphicNumberDocs(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $docs = $this->em->getRepository(Docs::class)->graphicNumberDocs($user);
        $keys = $values = $rgb = [];
        $total = 0;
        foreach ($docs as $doc) {
            $keys[] = $doc['type'];
            $values[] = $doc['total'];
            $rgb[] = Utils::randColor();
            $total += $doc['total'];
        }
        return $response->withJson([
            'status' => 'ok',
            'keys' => $keys,
            'values' => $values,
            'rgb' => $rgb,
            'total' => $total,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function graphicProjectByType(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $type = $request->getQueryParam('type');
        $status = $request->getQueryParam('status');
        $responsible = $request->getQueryParam('responsible');
        $start = $request->getQueryParam('start');
        $end = $request->getQueryParam('end');
        $projects = $this->em->getRepository(Projects::class)->graphicProjectByType($user, $id, $type, $status, $responsible, $start, $end);
        $keys = $values = $rgb = [];
        $total = 0;
        foreach ($projects as $project) {
            $keys[] = $project['type'];
            $values[] = $project['total'];
            $rgb[] = Utils::randColor();
            $total += $project['total'];
        }
        return $response->withJson([
            'status' => 'ok',
            'keys' => $keys,
            'values' => $values,
            'rgb' => $rgb,
            'total' => $total,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function graphicProjectByStatus(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $type = $request->getQueryParam('type');
        $status = $request->getQueryParam('status');
        $responsible = $request->getQueryParam('responsible');
        $start = $request->getQueryParam('start');
        $end = $request->getQueryParam('end');
        $projects = $this->em->getRepository(Projects::class)->graphicProjectByStatus($user, $id, $type, $status, $responsible, $start, $end);
        $keys = $values = $rgb = [];
        $total = 0;
        foreach ($projects as $project) {
            $keys[] = ($project['status']);
            $values[] = $project['total'];
            $rgb[] = Utils::randColor();
            $total += $project['total'];
        }
        return $response->withJson([
            'status' => 'ok',
            'keys' => $keys,
            'values' => $values,
            'rgb' => $rgb,
            'total' => $total,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function graphicAttendance(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $type = $request->getQueryParam('type');
        $responsible = $request->getQueryParam('responsible');
        $status = $request->getQueryParam('status');
        $neighborhood = $request->getQueryParam('neighborhood');
        $institution = $request->getQueryParam('institution');
        $start = $request->getQueryParam('start');
        $end = $request->getQueryParam('end');
        $attendances = $this->em->getRepository(Attendance::class)->graphicAttendance($user, $id, $type, $responsible, $status, $neighborhood, $institution, $start, $end);
        $keys = $values = $rgb = [];
        $total = 0;
        foreach ($attendances as $attendance) {
            $keys[] = $attendance['type'];
            $values[] = $attendance['total'];
            $rgb[] = Utils::randColor();
            $total += $attendance['total'];
        }
        return $response->withJson([
            'status' => 'ok',
            'keys' => $keys,
            'values' => $values,
            'rgb' => $rgb,
            'total' => $total,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function graphicAttendanceByInstitution(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $type = $request->getQueryParam('type');
        $responsible = $request->getQueryParam('responsible');
        $status = $request->getQueryParam('status');
        $neighborhood = $request->getQueryParam('neighborhood');
        $institution = $request->getQueryParam('institution');
        $start = $request->getQueryParam('start');
        $end = $request->getQueryParam('end');
        $attendances = $this->em->getRepository(Attendance::class)->graphicAttendanceByInstitution($user, $id, $type, $responsible, $status, $neighborhood, $institution, $start, $end);
        $keys = $values = $rgb = [];
        $total = 0;
        foreach ($attendances as $attendance) {
            $keys[] = $attendance['institution'];
            $values[] = $attendance['total'];
            $rgb[] = Utils::randColor();
            $total += $attendance['total'];
        }
        return $response->withJson([
            'status' => 'ok',
            'keys' => $keys,
            'values' => $values,
            'rgb' => $rgb,
            'total' => $total,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function graphicAttendanceByNeighborhood(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $type = $request->getQueryParam('type');
        $responsible = $request->getQueryParam('responsible');
        $status = $request->getQueryParam('status');
        $neighborhood = $request->getQueryParam('neighborhood');
        $institution = $request->getQueryParam('institution');
        $start = $request->getQueryParam('start');
        $end = $request->getQueryParam('end');
        $attendances = $this->em->getRepository(Attendance::class)->graphicAttendanceByNeighborhood($user, $id, $type, $responsible, $status, $neighborhood, $institution, $start, $end);
        $keys = $values = $rgb = [];
        $total = 0;
        foreach ($attendances as $attendance) {
            $keys[] = $attendance['neighborhood'];
            $values[] = $attendance['total'];
            $rgb[] = Utils::randColor();
            $total += $attendance['total'];
        }
        return $response->withJson([
            'status' => 'ok',
            'keys' => $keys,
            'values' => $values,
            'rgb' => $rgb,
            'total' => $total,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function graphicAttendanceByStatus(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $type = $request->getQueryParam('type');
        $responsible = $request->getQueryParam('responsible');
        $status = $request->getQueryParam('status');
        $neighborhood = $request->getQueryParam('neighborhood');
        $institution = $request->getQueryParam('institution');
        $start = $request->getQueryParam('start');
        $end = $request->getQueryParam('end');
        $attendances = $this->em->getRepository(Attendance::class)->graphicAttendanceByStatus($user, $id, $type, $responsible, $status, $neighborhood, $institution, $start, $end);
        $keys = $values = $rgb = [];
        $total = 0;
        foreach ($attendances as $attendance) {
            $keys[] = $attendance['status'];
            $values[] = $attendance['total'];
            $rgb[] = Utils::randColor();
            $total += $attendance['total'];
        }
        return $response->withJson([
            'status' => 'ok',
            'keys' => $keys,
            'values' => $values,
            'rgb' => $rgb,
            'total' => $total,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }
}