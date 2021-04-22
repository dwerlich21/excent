<?php


namespace App\Controllers;

use App\Helpers\Date;
use App\Helpers\Validator;
use App\Models\Entities\ActivityDeal;
use App\Models\Entities\Countries;
use App\Models\Entities\Deal;
use App\Models\Entities\User;
use Exception;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
class DealController extends Controller
{
    public function deal(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $today = date('Y-m-d');
        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $countries = $this->em->getRepository(Countries::class)->findBy([], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'deals/index.phtml', 'menuActive' => ['deals'],
            'user' => $user, 'deals' => $deals, 'countries' => $countries, 'activities' => $activities]);
    }

    public function viewDeal(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $today = date('Y-m-d');
        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $id = $request->getAttribute('route')->getArgument('id');
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $client = $this->em->getRepository(Deal::class)->findOneBy(['id' => $id]);
        $countries = $this->em->getRepository(Countries::class)->findBy([], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'deals/viewDeal.phtml', 'menuActive' => ['deals'],
            'user' => $user, 'deals' => $deals, 'client' => $client, 'countries' => $countries, 'activities' => $activities]);
    }

    public function saveDeal(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['dealId'] ?? 0;
            $date = date('Y-m-d H:i:s');
            $fields = [
                'name' => 'Name',
                'company' => 'Company',
                'email' => 'Email',
                'phone' => 'Phone',
                'status' => 'Status',
                'office' => 'Office'
            ];
            Validator::requireValidator($fields, $data);
            if ($data['dealId'] == 0) {
                $us = $this->em->getRepository(Deal::class)->findOneBy(['email' => $data['email']]);
                if($us) throw new Exception('E-mail already registered');
            }
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
            $type = 0;
            if ($data['type'] != null) $type = $data['type'];
            $activity = new ActivityDeal();
            $activity->setActivity($data['activityDeal'])
                ->setStatus(0)
                ->setUser($user)
                ->setDate(\DateTime::createFromFormat('Y-m-d H:i:s', $date))
                ->setDescription('')
                ->setType($type)
                ->setDeal($this->em->getReference(Deal::class, $id));
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
            $date = \date('Y-m-d');
            $data = (array)$request->getParams();
            $fields = [
                'date' => 'Date',
                'activity' => 'Activity',
                'options' => 'Type'
            ];
            Validator::requireValidator($fields, $data);
            $pendence = $this->em->getRepository(ActivityDeal::class)->activityVerify($user, $date);
//            if ($pendence != '') throw new Exception('You have pending tasks, finish them beforehand!');
            $task = new ActivityDeal();
            $description = '';
            if ($data['description']) $description = $data['description'];
            $task->setDate(new \DateTime($data['date']))
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

    public function updateActivityStatus(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $id = $data['taskId'];
            $task = $this->em->getRepository(ActivityDeal::class)->find($id);
            $task->setStatus(0)
                ->setDescription($data['taskDescription']);
            $this->em->getRepository(ActivityDeal::class)->save($task);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Congratulations! Task Completed!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }
}