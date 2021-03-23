<?php


namespace App\Controllers;

use App\Models\Entities\Deal;
use App\Models\Entities\Document;
use App\Models\Entities\Message;
use App\Models\Entities\Task;
use App\Models\Entities\User;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class ApiController extends Controller
{
    public function usersTable(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $name = $request->getQueryParam('name');
        $email = $request->getQueryParam('email');
        $type = $request->getQueryParam('type');
        $active = $request->getQueryParam('active');
        $index = $request->getQueryParam('index');
        $users = $this->em->getRepository(User::class)->list($id, $name, $email, $type, $active, 20, $index * 20);
        $total = $this->em->getRepository(User::class)->listTotal($id, $name, $email, $type, $active)['total'];
        $partial = ($index * 20) + sizeof($users);
        $partial = $partial <= $total ? $partial : $total;
        return $response->withJson([
            'status' => 'ok',
            'message' => $users,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function dealsTable(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $name = $request->getQueryParam('name');
        $company = $request->getQueryParam('company');
        $index = $request->getQueryParam('index');
        $deals = $this->em->getRepository(Deal::class)->list($id, $name, $company, 42, $index * 42);
        $total = $this->em->getRepository(Deal::class)->listTotal($id, $name, $company)['total'];
        $partial = ($index * 42) + sizeof($deals);
        $partial = $partial <= $total ? $partial : $total;
        return $response->withJson([
            'status' => 'ok',
            'message' => $deals,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function statusUpdate(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getQueryParam('deal');
        $status = $request->getQueryParam('status');
        $deal = $this->em->getRepository(Deal::class)->find($id);
        $deal->setStatus($status);
        $this->em->getRepository(Deal::class)->save($deal);
        return $response->withJson([
            'status' => 'ok',
            'message' => 'Status alterado com sucesso!',
        ], 201)
            ->withHeader('Content-type', 'application/json');
    }


    public
    function documentsTable(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $index = $request->getQueryParam('index');
        $documents = $this->em->getRepository(Document::class)->list($id, 20, $index * 20);
        $total = $this->em->getRepository(Document::class)->listTotal()['total'];
        $partial = ($index * 20) + sizeof($documents);
        $partial = $partial <= $total ? $partial : $total;

        return $response->withJson([
            'status' => 'ok',
            'message' => $documents,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public
    function documentDelete(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $this->em->getRepository(Document::class)->documentDelete($id);
        die();
    }

    public
    function messagesTable(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $index = $request->getQueryParam('index');
        $messages = $this->em->getRepository(Message::class)->list($id, 20, $index * 20);
        $total = $this->em->getRepository(Message::class)->listTotal()['total'];
        $partial = ($index * 20) + sizeof($messages);
        $partial = $partial <= $total ? $partial : $total;

        return $response->withJson([
            'status' => 'ok',
            'message' => $messages,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public
    function messageDelete(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $this->em->getRepository(Message::class)->messageDelete($id);
        die();
    }

    public
    function messagesDashboard(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $index = $request->getQueryParam('index');
        $messages = $this->em->getRepository(Message::class)->listDashboard($id, 20, $index * 20);

        return $response->withJson([
            'status' => 'ok',
            'message' => $messages,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public
    function tasksDashboard(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $index = $request->getQueryParam('index');
        $today = date('Y-m-d');
        $date = \DateTime::createFromFormat('Y-m-d', $today);
        $tasks = $this->em->getRepository(Task::class)->listDashboardNotNull($id, $user, $date, 20, $index * 20);
        $tasksNull = $this->em->getRepository(Task::class)->listDashboardNull($id, $user, $date, 20, $index * 20);
        $total = array_merge($tasks, $tasksNull);

        return $response->withJson([
            'status' => 'ok',
            'message' => $total,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }
}