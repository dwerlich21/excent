<?php


namespace App\Controllers;

use App\helpers\Validator;
use App\Models\Entities\Deal;
use App\Models\Entities\Message;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
class MessageController extends Controller
{
    public function message(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $deal = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId()]);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'messages/index.phtml', 'menuActive' => ['messages'],
            'user' => $user, 'deal' => $deal]);
    }

    public function saveMessage(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['messageId'] ?? 0;
            $fields = [
                'title' => 'Title',
                'description' => 'Description',
                'active' => 'Status'
            ];
            Validator::requireValidator($fields, $data);
            $message = new Message();
            if ($data['messageId'] > 0) {
                $message = $this->em->getRepository(Message::class)->find($data['messageId']);
            }
            $message->setTitle($data['title'])
                ->setDescription($data['description'])
                ->setUser($user)
                ->setActive($data['active']);
            $this->em->getRepository(Message::class)->save($message);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Successfully registered message!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }
}