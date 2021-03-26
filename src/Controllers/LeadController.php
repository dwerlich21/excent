<?php


namespace App\Controllers;


use App\helpers\Validator;
use App\Models\Entities\ActivityDeal;
use App\Models\Entities\Countries;
use App\Models\Entities\Deal;
use App\Models\Entities\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LeadController extends Controller
{
    public function lead(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $deal = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId()]);
        $countries = $this->em->getRepository(Countries::class)->findBy([], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'leads/index.phtml', 'menuActive' => ['leads'],
            'user' => $user, 'deal' => $deal, 'countries' => $countries]);
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
                'email' => 'Email',
                'name' => 'Name',
                'phone' => 'Phone',
                'country' => 'Country'
            ];
            Validator::requireValidator($fields, $data);
            $leads = new Deal();
            if ($data['leadId'] > 0) {
                $leads = $this->em->getRepository(Deal::class)->find($data['leadId']);
            }
            $leads->setPhone($data['phone'])
                ->setEmail($data['email'])
                ->setName($data['name'])
                ->setResponsible($user)
                ->setStatus(0)
                ->setCountry($this->em->getReference(Countries::class, $data['country']));

            $this->em->getRepository(Deal::class)->save($leads);
            $deal = $this->em->getRepository(Deal::class)->findOneBy([],['id' => 'desc']);
            $id = $deal->getId();
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