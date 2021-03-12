<?php


namespace App\Controllers;


use App\Helpers\Utils;
use App\helpers\Validator;
use App\Models\Entities\Attendance;
use App\Models\Entities\CabinetDetails;
use App\Models\Entities\Institution;
use App\Models\Entities\Neighborhood;
use App\Models\Entities\TypeOfService;
use App\Models\Entities\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AttendanceController extends Controller
{
    public function attendance(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $neighborhoods = $this->em->getRepository(Neighborhood::class)->findBy(['cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        $typeOfService = $this->em->getRepository(TypeOfService::class)->findBy(['active' => 1, 'cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        $users = $this->em->getRepository(User::class)->findBy(['active' => 1, 'cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        $institutions = $this->em->getRepository(Institution::class)->findBy(['cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'attendance/index.phtml', 'menuActive' => ['attendance'],
            'user' => $user, 'neighborhoods' => $neighborhoods, 'typeOfService' => $typeOfService,  'users' => $users, 'institutions' => $institutions]);
    }

    public function attendanceRegister(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $attendance = $this->em->getRepository(Attendance::class)->find($id??0);
        $institutions = $this->em->getRepository(Institution::class)->findBy(['cabinet' => $user->getCabinet()], ['name' => 'asc']);
        $neighborhoods = $this->em->getRepository(Neighborhood::class)->findBy(['cabinet' => $user->getCabinet()], ['name' => 'asc']);
        $typeOfService = $this->em->getRepository(TypeOfService::class)->findBy(['active' => 1, 'cabinet' => $user->getCabinet()], ['name' => 'asc']);
        if ($id != 0 && $user->getCabinet()->getId() != $attendance->getCabinet()->getId()) $this->redirect('atendimentos');
        return $this->renderer->render($response, 'default.phtml', ['page' => 'attendance/attendanceRegister.phtml', 'menuActive' => ['attendance'],
            'user' => $user, 'attendance' => $attendance, 'institutions' => $institutions, 'neighborhoods' => $neighborhoods, 'typeOfService' => $typeOfService]);
    }

    private function saveAttendanceFile($files, Attendance $attendance): Attendance
    {
        $folder = UPLOAD_FOLDER . 'atendimentos/';
        $attendanceFile = $files['attendanceFile'];
        if ($attendanceFile && $attendanceFile->getClientFilename()) {
            $time = time();
            $extension = explode('.', $attendanceFile->getClientFilename());
            $extension = end($extension);
            $target = "{$folder}{$time}attendanceFile.{$extension}";
            $attendanceFile->moveTo($target);
            $attendance->setAttendanceFile($target);
        }
        return $attendance;

    }

    public function saveAttendance(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $this->em->beginTransaction();
            $data = (array)$request->getParams();
            $files = $request->getUploadedFiles();
            $fields = [
                'type' => 'Tipo',
                'institution' => 'Órgão',
                'neighborhood' => 'Bairro',
                'reporting' => 'Relatante',
                'email' => 'E-mail',
                'status' => 'Status',
                'description' => 'Descrição'
            ];
            Validator::requireValidator($fields, $data);
            $attendance = new Attendance();
            if ($data['id'] > 0) {
                $attendance = $this->em->getRepository(Attendance::class)->find($data['id']);
            }
            $attendance = $this->saveAttendanceFile($files, $attendance);
            $attendance->setType($this->em->getReference(TypeOfService::class, $data['type']))
                ->setInstitution($this->em->getReference(Institution::class, $data['institution']))
                ->setNeighborhood($this->em->getReference(Neighborhood::class, $data['neighborhood']))
                ->setReporting($data['reporting'])
                ->setEmail($data['email'])
                ->setStatus($data['status'])
                ->setResponsible($user)
                ->setCabinet($user->getCabinet())
                ->setDescription($data['description']);
            $this->em->getRepository(Attendance::class)->save($attendance);
            $this->em->commit();
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Atendimento cadastrado com sucesso',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function viewAttendance(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $attendance = $this->em->getRepository(Attendance::class)->find($id);
        if ($id != 0 && $user->getCabinet()->getId() != $attendance->getCabinet()->getId()) $this->redirect('atendimentos');
        return $this->renderer->render($response, 'default.phtml', ['page' => 'attendance/viewAttendance.phtml', 'menuActive' => ['attendance'],
            'user' => $user, 'attendance' => $attendance,]);
    }

    public function attendanceCsv(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getQueryParam('id');
        $type = $request->getQueryParam('type');
        $responsible = $request->getQueryParam('responsible');
        $status = $request->getQueryParam('status');
        $institution = $request->getQueryParam('institution');
        $neighborhood = $request->getQueryParam('neighborhood');
        $attendances = $this->em->getRepository(Attendance::class)->list($user, $id, $type, $responsible, $status, $neighborhood, $institution );
        $filename = "ListaDeAtendimentos-" . time() . ".csv";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: text/csv; charset=UTF-8");
        $out = fopen("php://output", 'w');
        fputs($out, "\xEF\xBB\xBF"); // UTF-8 BOM !!!!!
        fputcsv($out, ['ID', 'TIPO', 'RESPONSÁVEL', 'INSTITUIÇÃO', 'BAIRRO', 'STATUS'], ',', '"');
        foreach ($attendances as $attendance) {
            fputcsv($out, [$attendance['id'], $attendance['type'], $attendance['user'], $attendance['institution'], $attendance['neighborhood'],
                $attendance['status']]);
        }
        fclose($out);
        exit;
    }

    public function attendancePdf(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getQueryParam('id');
        $type = $request->getQueryParam('type');
        $responsible = $request->getQueryParam('responsible');
        $status = $request->getQueryParam('status');
        $institution = $request->getQueryParam('institution');
        $neighborhood = $request->getQueryParam('neighborhood');
        $attendances = $this->em->getRepository(Attendance::class)->list($user, $id, $type, $responsible, $status, $neighborhood, $institution );
        $cabinetDetail = $this->em->getRepository(CabinetDetails::class)->findOneBy(['cabinet' => $user->getCabinet()->getId()]);
        $html = "
        <h3>Atendimentos</h3>
        <br>
        <table>
            <thead>
            <tr>
                <th>Data</th>
                <th>Tipo</th>
                <th>Instituição</th>
                <th>Bairro</th>
                <th>Status</th>
            </tr>
            </thead>";
        foreach ($attendances as $attendance):
        $html .= "
         <tr>
               <td>{$attendance['date']}</td> 
               <td>{$attendance['type']}</td> 
               <td>{$attendance['institution']}</td> 
               <td>{$attendance['neighborhood']}</td> 
               <td>{$attendance['status']}</td> 
        </tr>
        ";
        endforeach;
        $html .= "</table>
        ";
        $mpdf = new \Mpdf\Mpdf(['margin_header' => 10, 'margin_footer' => 5, 'margin_left' => 20, 'margin_right' => 10]);
        $mpdf->SetHTMLHeader($cabinetDetail->getHeader());
        $mpdf->SetHTMLFooter($cabinetDetail->getFooter());
        $mpdf->SetMargins(0, 0, 50);
        $css = file_get_contents("assets/css/pdf.css");
        $mpdf->WriteHTML($css, 1);
        $mpdf->WriteHTML($html);
        $mpdf->Output("ListaDeAtendimentos-" . time() . ".pdf", \Mpdf\Output\Destination::INLINE);
        die();
    }
}