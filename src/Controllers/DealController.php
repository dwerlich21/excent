<?php


namespace App\Controllers;

use App\helpers\Validator;
use App\Models\Entities\Deal;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
class DealController extends Controller
{
    public function deal(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $clients = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId()]);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'deals/index.phtml', 'menuActive' => ['deals'],
            'user' => $user, 'clients' => $clients]);
    }

    public function saveDeal(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['clientId'] ?? 0;
            $fields = [
                'name' => 'Nome',
                'company' => 'Empresa',
                'email' => 'E-mail',
                'phone' => 'Telefone',
                'status' => 'Status',
                'office' => 'Cargo'
            ];
            Validator::requireValidator($fields, $data);
            $client = new Deal();
            if ($data['clientId'] > 0) {
                $client = $this->em->getRepository(Deal::class)->find($data['clientId']);
            }
            $client->setCompany($data['company'])
                ->setEmail($data['email'])
                ->setName($data['name'])
                ->setPhone($data['phone'])
                ->setOffice($data['office'])
                ->setStatus($data['status'])
                ->setResponsible($user);
            $this->em->getRepository(Deal::class)->save($client);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Successfully registered client!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }
}