<?php


namespace App\Controllers;


use App\Helpers\Date;
use App\helpers\Validator;
use App\Models\Entities\CabinetDetails;
use App\Models\Entities\Meetings;
use App\Models\Entities\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EventsController extends Controller
{
    public function events(Request $request, Response $response)
    {
        $user = $this->getLogged();
        return $this->renderer->render($response, 'default.phtml', ['page' => 'events/index.phtml', 'menuActive' => ['events'],
            'user' => $user]);
    }

    public function eventsRegister(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $meetings = $this->em->getRepository(Meetings::class)->find($id??0);
        $users = $this->em->getRepository(User::class)->findBy(['cabinet' => $user->getCabinet()->getId(), 'active' => 1], ['name' => 'asc']);
        if ($id != 0 && $user->getCabinet()->getId() != $meetings->getCabinet()->getId()) $this->redirect('eventos');
        return $this->renderer->render($response, 'default.phtml', ['page' => 'events/eventsRegister.phtml', 'menuActive' => ['events'],
            'user' => $user, 'meetings' => $meetings, 'users' => $users]);
    }


    public function saveEvents(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $fields = [
                'start' => 'Início',
                'adress' => 'Endereço',
                'description' => 'Descrição',
                'theme' => 'Pauta',
                'type' => 'Tipo de Evento'
            ];
            Validator::requireValidator($fields, $data);
            $meetings = new Meetings();
            if ($data['id'] > 0) {
                $meetings = $this->em->getRepository(Meetings::class)->find($data['id']);
            }
            $end = null;
            if ($data['end']) $end = new \DateTime($data['end']);
            $meetings->setEnd($end)
                ->setStart(new  \DateTime($data['start']))
                ->setAdress($data['adress'])
                ->setContact($data['contact'])
                ->setEmail($data['email'])
                ->setCabinet($user->getCabinet())
                ->setTheme($data['theme'])
                ->setType($data['type'])
                ->setDescription($data['description']);
            $this->em->getRepository(Meetings::class)->save($meetings);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Reunião cadastrada com sucesso',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function viewEvents(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $meetings = $this->em->getRepository(Meetings::class)->find($id);
        if ($id != 0 && $user->getCabinet()->getId() != $meetings->getCabinet()->getId()) $this->redirect('eventos');
        return $this->renderer->render($response, 'default.phtml', ['page' => 'events/viewEvents.phtml', 'menuActive' => ['events'],
            'user' => $user, 'meetings' => $meetings,]);
    }

    public function pdf(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $meetings = $this->em->getRepository(Meetings::class)->find($id);
        $cabinetDetail = $this->em->getRepository(CabinetDetails::class)->findOneBy(['cabinet' => $user->getCabinet()->getId()]);
        $date = \App\Helpers\Utils::dateInFull($meetings->getStart()->format('d/m/Y'));
        $html = "
            <body>
                <div id=\"info\">
                    <div class=\"row\">
                        <div class=\"col\" id='cityDate'>
                            {$cabinetDetail->getCity()}, {$date}
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col\">
                            {$meetings->getTypeStr()}: {$meetings->getTheme()}
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col\">
                            <br><br>{$meetings->getDescription()}
                        </div>
                    </div>
                </div>
            </body>
            ";
        $mpdf = new \Mpdf\Mpdf(['margin_header' => 10, 'margin_footer' => 5, 'margin_left' => 20, 'margin_right' => 10]);
        $mpdf->SetHTMLHeader($cabinetDetail->getHeader());
        $mpdf->SetHTMLFooter($cabinetDetail->getFooter());
        $mpdf->SetMargins(0, 0, 50);
        $css = file_get_contents("assets/css/pdf.css");
        $mpdf->WriteHTML($css, 1);
        $mpdf->WriteHTML($html);
        $mpdf->Output('teste.pdf', \Mpdf\Output\Destination::INLINE);
        die();
    }
}