<?php


namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Entities\ActivityDeal;
use App\Models\Entities\Deal;
use App\Models\Entities\DocumentDestiny;
use App\Models\Entities\Message;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;


class AdminController extends Controller
{

    public function index(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $today = date('Y-m-d');
        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $messages = $this->em->getRepository(Message::class)->findBy(['active' => 1], ['date' => 'desc'], 3);
        $docs = $this->em->getRepository(DocumentDestiny::class)->findBy(['destiny' => $user->getId()], ['id' => 'desc'], 3);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'index.phtml', 'menuActive' => ['dashboard'],
            'user' => $user, 'deals' => $deals, 'messages' => $messages, 'docs' => $docs, 'activities' => $activities]);
    }

    public function taskStatus(Request $request, Response $response)
    {
        $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $task = $this->em->getRepository(ActivityDeal::class)->find($id);
        $task->setStatus(0);
        $this->em->getRepository(ActivityDeal::class)->save($task);
    }
}


