<?php


namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Entities\ActivityDeal;
use App\Models\Entities\CompanyFiles;
use App\Models\Entities\Deal;
use App\Models\Entities\DocumentMyFolder;
use App\Models\Entities\FolderAccess;
use App\Models\Entities\User;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use RecursiveDirectoryIterator;
use DirectoryIterator;
use FilesystemIterator;

class DocumentController extends Controller
{

    public function myFolder(Request $request, Response $response, $typeAccess)
    {
        $user = $this->getLogged();
        $dir = $request->getQueryParam('dir');
        if ($dir == 'uploads/my-folder/' || $dir == 'uploads/company-files/' || $dir == 'uploads/') $this->redirect('documents/my-folder');
        $today = date('Y-m-d');
        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $baseDir = 'uploads/my-folder/' . $user->getFolder() . '/';
        if ($dir != null) $baseDir = $dir;
        $openDir = dir($baseDir);
        $strDir = strrpos(substr($dir, 0, -1), '/');
        $backDir = substr($dir, 0, $strDir + 1);
        $typesOrder = $this->folderByType($openDir, $baseDir);
        $folder = 'my-folder';
        return $this->renderer->render($response, 'default.phtml', ['page' => 'documents/index.phtml', 'menuActive' => ['documents'], 'folder' => $folder,
            'section' => 'myFolder', 'subMenu' => 'documentsGroup', 'user' => $user, 'deals' => $deals, 'typeAccess' => $typeAccess,
            'activities' => $activities, 'openDir' => $openDir, 'baseDir' => $baseDir, 'backDir' => $backDir, 'types' => $typesOrder]);
    }

    public function folders(Request $request, Response $response, $typeAccess)
    {
        $user = $this->getLogged();
        if ($user->getType() > 2) $this->redirect('');
        $dir = $request->getQueryParam('dir');
        $today = date('Y-m-d');
        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $baseDir = 'uploads/my-folder/';
        if ($dir != null) $baseDir = $dir;
        $openDir = dir($baseDir);
        $strDir = strrpos(substr($dir, 0, -1), '/');
        $backDir = substr($dir, 0, $strDir + 1);
        $typesOrder = $this->folderByType($openDir, $baseDir);
        $folder = 'folders';
        return $this->renderer->render($response, 'default.phtml', ['page' => 'documents/index.phtml', 'menuActive' => ['documents'], 'folder' => $folder,
            'section' => 'folders', 'subMenu' => 'documentsGroup', 'user' => $user, 'deals' => $deals, 'typeAccess' => $typeAccess,
            'activities' => $activities, 'openDir' => $openDir, 'baseDir' => $baseDir, 'backDir' => $backDir, 'types' => $typesOrder]);
    }

    public function companyFiles(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $dir = $request->getQueryParam('dir');
//        if ($dir == 'uploads/my-folder/' || $dir == 'uploads/company-files/' || $dir == 'uploads/') $this->redirect('documents/company-files');
        $today = date('Y-m-d');
        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        $baseDir = 'uploads/company-files/';
        if ($dir != null) $baseDir = $dir;
        $openDir = dir($baseDir);
        $strDir = strrpos(substr($dir, 0, -1), '/');
        $backDir = substr($dir, 0, $strDir + 1);
        $typesOrder = $this->folderByType($openDir, $baseDir);
        $folders = $this->em->getRepository(CompanyFiles::class)->findBy([], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'documents/companyFiles.phtml', 'menuActive' => ['documents'],
            'section' => 'companyFiles', 'subMenu' => 'documentsGroup', 'user' => $user, 'deals' => $deals, 'activities' => $activities,
            'baseDir' => $baseDir, 'backDir' => $backDir, 'types' => $typesOrder, 'folders' => $folders]);
    }

    public function viewCompanyFiles(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $dir = $request->getQueryParam('dir');
        $id = $request->getAttribute('route')->getArgument('id');
        if ($dir == 'uploads/my-folder/') $this->redirect('documents/company-files');
        if ($dir == 'uploads/company-files/') $this->redirect('documents/company-files');
        if ($dir == 'uploads/') $this->redirect('documents/company-files');
        $baseDir = $dir;
        $openDir = dir($baseDir);
        $strDir = strrpos(substr($dir, 0, -1), '/');
        $backDir = substr($dir, 0, $strDir + 1);
        $typesOrder = $this->folderByType($openDir, $baseDir);
        $today = date('Y-m-d');
        $activities = $this->em->getRepository(ActivityDeal::class)->totalCalendar(0, $user, $today);
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'documents/companyFilesView.phtml', 'menuActive' => ['documents'],
            'section' => 'companyFiles', 'subMenu' => 'documentsGroup', 'user' => $user, 'deals' => $deals, 'id' => $id,
            'activities' => $activities, 'openDir' => $openDir, 'baseDir' => $baseDir, 'backDir' => $backDir, 'types' => $typesOrder]);
    }

    public function folderByType($openDir, $baseDir)
    {
//        die(var_dump($openDir));
        $type = $pdf = $excel = $word = $image = $outer = [];
        while ($document = $openDir->read()) {
            if ($document != '.' && $document != '..') {
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

    private function saveDocumentFile($files, $destiny, $title)
    {
        $folder = UPLOAD_FOLDER . $destiny;
        $documentFile = $files['projectFile'];
        if ($documentFile && $documentFile->getClientFilename()) {
            $extension = explode('.', $documentFile->getClientFilename());
            $extension = end($extension);
            $target = "{$folder}{$title}.{$extension}";
            $documentFile->moveTo($target);
        }
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
            $this->saveDocumentFile($files, $data['destiny'], $title);
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
            $id = $request->getQueryParam('id');
            $dir = UPLOAD_FOLDER . $dir;
            if (dir($dir)) {
                if (rmdir($dir) == true) {
                    rmdir($dir);
                    $status = 'ok';
                    $message = 'Folder successfully deleted!';
                } else {
                    throw new \Exception('Folder must be empty to be deleted!');
                }
            } else {
                unlink($dir);
                $status = 'ok';
                $message = 'File successfully deleted!';
            }
            if ($id) {
                $this->em->getRepository(CompanyFiles::class)->deleteFolder($id);
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

    public function saveFolder(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $fields = [
                'nameFolder' => 'Name Folders'
            ];
            Validator::requireValidator($fields, $data);
            $title = str_replace(' ', '-', $data['nameFolder']);
            $destiny = $data['destiny'] . $title;
            $folder = new CompanyFiles();
            $folder->setName($data['nameFolder'])
                ->setResponsible($user)
                ->setFolder($destiny);
            mkdir(UPLOAD_FOLDER . $data['destiny'] . $title);
            $this->em->getRepository(CompanyFiles::class)->save($folder);
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

    public function filectime(Request $request, Response $response)
    {
        $this->getLogged();
        $route = $request->getQueryParam('time');
        $time = date('d/m/Y H:i:s', filectime($route));
        return $response->withJson([
            'status' => 'ok',
            'message' => $time,
        ], 200)
            ->withHeader('Content-type', 'application/json');
    }
}
