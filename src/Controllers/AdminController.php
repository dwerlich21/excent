<?php


namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Entities\AccessLog;
use App\Models\Entities\Events;
use App\Models\Entities\Gallery;
use App\Models\Entities\Institution;
use App\Models\Entities\Meetings;
use App\Models\Entities\Neighborhood;
use App\Models\Entities\TypeOfProject;
use App\Models\Entities\TypeOfService;
use App\Models\Entities\User;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;


class AdminController extends Controller
{

    public function index(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $neighborhoods = $this->em->getRepository(Neighborhood::class)->findBy(['cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        $typeOfService = $this->em->getRepository(TypeOfService::class)->findBy(['active' => 1, 'cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        $users = $this->em->getRepository(User::class)->findBy(['active' => 1, 'cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        $institutions = $this->em->getRepository(Institution::class)->findBy(['cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        $typeOfProject = $this->em->getRepository(TypeOfProject::class)->findBy(['active' => 1, 'cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'index.phtml', 'menuActive' => ['dashboard'],
            'user' => $user, 'neighborhoods' => $neighborhoods, 'typeOfService' => $typeOfService, 'users' => $users,
            'institutions' => $institutions, 'typeOfProject' => $typeOfProject]);
    }

    public function users(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $users = $this->em->getRepository(User::class)->findBy([], ['name' => 'asc']);
        $hits = $this->em->getRepository(AccessLog::class)->findBy(['user' => null], ['id' => 'desc'], 30);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'users/index.phtml', 'menuActive' => ['dashboard'],
            'user' => $user, 'hits' => $hits, 'users' => $users]);
    }

    public function gallery(Request $request, Response $response)
    {
        $user = $this->getLogged(); //usario logado
        $images = $this->em->getRepository(Gallery::class)->findBy(['cabinet' => $user->getCabinet()], ['id' => 'desc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'gallery.phtml', 'menuActive' => ['gallery'],
            'user' => $user, 'images' => $images]);
    }

    private function saveGalleryFile($files, Gallery $img): Gallery
    {
        $folder = UPLOAD_FOLDER . 'imagens/';
        $imgFile = $files['img'];
        if ($imgFile && $imgFile->getClientFilename()) {
            $time = time();
            $extension = explode('.', $imgFile->getClientFilename());
            $extension = end($extension);
            $target = "{$folder}{$time}imgFile.{$extension}";
            $imgFile->moveTo($target);
            $img->setImgFile($target);
        }
        return $img;
    }

    public function saveImage(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $this->em->beginTransaction();
            $data = (array)$request->getParams();
            $files = $request->getUploadedFiles();
            $fields = [

            ];
            Validator::requireValidator($fields, $data);
            $img = new Gallery();
            $img = $this->saveGalleryFile($files, $img);
            $img->setCabinet($user->getCabinet());
            $this->em->getRepository(Gallery::class)->save($img);
            $this->em->commit();
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Imagem cadastrada com sucesso',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function schedule(Request $request, Response $response)
    {
        $user = $this->getLogged();
        return $this->renderer->render($response, 'default.phtml', ['page' => 'schedule/index.phtml', 'menuActive' => ['schedule'],
            'user' => $user,]);
    }

    public function getSchedule(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $meetings = $this->em->getRepository(Meetings::class)->findBy(['cabinet' => $user->getCabinet()->getId()]);
            $eventsArray = [];
            foreach ($meetings as $meeting) {
                $id = $meeting->getId();
                $start = $end = $meeting->getStart()->format('Y-m-d H:i:s');
                if ($meeting->getEnd()) $end = $meeting->getEnd()->format('Y-m-d H:i:s');
                $title = $meeting->getTheme();
                $adress = $meeting->getAdressStr();
                if ($meeting->getType() == 1) {
                    $color = 'blue';
                } else if ($meeting->getType() == 2) {
                    $color = 'red';
                } else {
                    $color = 'yellow';
                }
                $eventsArray[] = ['id' => $id, 'start' => $start, 'title' => $title, 'adress' => $adress, 'end' => $end, 'color' => $color];
            }
            return $response->withJson(
                $eventsArray
                , 201)
                ->withHeader('Content-type', 'application/json');
        } catch (Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }
}


