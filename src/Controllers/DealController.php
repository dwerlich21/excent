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
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $countries = $this->em->getRepository(Countries::class)->findBy([], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'deals/index.phtml', 'menuActive' => ['deals'],
            'user' => $user, 'deals' => $deals, 'countries' => $countries]);
    }

    public function viewDeal(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $client = $this->em->getRepository(Deal::class)->findOneBy(['id' => $id]);
        $countries = $this->em->getRepository(Countries::class)->findBy([], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'deals/viewDeal.phtml', 'menuActive' => ['deals'],
            'user' => $user, 'deals' => $deals, 'client' => $client, 'countries' => $countries]);
    }

    public function saveDeal(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['dealId'] ?? 0;
            $date = date('Y-m-d');
            $hour = \date('H:i');
            $fields = [
                'name' => 'Name',
                'company' => 'Company',
                'email' => 'Email',
                'phone' => 'Phone',
                'status' => 'Status',
                'office' => 'Office'
            ];
            Validator::requireValidator($fields, $data);
            $deal = new Deal();
            if ($data['dealId'] > 0) {
                $deal = $this->em->getRepository(Deal::class)->find($data['dealId']);
            }
            $deal->setCompany($data['company'])
                ->setEmail($data['email'])
                ->setName($data['name'])
                ->setPhone($data['phone'])
                ->setOffice($data['office'])
                ->setType($data['typeDeal'])
                ->setStatus($data['status'])
                ->setCountry($this->em->getReference(Countries::class, $data['country']))
                ->setResponsible($user);
            $this->em->getRepository(Deal::class)->save($deal);
            if ($data['dealId'] != 0) {
                $id = intval($data['dealId']);
            } else {
                $newDeal = $this->em->getRepository(Deal::class)->findOneBy([],['id' => 'desc']);
                $id = $newDeal->getId();
            }
            $activity = new ActivityDeal();
            $activity->setActivity($data['activityDeal'])
                ->setStatus(0)
                ->setUser($user)
                ->setDate(\DateTime::createFromFormat('Y-m-d', $date))
                ->setDescription('')
                ->setType($data['type'])
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
            $description = '';
            if ($data['description']) $description = $data['description'];
            if ($data['time']) $time = \DateTime::createFromFormat('H:i', $data['time']);
            $task->setDate(\DateTime::createFromFormat('d/m/Y', $data['date']))
                ->setTime($time)
                ->setType($data['options'])
                ->setStatus($data['status'])
                ->setUser($user)
                ->setActivity($data['activity'])
                ->setDescription($description)
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