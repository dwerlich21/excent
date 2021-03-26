<?php


namespace App\Controllers;

use App\helpers\Validator;
use App\Models\Entities\ActivityDeal;
use App\Models\Entities\Countries;
use App\Models\Entities\Deal;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
class DealController extends Controller
{
    public function deal(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId()]);
        $countries = $this->em->getRepository(Countries::class)->findBy([], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'deals/index.phtml', 'menuActive' => ['deals'],
            'user' => $user, 'deals' => $deals, 'countries' => $countries]);
    }

    public function viewDeal(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId()]);
        $client = $this->em->getRepository(Deal::class)->findOneBy(['id' => $id]);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'deals/viewDeal.phtml', 'menuActive' => ['deals'],
            'user' => $user, 'deals' => $deals, 'client' => $client]);
    }

    public function saveDeal(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['clientId'] ?? 0;
            $date = date('Y-m-d');
            $hour = \date('H:i');
            $fields = [
                'name' => 'Nome',
                'company' => 'Empresa',
                'email' => 'E-mail',
                'phone' => 'Telefone',
                'status' => 'Status',
                'office' => 'Cargo'
            ];
            Validator::requireValidator($fields, $data);
            $deal = new Deal();
            if ($data['clientId'] > 0) {
                $deal = $this->em->getRepository(Deal::class)->find($data['clientId']);
            }
            $deal->setCompany($data['company'])
                ->setEmail($data['email'])
                ->setName($data['name'])
                ->setPhone($data['phone'])
                ->setOffice($data['office'])
                ->setType(1)
                ->setStatus($data['status'])
                ->setResponsible($user);
            $this->em->getRepository(Deal::class)->save($deal);
            $newDeal = $this->em->getRepository(Deal::class)->findOneBy([],['id' => 'desc']);
            $id = $newDeal->getId();
            $activity = new ActivityDeal();
            $activity->setActivity('Deal created')
                ->setStatus(1)
                ->setUser($user)
                ->setDate(\DateTime::createFromFormat('Y-m-d', $date))
                ->setDescription('')
                ->setType(0)
                ->setDeal($this->em->getReference(Deal::class, $id))
                ->setTime(\DateTime::createFromFormat('H:i', $hour));
            $this->em->getRepository(ActivityDeal::class)->save($activity);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Successfully registered Deal!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function saveActivityDeal(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $fields = [
                'date' => 'Date',
                'activity' => 'Activity',
                'options' => 'Type'
            ];
            Validator::requireValidator($fields, $data);
            $task = new ActivityDeal();
            $time = null;
            if ($data['time']) $time = \DateTime::createFromFormat('H:i', $data['time']);
            $task->setDate(\DateTime::createFromFormat('d/m/Y', $data['date']))
                ->setTime($time)
                ->setType($data['options'])
                ->setStatus(1)
                ->setUser($user)
                ->setActivity($data['activity'])
                ->setDescription($data['description'])
                ->setDeal($this->em->getReference(Deal::class, $data['dealId']));
            $this->em->getRepository(ActivityDeal::class)->save($task);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Successfully registered activity!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }
}