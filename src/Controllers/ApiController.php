<?php


namespace App\Controllers;

use App\Models\Entities\ActivityDeal;
use App\Models\Entities\Deal;
use App\Models\Entities\DocumentMyFolder;
use App\Models\Entities\DocumentCategory;
use App\Models\Entities\DocumentDestiny;
use App\Models\Entities\FolderAcess;
use App\Models\Entities\Message;
use App\Models\Entities\Transaction;
use App\Models\Entities\User;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class ApiController extends Controller
{
    public function usersTable(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $manager = 0;
        if ($user->getType() == 3) $manager = $user->getId();
        $id = $request->getAttribute('route')->getArgument('id');
        $name = $request->getQueryParam('name');
        $type = $request->getQueryParam('type');
        $index = $request->getQueryParam('index');
        $users = $this->em->getRepository(User::class)->list($id, $name, $manager, $type, 20, $index * 20);
        $total = $this->em->getRepository(User::class)->listTotal($id, $name, $manager, $type)['total'];
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
        $country = $request->getQueryParam('country');
        $index = $request->getQueryParam('index');
        $deals = $this->em->getRepository(Deal::class)->list($id, $name, $country, 42, $index * 42);
        $total = $this->em->getRepository(Deal::class)->listTotal($id, $name, $country)['total'];
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

    public function ActivityDeal(Request $request, Response $response)
    {
        $this->getLogged(true);
        $deal = $request->getQueryParam('deal');
        $index = $request->getQueryParam('index');
        $activity = $this->em->getRepository(ActivityDeal::class)->list($deal, 10, $index * 10);
        $total = $this->em->getRepository(ActivityDeal::class)->listTotal($deal)['total'];
        $partial = ($index * 42) + sizeof($activity);
        $partial = $partial <= $total ? $partial : $total;
        return $response->withJson([
            'status' => 'ok',
            'message' => $activity,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function statusStr($status)
    {
        switch (intval($status)) {
            case 1:
                return 'Follow Up';
            case 2:
                return 'Pending Agreement';
            case 3:
                return 'Approved Terms';
        }

    }

    public function statusUpdate(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $date = date('Y-m-d H:i');
        $id = $request->getQueryParam('deal');
        $newStatus = $request->getQueryParam('status');
        $deal = $this->em->getRepository(Deal::class)->find($id);
        $status = $deal->getStatus();
        $deal->setStatus($newStatus);
        $this->em->getRepository(Deal::class)->save($deal);
        $statusStr = $this->statusStr($status);
        $newStatusStr = $this->statusStr($newStatus);
        $description = "Moved from " . $statusStr . " to " . $newStatusStr;
        $activity = new ActivityDeal();
        $activity->setDeal($this->em->getReference(Deal::class, $id))
            ->setUser($user)
            ->setDate(\DateTime::createFromFormat('Y-m-d H:i', $date))
            ->setType(0)
            ->setDescription('')
            ->setStatus(0)
            ->setActivity($description);
        $this->em->getRepository(ActivityDeal::class)->save($activity);
        return $response->withJson([
            'status' => 'ok',
            'message' => 'Status alterado com sucesso!',
        ], 201)
            ->withHeader('Content-type', 'application/json');
    }

    public function myFoldersTable(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $responsible = $user->getId();
        $index = $request->getQueryParam('index');
        $title = $request->getQueryParam('title');
        $documents = $this->em->getRepository(DocumentMyFolder::class)->list($responsible, $title, 20, $index * 20);
        $total = $this->em->getRepository(DocumentMyFolder::class)->listTotal($responsible, $title)['total'];
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

    public function myFolderDeleteDoc(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $this->em->getRepository(DocumentMyFolder::class)->delDocument($id);
        die();
    }

    public function foldersTable(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $responsible = $user->getId();
        $id = $request->getAttribute('route')->getArgument('id');
        $index = $request->getQueryParam('index');
        $name = $request->getQueryParam('name');
        $documents = $this->em->getRepository(FolderAcess::class)->list($id, $responsible, $name, 20, $index * 20);
        $total = $this->em->getRepository(FolderAcess::class)->listTotal($id, $responsible, $name)['total'];
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

    public function messagesTable(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $index = $request->getQueryParam('index');
        $title = $request->getQueryParam('title');
        $active = $request->getQueryParam('active');
        $messages = $this->em->getRepository(Message::class)->list($id, $title, $active, 20, $index * 20);
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

    public function messageDelete(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $this->em->getRepository(Message::class)->messageDelete($id);
        die();
    }

    public function tasksDashboard(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $index = $request->getQueryParam('index');
        $today = date('Y-m-d');
        $tasks = $this->em->getRepository(ActivityDeal::class)->listDashboard($id, $user, $today, 20, $index * 20);

        return $response->withJson([
            'status' => 'ok',
            'message' => $tasks,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function leadsTable(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $index = $request->getQueryParam('index');
        $name = $request->getQueryParam('name');
        $country = $request->getQueryParam('country');
        $messages = $this->em->getRepository(Deal::class)->listLead($id, $name, $country, 20, $index * 20);
        $total = $this->em->getRepository(Deal::class)->listTotalLead($id, $name, $country)['total'];
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

    public function leadDelete(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $this->em->getRepository(Deal::class)->dealDelete($id);
        die();
    }

    public function transactionsDelete(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $this->em->getRepository(Transaction::class)->transactionsDelete($id);
        die();
    }

    public function transactionssTable(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $index = $request->getQueryParam('index');
        $user = $request->getQueryParam('user');
        $country = $request->getQueryParam('country');
        $transactions = $this->em->getRepository(Transaction::class)->list($id, $user, $country, 20, $index * 20);
        $total = $this->em->getRepository(Transaction::class)->listTotal($id, $user, $country)['total'];
        $partial = ($index * 20) + sizeof($transactions);
        $partial = $partial <= $total ? $partial : $total;

        return $response->withJson([
            'status' => 'ok',
            'message' => $transactions,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }
}