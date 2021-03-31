<?php


namespace App\Controllers;


use App\Helpers\Utils;
use App\Helpers\Validator;
use App\Models\Entities\Countries;
use App\Models\Entities\Deal;
use App\Models\Entities\Transaction;
use App\Models\Entities\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TransactionsController extends Controller
{
    public function transactions(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $countries = $this->em->getRepository(Countries::class)->findBy([], ['name' => 'asc']);
        $users = $this->em->getRepository(User::class)->findBy(['type' => 4, 'active' => 1], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'transactions/index.phtml', 'menuActive' => ['transactions'],
            'user' => $user, 'deals' => $deals, 'countries' => $countries, 'users' => $users]);
    }

    public function saveTransactions(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['transactionId'] ?? 0;
            $id = $data['user'];
            $us = $this->em->getRepository(User::class)->find($id);
            $country = $us->getCountry()->getId();
            $fields = [
                'user' => 'User'
            ];
            Validator::requireValidator($fields, $data);
            $message = new Transaction();
            if ($data['transactionId'] > 0) {
                $message = $this->em->getRepository(Transaction::class)->find($data['transactionId']);
            }
            $message->setWithdrawals(Utils::saveMoney($data['withdrawals']))
                ->setDeposit(Utils::saveMoney($data['deposit']))
                ->setUser($this->em->getReference(User::class, ($data['user'])))
                ->setResponsible($user)
                ->setCountry($this->em->getReference(Countries::class, $country));
            $this->em->getRepository(Transaction::class)->save($message);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Successfully registered transaction!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }
}