<?php


namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Entities\ActivityDeal;
use App\Models\Entities\Deal;
use App\Models\Entities\DocumentDestiny;
use App\Models\Entities\Message;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class CalendarController extends Controller
{
    public function calendar(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $deals = $this->em->getRepository(Deal::class)->findBy(['responsible' => $user->getId(), 'type' => 0], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'calendar/index.phtml', 'menuActive' => ['calendar'],
            'user' => $user, 'deals' => $deals]);
    }

    public function getTasks(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $activities = $this->em->getRepository(ActivityDeal::class)->findBy(['user' => $user->getId()]);
            $eventsArray = [];
            foreach ($activities as $activity) {
                if ($activity->getType() >= 2) {
                    $id = $activity->getId();
                    $start = $activity->getDate()->format('Y-m-d H:i:s');
                    $title = $activity->getActivity();
                    $deal = $activity->getDeal()->getName();
                    $dealId = $activity->getDeal()->getId();
                    if ($activity->getType() == 2) {
                        $color = 'blue';
                    } else if ($activity->getType() == 3) {
                        $color = 'red';
                    } else if ($activity->getType() == 4) {
                        $color = 'green';
                    } else if ($activity->getType() == 5) {
                        $color = 'grey';
                    } else if ($activity->getType() == 6) {
                        $color = 'purple';
                    } else {
                        $color = 'yellow';
                    }
                    $eventsArray[] = ['id' => $id, 'start' => $start, 'title' => $title, 'deal' => $deal, 'color' => $color, 'dealId' => $dealId];
                }
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