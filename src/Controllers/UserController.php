<?php


namespace App\Controllers;

use App\helpers\Validator;
use App\Models\Entities\Client;
use App\Models\Entities\User;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
class UserController extends Controller
{
    public function user(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $clients = $this->em->getRepository(Client::class)->findBy(['responsible' => $user->getId()]);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'users/index.phtml', 'menuActive' => ['users'],
            'user' => $user, 'clients' => $clients]);
    }

    public function saveUser(Request $request, Response $response)
    {
        try {
            $this->getLogged();
            $data = (array)$request->getParams();
            $data['userId'] ?? 0;
            $fields = [
                'email' => 'Email',
                'name' => 'Name',
                'type' => 'Type'
            ];
            Validator::requireValidator($fields, $data);
            $users = new User();
            if ($data['userId'] > 0) {
                $users = $this->em->getRepository(User::class)->find($data['userId']);
            }
            $users->setPassword('123')
                ->setEmail($data['email'])
                ->setName($data['name'])
                ->setActive($data['active'])
                ->setType($data['type']);
            $this->em->getRepository(User::class)->save($users);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Successfully registered user!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }
}