<?php


namespace App\Controllers;

use App\helpers\Validator;
use App\Models\Entities\Client;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
class ClientController extends Controller
{
    public function client(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $clients = $this->em->getRepository(Client::class)->findBy(['responsible' => $user->getId()]);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'clients/index.phtml', 'menuActive' => ['clients'],
            'user' => $user, 'clients' => $clients]);
    }

    public function saveClient(Request $request, Response $response)
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
                'office' => 'Cargo'
            ];
            Validator::requireValidator($fields, $data);
            $client = new Client();
            if ($data['clientId'] > 0) {
                $client = $this->em->getRepository(Client::class)->find($data['clientId']);
            }
            $client->setCompany($data['company'])
                ->setEmail($data['email'])
                ->setName($data['name'])
                ->setPhone($data['phone'])
                ->setOffice($data['office'])
                ->setStatus(0)
                ->setResponsible($user);
            $this->em->getRepository(Client::class)->save($client);
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