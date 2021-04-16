<?php


namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Entities\ActivityDeal;
use App\Models\Entities\Deal;
use App\Models\Entities\DocumentMyFolder;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class DocumentController extends Controller
{
    public function myFolder(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $today = date('Y-m-d');
        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'documents/index.phtml', 'menuActive' => 'documents',
            'section' => 'myFolder', 'subMenu' => 'documentsGroup', 'user' => $user, 'deals' => $deals,
            'activities' => $activities]);
    }

    public function companyFiles(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $today = date('Y-m-d');
        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'documents/companyFiles.phtml', 'menuActive' => 'documents',
            'section' => 'companyFiles', 'subMenu' => 'documentsGroup', 'user' => $user, 'deals' => $deals, 'activities' => $activities]);
    }

    private function saveDocumentFile($files, DocumentMyFolder $document): DocumentMyFolder
    {
        $folder = UPLOAD_FOLDER . 'my-folder/';
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

    public function saveDocumentMyFolder(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $this->em->beginTransaction();
            $data = (array)$request->getParams();
            $files = $request->getUploadedFiles();
            $fields = [
                'title' => 'Title'
            ];
            Validator::requireValidator($fields, $data);
            $document = new DocumentMyFolder();
            $document = $this->saveDocumentFile($files, $document);
            $document->setTitle($data['title'])
                ->setResponsible($user);
            $this->em->getRepository(DocumentMyFolder::class)->save($document);
            $this->em->commit();
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Successfully Registered Document!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function saveFolder(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $fields = [
                'nameFolder' => 'Name Folders'
            ];
            Validator::requireValidator($fields, $data);
            $folder = new Folders();
            $folder->setName($data['nameFolder']);
            $folder->setResponsible($user);
            $this->em->getRepository(Folders::class)->save($folder);
            $acess = new FolderAcess();
            $acess->setUser($user)
                ->setName($data['NameFolder']);
            $this->em->getRepository(Folders::class)->save($acess);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Successfully Registered Folders!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }
}
