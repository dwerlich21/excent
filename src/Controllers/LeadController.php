<?php


namespace App\Controllers;


use App\Helpers\Validator;
use App\Models\Entities\ActivityDeal;
use App\Models\Entities\Countries;
use App\Models\Entities\Deal;
use App\Models\Entities\User;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LeadController extends Controller
{
    public function lead(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $countries = $this->em->getRepository(Countries::class)->findBy([], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'leads/index.phtml', 'menuActive' => ['leads'],
            'user' => $user, 'deals' => $deals, 'countries' => $countries]);
    }

    public function saveLead(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['leadId'] ?? 0;
            $date = date('Y-m-d');
            $hour = \date('H:i');

            $fields = [
                'emailLead' => 'Email',
                'nameLead' => 'Name',
                'phoneLead' => 'Phone',
                'countryLead' => 'Country'
            ];
            Validator::requireValidator($fields, $data);
            if ($data['leadId'] == 0) {
                $us = $this->em->getRepository(Deal::class)->findOneBy(['email' => $data['emailLead']]);
                if($us) throw new Exception('E-mail already registered');
            }
            $leads = new Deal();
            if ($data['leadId'] > 0) {
                $leads = $this->em->getRepository(Deal::class)->find($data['leadId']);
            }
            $leads->setPhone($data['phoneLead'])
                ->setEmail($data['emailLead'])
                ->setName($data['nameLead'])
                ->setResponsible($user)
                ->setStatus(0)
                ->setType(0)
                ->setCountry($this->em->getReference(Countries::class, $data['countryLead']));

            $this->em->getRepository(Deal::class)->save($leads);
            $lead = $this->em->getRepository(Deal::class)->findOneBy([],['id' => 'desc']);
            $id = $lead->getId();
            $activity = new ActivityDeal();
            $activity->setActivity('Lead created')
                ->setStatus(0)
                ->setUser($user)
                ->setDate(\DateTime::createFromFormat('Y-m-d', $date))
                ->setDescription('')
                ->setType(0)
                ->setDeal($this->em->getReference(Deal::class, $id))
                ->setTime(\DateTime::createFromFormat('H:i', $hour));
            $this->em->getRepository(ActivityDeal::class)->save($activity);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Successfully registered lead!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }
}