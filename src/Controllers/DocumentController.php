<?php


namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Entities\Deal;
use App\Models\Entities\Document;
use App\Models\Entities\DocumentCategory;
use App\Models\Entities\DocumentDestiny;
use App\Models\Entities\User;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class DocumentController extends Controller
{
    public function document(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $categories = $this->em->getRepository(DocumentCategory::class)->findBy([], ['nameCategory' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'documents/index.phtml', 'menuActive' => 'documents',
            'section' => 'DocumentsSubmit', 'subMenu' => 'documentsGroup', 'user' => $user, 'deals' => $deals, 'categories' => $categories]);
    }

    public function category(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'documents/category.phtml', 'menuActive' => 'documents',
            'section' => 'DocumentsCategory', 'subMenu' => 'documentsGroup', 'user' => $user, 'deals' => $deals]);
    }

    public function received(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $categories = $this->em->getRepository(DocumentCategory::class)->findBy([], ['nameCategory' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'documents/received.phtml', 'menuActive' => ['documents'],
            'section' => 'DocumentsReceived', 'subMenu' => 'documentsGroup', 'user' => $user, 'deals' => $deals, 'categories' => $categories]);
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
            $user = $this->getLogged();
            $date = date('Y-m-d H:i');
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
                ->setType($this->em->getReference(DocumentCategory::class, $data['type']))
                ->setResponsible($user)
                ->setCreated(\DateTime::createFromFormat('Y-m-d H:i', $date))
                ->setDescription($data['description']);
            $this->em->getRepository(Document::class)->save($document);
            $this->em->commit();
            if ($user->getType() != 3) $destinations = $this->em->getRepository(User::class)->findBy(['type' => $data['destiny']]);
            if ($user->getType() == 3) $destinations = $this->em->getRepository(User::class)->findBy(['type' => $data['destiny'], 'manager' => $user->getId()]);
            $oldDocument = $this->em->getRepository(Document::class)->findOneBy(['responsible' => $user->getId()], ['id' => 'desc']);
            foreach ($destinations as $destiny):
                $documentDestiny = new DocumentDestiny;
                $documentDestiny->setDocument($oldDocument)
                    ->setStatus(1)
                    ->setDestiny($destiny);
                $this->em->getRepository(DocumentDestiny::class)->save($documentDestiny);
            endforeach;
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

    public function saveCategory(Request $request, Response $response)
    {

        try {
            $this->getLogged();
            $data = (array)$request->getParams();
            $fields = [
                'nameCategory' => 'Name Category'
            ];
            Validator::requireValidator($fields, $data);
            $category = new DocumentCategory();
            $category->setNameCategory($data['nameCategory']);
            $category->setActive($data['active']);
            $this->em->getRepository(DocumentCategory::class)->save($category);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Successfully Registered Document Category!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }
}
