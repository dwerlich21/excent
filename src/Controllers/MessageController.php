<?php


namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Entities\ActivityDeal;
use App\Models\Entities\Deal;
use App\Models\Entities\Message;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
class MessageController extends Controller
{
    public function message(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $today = date('Y-m-d');
        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'messages/index.phtml', 'menuActive' => ['messages'],
            'user' => $user, 'deals' => $deals, 'activities' => $activities]);
    }

    public function viewReceived(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $today = date('Y-m-d');
        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $message = $this->em->getRepository(Message::class)->find($id);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'messages/view.phtml', 'menuActive' => ['messages'],
            'user' => $user, 'deals' => $deals, 'activities' => $activities, 'message' => $message]);
    }

    private function saveDocumentFile($files, Message $document): Message
    {
        $folder = UPLOAD_FOLDER;
        $documentFile = $files['projectFile'];
        if ($documentFile && $documentFile->getClientFilename()) {
            $time = time();
            $extension = explode('.', $documentFile->getClientFilename());
            $extension = end($extension);
            $target = "{$folder}{$time}documentFile.{$extension}";
            $documentFile->moveTo($target);
            $document->setDocumentFile($target);
        }
        return $document;
    }

    public function saveMessage(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $this->em->beginTransaction();
            $data = (array)$request->getParams();
            $files = $request->getUploadedFiles();
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
            $message = $this->saveDocumentFile($files, $message);
            $message->setTitle($data['title'])
                ->setDescription($data['description'])
                ->setUser($user)
                ->setActive($data['active']);
            $this->em->getRepository(Message::class)->save($message);
            $this->em->commit();
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