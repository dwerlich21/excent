<?php


namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Entities\ActivityDeal;
use App\Models\Entities\Countries;
use App\Models\Entities\Deal;
use App\Models\Entities\User;
use Exception;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
class UserController extends Controller
{
    public function user(Request $request, Response $response)
    {
        $user = $this->getLogged();
        if ($user->getType() == 4 || $user->getType() == 5) $this->redirect();
        $today = date('Y-m-d');
        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $users = $this->em->getRepository(User::class)->findBy(['type' => 3], ['name' => 'asc']);
        $countries = $this->em->getRepository(Countries::class)->findBy([], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'users/index.phtml', 'menuActive' => ['users'],
            'user' => $user, 'deals' => $deals, 'users' => $users, 'countries' => $countries, 'activities' => $activities]);
    }

    public function editUser(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $id = $request->getAttribute('route')->getArgument('id');
            $fields = [
                'email' => 'Email',
                'name' => 'Name',
                'password' => 'password'
            ];
            Validator::requireValidator($fields, $data);
            $us = $this->em->getRepository(User::class)->find($id);
            if ($us->getId() != $user->getId()) $this->redirect();
            $us->setEmail($data['email'])
                ->setName($data['name'])
                ->setCountry($this->em->getReference(Countries::class, $data['userCountry']))
                ->setPassword(password_hash($data['password'], PASSWORD_ARGON2I));
            $this->em->getRepository(User::class)->save($us);
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

    public function saveUser(Request $request, Response $response)
    {
        try {
            $this->getLogged();
            $data = (array)$request->getParams();
            $data['userId'] ?? 0;
            $fields = [
                'email' => 'Email',
                'name' => 'Name',
                'password' => 'Password',
                'country' => 'Country',
                'type' => 'Type'
            ];
            Validator::requireValidator($fields, $data);
            if ($data['userId'] == 0) {
                $user = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
                if($user) throw new Exception('E-mail already registered');
            }
            $users = new User();
            if ($data['userId'] > 0) {
                $users = $this->em->getRepository(User::class)->find($data['userId']);
            }
            $manager = null;
            if ($data['manager'] != 0) $manager = $this->em->getReference(User::class, $data['manager']);
            $users->setEmail($data['email'])
                ->setName($data['name'])
                ->setActive($data['active'])
                ->setType($data['type'])
                ->setCountry($this->em->getReference(Countries::class, $data['country']))
                ->setManager($manager)
                ->setPassword(password_hash($data['password'], PASSWORD_ARGON2I));
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