<?php


namespace App\Controllers;


use App\Helpers\Utils;
use App\Models\Entities\Attendance;
use App\Models\Entities\Contacts;
use App\Models\Entities\Docs;
use App\Models\Entities\DocumentType;
use App\Models\Entities\Institution;
use App\Models\Entities\Meetings;
use App\Models\Entities\Neighborhood;
use App\Models\Entities\Projects;
use App\Models\Entities\TypeOfProject;
use App\Models\Entities\TypeOfService;
use App\Models\Entities\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ApiController extends Controller
{
    public function users(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $name = $request->getQueryParam('name');
        $email = $request->getQueryParam('email');
        $type = $request->getQueryParam('type');
        $active = $request->getQueryParam('active');
        $index = $request->getQueryParam('index');
        $users = $this->em->getRepository(User::class)->list($user, $id, $name, $email, $type,  $active, 20, $index * 20);
        $total = $this->em->getRepository(User::class)->listTotal($user, $id, $name, $email, $type, $active)['total'];
        $partial = ($index * 20) + sizeof($users);
        $partial = $partial <= $total ? $partial : $total; // de algum jeito o scroll chamou a mais
        return $response->withJson([
            'status' => 'ok',
            'message' => $users,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function institutions(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $name = $request->getQueryParam('name');
        $email = $request->getQueryParam('email');
        $responsible = $request->getQueryParam('responsible');
        $index = $request->getQueryParam('index');
        $institution = $this->em->getRepository(Institution::class)->list($user, $id, $name, $email, $responsible, 20, $index * 20);
        $total = $this->em->getRepository(Institution::class)->listTotal($user, $id, $name, $email, $responsible)['total'];
        $partial = ($index * 20) + sizeof($institution);
        $partial = $partial <= $total ? $partial : $total;
        return $response->withJson([
            'status' => 'ok',
            'message' => $institution,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function neighborhoods(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $name = $request->getQueryParam('name');
        $index = $request->getQueryParam('index');
        $neighborhood = $this->em->getRepository(Neighborhood::class)->list($user, $id, $name, 20, $index * 20);
        $total = $this->em->getRepository(Neighborhood::class)->listTotal($user, $id, $name)['total'];
        $partial = ($index * 20) + sizeof($neighborhood);
        $partial = $partial <= $total ? $partial : $total;
        return $response->withJson([
            'status' => 'ok',
            'message' => $neighborhood,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function meetings(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $start = $request->getQueryParam('start');
        $adress = $request->getQueryParam('adress');
        $theme = $request->getQueryParam('theme');
        $index = $request->getQueryParam('index');
        $meetings = $this->em->getRepository(Meetings::class)->list($user, $id, $start, $adress, $theme, 20, $index * 20);
        $total = $this->em->getRepository(Meetings::class)->listTotal($user, $id, $start, $adress, $theme)['total'];
        $partial = ($index * 20) + sizeof($meetings);
        $partial = $partial <= $total ? $partial : $total;
        return $response->withJson([
            'status' => 'ok',
            'message' => $meetings,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function contacts(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $name = $request->getQueryParam('name');
        $email = $request->getQueryParam('email');
        $type = $request->getQueryParam('type');
        $index = $request->getQueryParam('index');
        $contacts = $this->em->getRepository(Contacts::class)->list($user, $id, $name, $email, $type, 20, $index * 20);
        $total = $this->em->getRepository(Contacts::class)->listTotal($user, $id, $name, $email, $type)['total'];
        $partial = ($index * 20) + sizeof($contacts);
        $partial = $partial <= $total ? $partial : $total;
        return $response->withJson([
            'status' => 'ok',
            'message' => $contacts,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function projects(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $start = $request->getQueryParam('start');
        $end = $request->getQueryParam('end');
        $type = $request->getQueryParam('type');
        $status = $request->getQueryParam('status');
        $responsible = $request->getQueryParam('responsible');
        $index = $request->getQueryParam('index');
        $projects = $this->em->getRepository(Projects::class)->list($user, $id, $type, $status, $responsible, $start, $end, 20, $index * 20);
        $total = $this->em->getRepository(Projects::class)->listTotal($user, $id, $type, $status, $responsible, $start, $end)['total'];
        $partial = ($index * 20) + sizeof($projects);
        $partial = $partial <= $total ? $partial : $total;
        return $response->withJson([
            'status' => 'ok',
            'message' => $projects,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function attendance(Request $request, Response $response)
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
        $index = $request->getQueryParam('index');
        $attendance = $this->em->getRepository(Attendance::class)->list($user, $id, $type, $responsible, $status, $neighborhood, $institution, $start, $end, 20, $index * 20);
        $total = $this->em->getRepository(Attendance::class)->listTotal($user, $id, $type, $responsible, $status, $neighborhood, $institution, $start, $end)['total'];
        $partial = ($index * 20) + sizeof($attendance);
        $partial = $partial <= $total ? $partial : $total;
        return $response->withJson([
            'status' => 'ok',
            'message' => $attendance,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function docs(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $type = $request->getQueryParam('type');
        $responsible = $request->getQueryParam('responsible');
        $index = $request->getQueryParam('index');
        $docs = $this->em->getRepository(Docs::class)->list($user, $id, $type, $responsible, 20, $index * 20);
        $total = $this->em->getRepository(Docs::class)->listTotal($user, $id, $type, $responsible)['total'];
        $partial = ($index * 20) + sizeof($docs);
        $partial = $partial <= $total ? $partial : $total;
        return $response->withJson([
            'status' => 'ok',
            'message' => $docs,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function documentType(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $index = $request->getQueryParam('index');
        $documentType = $this->em->getRepository(DocumentType::class)->list($user, $id, 20, $index * 20);
        $total = $this->em->getRepository(DocumentType::class)->listTotal($user, $id)['total'];
        $partial = ($index * 20) + sizeof($documentType);
        $partial = $partial <= $total ? $partial : $total;
        return $response->withJson([
            'status' => 'ok',
            'message' => $documentType,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function typeOfService(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $index = $request->getQueryParam('index');
        $typeOfService = $this->em->getRepository(TypeOfService::class)->list($user, $id, 20, $index * 20);
        $total = $this->em->getRepository(TypeOfService::class)->listTotal($user, $id)['total'];
        $partial = ($index * 20) + sizeof($typeOfService);
        $partial = $partial <= $total ? $partial : $total;
        return $response->withJson([
            'status' => 'ok',
            'message' => $typeOfService,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function typeOfProject(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $index = $request->getQueryParam('index');
        $typeOfProject = $this->em->getRepository(TypeOfProject::class)->list($user, $id, 20, $index * 20);
        $total = $this->em->getRepository(TypeOfProject::class)->listTotal($user, $id)['total'];
        $partial = ($index * 20) + sizeof($typeOfProject);
        $partial = $partial <= $total ? $partial : $total;
        return $response->withJson([
            'status' => 'ok',
            'message' => $typeOfProject,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }
}