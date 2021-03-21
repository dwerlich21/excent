<?php


namespace App\Controllers;

use App\Helpers\Date;
use App\Helpers\Validator;
use App\Models\Entities\Deal;
use App\Models\Entities\Document;
use App\Models\Entities\Message;
use App\Models\Entities\Task;
use App\Models\Entities\User;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;


class AdminController extends Controller
{

    public function index(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $today = date('Y-m-d');
        $date = \DateTime::createFromFormat('Y-m-d', $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId()]);
        $tasks = $this->em->getRepository(Task::class)->findBy(['date' => $date, 'status' => 1, 'user' => $user->getId()]);
        $messages = $this->em->getRepository(Message::class)->findBy(['active' => 1]);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'index.phtml', 'menuActive' => ['dashboard'],
            'user' => $user, 'deals' => $deals, 'tasks' => $tasks, 'messages' => $messages]);
    }

    public function saveTask(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $fields = [
                'date' => 'Date',
                'client' => 'Deal',
                'action' => 'Action'
            ];
            Validator::requireValidator($fields, $data);
            $task = new Task();
            $time = null;
            if ($data['time']) $time = \DateTime::createFromFormat('H:i', $data['time']);
            $task->setDate(\DateTime::createFromFormat('d/m/Y', $data['date']))
                ->setTime($time)
                ->setStatus(1)
                ->setUser($user)
                ->setAction($data['action'])
                ->setDescription($data['description'])
                ->setDeal($this->em->getReference(Deal::class, $data['client']));
            $this->em->getRepository(Task::class)->save($task);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Successfully registered task!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function taskStatus(Request $request, Response $response)
    {
        $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $task = $this->em->getRepository(Task::class)->find($id);
        $task->setStatus(0);
        $this->em->getRepository(Task::class)->save($task);
    }
}


