<?php


namespace App\Controllers;

use App\Models\Entities\ActivityDeal;
use App\Models\Entities\Deal;
use App\Models\Entities\Document;
use App\Models\Entities\DocumentCategory;
use App\Models\Entities\DocumentDestiny;
use App\Models\Entities\Message;
use App\Models\Entities\Transaction;
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
        $id = $request->getQueryParam('activity');
        $index = $request->getQueryParam('index');
        $activity = $this->em->getRepository(ActivityDeal::class)->list($id, $deal, 10, $index * 10);
        $total = $this->em->getRepository(ActivityDeal::class)->listTotal($id, $deal)['total'];
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
        $date = date('Y-m-d');
        $hour = \date('H:i');
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
            ->setDate(\DateTime::createFromFormat('Y-m-d', $date))
            ->setTime(\DateTime::createFromFormat('H:i', $hour))
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

    public function documentsSentTable(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $resposible = $user->getId();
        $id = $request->getAttribute('route')->getArgument('id');
        $index = $request->getQueryParam('index');
        $title = $request->getQueryParam('title');
        $type = $request->getQueryParam('type');
        $documents = $this->em->getRepository(Document::class)->list($id, $resposible, $title, $type, 20, $index * 20);
        $total = $this->em->getRepository(Document::class)->listTotal($id, $resposible, $title, $type)['total'];
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

    public function documentsReceivedTable(Request $request, Response $response)
    {
        $user = $this->getLogged(true);
        $destiny = $user->getId();
        $id = $request->getAttribute('route')->getArgument('id');
        $index = $request->getQueryParam('index');
        $title = $request->getQueryParam('title');
        $type = $request->getQueryParam('type');
        $documents = $this->em->getRepository(DocumentDestiny::class)->list($id, $destiny, $title, $type, 20, $index * 20);
        $total = $this->em->getRepository(DocumentDestiny::class)->listTotal($id, $destiny, $title, $type)['total'];
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

    public function documentsCategoryTable(Request $request, Response $response)
    {
        $this->getLogged(true);
        $index = $request->getQueryParam('index');
        $categories = $this->em->getRepository(DocumentCategory::class)->list(20, $index * 20);
        $total = $this->em->getRepository(DocumentCategory::class)->listTotal()['total'];
        $partial = ($index * 20) + sizeof($categories);
        $partial = $partial <= $total ? $partial : $total;

        return $response->withJson([
            'status' => 'ok',
            'message' => $categories,
            'total' => (int)$total,
            'partial' => $partial,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }

    public function categoryDelete(Request $request, Response $response)
    {
        $this->getLogged(true);
        $id = $request->getAttribute('route')->getArgument('id');
        $this->em->getRepository(DocumentCategory::class)->categoryDelete($id);
        die();
    }

    public function updateStatus(Request $request, Response $response)
    {
        try {
            $id = $request->getAttribute('route')->getArgument('id');

            $destiny = $this->em->getRepository(DocumentDestiny::class)->find($id);
            $destiny->setStatus(0);
            $this->em->getRepository(DocumentDestiny::class)->save($destiny);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Successfully Update Status!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }



public
function messagesTable(Request $request, Response $response)
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
    $title = $request->getQueryParam('title');
    $active = $request->getQueryParam('active');
    $messages = $this->em->getRepository(Message::class)->listDashboard($id, $title, $active, 20, $index * 20);

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
    $tasks = $this->em->getRepository(ActivityDeal::class)->listDashboardNotNull($id, $user, $today, 20, $index * 20);
    $tasksNull = $this->em->getRepository(ActivityDeal::class)->listDashboardNull($id, $user, $today, 20, $index * 20);
    $total = array_merge($tasks, $tasksNull);

    return $response->withJson([
        'status' => 'ok',
        'message' => $total,
    ], 200)
        ->withHeader('Content-type', 'application/json');
}

public
function leadsTable(Request $request, Response $response)
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

public
function leadDelete(Request $request, Response $response)
{
    $this->getLogged(true);
    $id = $request->getAttribute('route')->getArgument('id');
    $this->em->getRepository(Deal::class)->dealDelete($id);
    die();
}

public
function transactionsDelete(Request $request, Response $response)
{
    $this->getLogged(true);
    $id = $request->getAttribute('route')->getArgument('id');
    $this->em->getRepository(Transaction::class)->transactionsDelete($id);
    die();
}

public
function transactionssTable(Request $request, Response $response)
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

public
function rankingAdvisors(Request $request, Response $response)
{
    $this->getLogged(true);
    $id = $request->getAttribute('route')->getArgument('id');
    $index = $request->getQueryParam('index');
    $transactions = $this->em->getRepository(Transaction::class)->rankingAdvisors(20, $index * 20);

    return $response->withJson([
        'status' => 'ok',
        'message' => $transactions,
    ], 200)
        ->withHeader('Content-type', 'application/json');
}

public
function rankingManagers(Request $request, Response $response)
{
    $this->getLogged(true);
    $id = $request->getAttribute('route')->getArgument('id');
    $index = $request->getQueryParam('index');
    $transactions = $this->em->getRepository(Transaction::class)->rankingManagers(20, $index * 20);

    return $response->withJson([
        'status' => 'ok',
        'message' => $transactions,
    ], 200)
        ->withHeader('Content-type', 'application/json');
}

public
function rankingManagersGroup(Request $request, Response $response)
{
    $user = $this->getLogged(true);
    $id = $user->getId();
    $index = $request->getQueryParam('index');
    $transactions = $this->em->getRepository(Transaction::class)->rankingManagersGroup($id, 20, $index * 20);

    return $response->withJson([
        'status' => 'ok',
        'message' => $transactions,
    ], 200)
        ->withHeader('Content-type', 'application/json');
}
}