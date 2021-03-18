<?php


namespace App\Controllers;

use App\helpers\Validator;
use App\Models\Entities\Deal;
use App\Models\Entities\Document;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
class DocumentController extends Controller
{
    public function document(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $clients = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId()]);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'documents/index.phtml', 'menuActive' => ['documents'],
            'user' => $user, 'clients' => $clients]);
    }

    private function saveDocumentFile($files, Document $document): Document
    {
        $folder = UPLOAD_FOLDER;
        $documentFile = $files['projectFile'];
        if ($documentFile && $documentFile->getDealFilename()) {
            $time = time();
            $extension = explode('.', $documentFile->getDealFilename());
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
}