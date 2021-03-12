<?php


namespace App\Controllers;


use App\Helpers\Date;
use App\helpers\Validator;
use App\Models\Entities\Attendance;
use App\Models\Entities\CabinetDetails;
use App\Models\Entities\DocumentType;
use App\Models\Entities\Institution;
use App\Models\Entities\Docs;
use App\Models\Entities\Meetings;
use App\Models\Entities\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DocsController extends Controller
{
    public function docs(Request $request, Response $response)
    {
        $user = $this->getLogged();
        return $this->renderer->render($response, 'default.phtml', ['page' => 'docs/docs.phtml', 'menuActive' => ['docs'],
            'user' => $user]);
    }

    public function docsRegister(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $docs = $this->em->getRepository(Docs::class)->find($id??0);
        $documentsType = $this->em->getRepository(DocumentType::class)->findBy(['active' => 1, 'cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        $institutions = $this->em->getRepository(Institution::class)->findBy(['cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        $attendances = $this->em->getRepository(Attendance::class)->findBy(['cabinet' => $user->getCabinet()->getId()], ['id' => 'desc']);
        $meetings = $this->em->getRepository(Meetings::class)->findBy(['cabinet' => $user->getCabinet()->getId()], ['id' => 'desc']);
        if ($id != 0 && $user->getCabinet()->getId() != $docs->getCabinet()->getId()) $this->redirect('documentos');
        return $this->renderer->render($response, 'default.phtml', ['page' => 'docs/docsRegister.phtml', 'menuActive' => ['docs'],
            'user' => $user, 'documentsType' => $documentsType, 'docs' => $docs, 'institutions' => $institutions,
            'attendances' => $attendances, 'meetings' => $meetings]);
    }

    public function numberDoc($user, $type)
    {
        $year = \date('Y');
        $number = $this->em->getRepository(Docs::class)->number($user, $type, $year);
        $intNumber = intval($number['total']);
        $intNumber += 1;

        return '0' . $intNumber . '/' . $year;
    }

    public function saveDocs(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged(true);
            $data = (array)$request->getParams();
            $fields = [
                'type' => 'Tipo',
                'subjectMatter' => 'Assunto',
                'recipient' => 'Destinatário',
                'description' => 'Descrição',
            ];
            Validator::requireValidator($fields, $data);
            $docs = new Docs();
            $data['id'] ?? 0;
            if ($data['id'] > 0) {
                $docs = $this->em->getRepository(Docs::class)->find($data['id']);
            }
            $meeting = $institution = $attendance = null;
            if ($data['meeting']) $meeting = $this->em->getReference(Meetings::class, $data['meeting']);
            if ($data['institution']) $institution = $this->em->getReference(Institution::class, $data['institution']);
            if ($data['attendance']) $attendance = $this->em->getReference(Attendance::class, $data['attendance']);
            $docs->setType($this->em->getReference(DocumentType::class, $data['type']))
                ->setSubjectMatter($data['subjectMatter'])
                ->setRecipient($data['recipient'])
                ->setAttendance($attendance)
                ->setInstitution($institution)
                ->setMeeting($meeting)
                ->setDescription($data['description'])
                ->setResponsible($user)
                ->setCabinet($user->getCabinet());
            if ($data['id'] == 0) $docs->setNumber($this->numberDoc($user, $data['type']));
            $this->em->getRepository(Docs::class)->save($docs);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Documento cadastrado com sucesso',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function pdf(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $docs = $this->em->getRepository(Docs::class)->find($id);
        $cabinetDetail = $this->em->getRepository(CabinetDetails::class)->findOneBy(['cabinet' => $user->getCabinet()->getId()]);
        $date = \App\Helpers\Utils::dateInFull($docs->getDate()->format('d/m/Y'));
        $html = "
            <body>
                <div id=\"info\">
                    <div class=\"row\">
                        <div class=\"col\">
                            {$docs->getType()->getName()} nº <b>{$docs->getNumber()}</b>
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col\" id='cityDate'>
                            {$cabinetDetail->getCity()}, {$date}
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col\">
                            {$docs->getRecipient()}
                        </div>
                    </div>";
            if ($docs->getInstitution() != null):
            $html .= "
                    <div class=\"row\">
                        <div class=\"col\">
                            {$docs->getInstitution()->getName()}
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col\">
                            {$docs->getInstitution()->getAdress()} - {$docs->getInstitution()->getNeighborhood()},
                            {$docs->getInstitution()->getCityId()}  - CEP: {$docs->getInstitution()->getZipCode()}
                        </div>
                    </div>";
                endif;
            $html .= "
                    <div class=\"row\">
                        <div class=\"col\">
                            <br>Assunto: <b>{$docs->getSubjectMatter()}</b>
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col\">
                            <br><br>{$docs->getDescription()}
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