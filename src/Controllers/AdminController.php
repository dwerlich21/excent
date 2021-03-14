<?php


namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Entities\Client;
use App\Models\Entities\Document;
use App\Models\Entities\Task;
use App\Models\Entities\User;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;


class AdminController extends Controller
{

    public function index(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $clients = $this->em->getRepository(Client::class)->findBy(['responsible' => $user->getId()]);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'index.phtml', 'menuActive' => ['dashboard'],
            'user' => $user, 'clients' => $clients]);
    }

    public function user(Request $request, Response $response)
    {
        $user = $this->getLogged();
        return $this->renderer->render($response, 'default.phtml', ['page' => 'users/index.phtml', 'menuActive' => ['users'],
            'user' => $user]);
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

    public function client(Request $request, Response $response)
    {
        $user = $this->getLogged();
        return $this->renderer->render($response, 'default.phtml', ['page' => 'clients/index.phtml', 'menuActive' => ['clients'],
            'user' => $user]);
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

    public function document(Request $request, Response $response)
    {
        $user = $this->getLogged();
        return $this->renderer->render($response, 'default.phtml', ['page' => 'documents/index.phtml', 'menuActive' => ['documents'],
            'user' => $user]);
    }

    private function saveDocumentFile($files, Document $document): Document
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

    public function saveDocument(Request $request, Response $response)
    {
        try {
            $this->getLogged();
            $this->em->beginTransaction();
            $data = (array)$request->getParams();
            $files = $request->getUploadedFiles();
            $fields = [
                'title' => 'Title',
                'description' => 'Description',
                'type' => 'Type'
            ];
            Validator::requireValidator($fields, $data);
            $document = new Document();
            $document = $this->saveDocumentFile($files, $document);
            $document->setTitle($data['title'])
                ->setDescription($data['description']);
            $this->em->getRepository(Document::class)->save($document);
            $this->em->commit();
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Successfully registered document!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function saveTask(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['userId'] ?? 0;
            $fields = [
                'date' => 'Date',
                'client' => 'Client',
                'action' => 'Action'
            ];
            Validator::requireValidator($fields, $data);
            $task = new Task();
            $time = null;
            if ($data['time']) $time = \DateTime::createFromFormat('H:i', $data['time']);
            $task->setDate(\DateTime::createFromFormat('d/m/Y', $data['date']))
                ->setTime($time)
                ->setUser($user)
                ->setAction($data['action'])
                ->setDescription($data['description'])
                ->setClient($this->em->getReference(Client::class,$data['client']));
            $this->em->getRepository(Task::class)->save($task);
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


