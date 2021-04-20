<?php


namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Entities\ActivityDeal;
use App\Models\Entities\CompanyFiles;
use App\Models\Entities\Deal;
use App\Models\Entities\DocumentMyFolder;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use RecursiveDirectoryIterator;
use DirectoryIterator;
use FilesystemIterator;

class DocumentController extends Controller
{

    public function myFolder(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $dir = $request->getQueryParam('dir');
        $today = date('Y-m-d');
        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $baseDir = 'uploads/my-folder/' . $user->getFolder() . '/';
        if ($dir != null) $baseDir = $dir;
        $openDir = dir($baseDir);
        $strDir = strrpos(substr($dir, 0, -1), '/');
        $backDir = substr($dir, 0, $strDir + 1);
        $typesOrder = $this->folderByType($openDir, $baseDir);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'documents/index.phtml', 'menuActive' => ['documents'],
            'section' => 'myFolder', 'subMenu' => 'documentsGroup', 'user' => $user, 'deals' => $deals,
            'activities' => $activities, 'openDir' => $openDir, 'baseDir' => $baseDir, 'backDir' => $backDir, 'types' => $typesOrder]);
    }

    public function folderByType($openDir, $baseDir)
    {
        $type = $pdf = $excel = $word = $image = $outer = [];
        while ($document = $openDir->read()) {
            if ($document != '.' && $document != '..'){
                if (is_dir($baseDir . $document)) {
                    $type[] = $document;
                } elseif (substr($document, -3) == 'pdf') {
                    $pdf[] = $document;
                } elseif (substr($document, -3) == 'lsx' || substr($document, -3) == 'csv') {
                    $excel[] = $document;
                } elseif (substr($document, -3) == 'ocx') {
                    $word[] = $document;
                } elseif (substr($document, -3) == 'peg' || substr($document, -3) == 'jpg' || substr($document, -3) == 'png') {
                    $image[] = $document;
                } else {
                    $outer[] = $document;
                }
            }
        }
        $types = array_merge($type, $pdf, $excel, $word, $image, $outer);
        return $types;
    }

    public function companyFiles(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $today = date('Y-m-d');
        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $baseDir = 'uploads/company-files/';


        return $this->renderer->render($response, 'default.phtml', ['page' => 'documents/companyFiles.phtml', 'menuActive' => 'documents',
            'section' => 'companyFiles', 'subMenu' => 'documentsGroup', 'user' => $user, 'deals' => $deals, 'activities' => $activities]);
    }

    private function saveDocumentFile($files, DocumentMyFolder $document, $destiny, $title): DocumentMyFolder
    {
        $folder = UPLOAD_FOLDER . $destiny;
        $documentFile = $files['projectFile'];
        if ($documentFile && $documentFile->getClientFilename()) {
            $extension = explode('.', $documentFile->getClientFilename());
            $extension = end($extension);
            $target = "{$folder}{$title}.{$extension}";
            $documentFile->moveTo($target);
            $document->setDocumentFile($target);
        }
        return $document;
    }

    public function saveDocument(Request $request, Response $response)
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
            $title = str_replace(' ', '-', $data['title']);
            $document = new DocumentMyFolder();
            $document = $this->saveDocumentFile($files, $document, $data['destiny'], $title);
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

    public function registerFolder(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $fields = [
                'title' => 'Name'
            ];
            Validator::requireValidator($fields, $data);
            $title = str_replace(' ', '-', $data['title']);
            mkdir(UPLOAD_FOLDER . $data['destiny'] . $title);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Successfully Registered Folder!',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function deleteFolder(Request $request, Response $response)
    {
        try {
            $dir = $request->getQueryParam('dir');
            $dir = UPLOAD_FOLDER . $dir;
            if (dir($dir)) {
                rmdir($dir);
                if (rmdir($dir) == false) {
                   throw new \Exception('Folder must be empty to be deleted!');
                } else {
                    $status = 'ok';
                    $message = 'Folder successfully deleted!';
                }
            } else {
                unlink($dir);
                $status = 'ok';
                $message = 'File successfully deleted!';
            }
            return $response->withJson([
                'status' => $status,
                'message' => $message,
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }
}
