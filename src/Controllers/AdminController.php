<?php


namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Entities\ActivityDeal;
use App\Models\Entities\Deal;
use App\Models\Entities\Message;
use App\Models\Entities\Transaction;
use DateInterval;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;


class AdminController extends Controller
{

    public function index(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $user->getId();
        $today = date('Y-m-d');
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-01 00:00:00', strtotime('+1 month'));

        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $messages = $this->em->getRepository(Message::class)->findBy(['active' => 1], ['date' => 'desc'], 3);
        $advisors = $this->em->getRepository(Transaction::class)->rankingAdvisors($start, $end);
        $managers = $this->em->getRepository(Transaction::class)->rankingManagers($start, $end);
        $managersGroup = $this->em->getRepository(Transaction::class)->rankingManagersGroup($id, $start, $end);
        $master = $this->em->getRepository(Transaction::class)->rankingMaster($start, $end);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'index.phtml', 'menuActive' => ['dashboard'],
            'user' => $user, 'deals' => $deals, 'messages' => $messages, 'advisors' => $advisors, 'activities' => $activities,
            'managers' => $managers, 'managersGroup' => $managersGroup, 'master' => $master]);
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


