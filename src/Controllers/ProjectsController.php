<?php


namespace App\Controllers;


use App\helpers\Validator;
use App\Models\Entities\Attendance;
use App\Models\Entities\CabinetDetails;
use App\Models\Entities\Projects;
use App\Models\Entities\TypeOfProject;
use App\Models\Entities\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProjectsController extends Controller
{
    public function projects(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $typeOfProject = $this->em->getRepository(TypeOfProject::class)->findBy(['cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        $users = $this->em->getRepository(User::class)->findBy(['active' => 1, 'cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'projects/index.phtml', 'menuActive' => ['projects'],
            'user' => $user, 'typeOfProject' => $typeOfProject, 'users' => $users]);
    }

    public function projectsRegister(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $projects = $this->em->getRepository(Projects::class)->find($id??0);
        $typeOfProject = $this->em->getRepository(TypeOfProject::class)->findBy(['active' => 1, 'cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        if ($id != 0 && $user->getCabinet()->getId() != $projects->getCabinet()->getId()) $this->redirect('projetos');
        return $this->renderer->render($response, 'default.phtml', ['page' => 'projects/projectsRegister.phtml', 'menuActive' => ['projects'],
            'user' => $user, 'projects' => $projects, 'typeOfProject' => $typeOfProject]);
    }

    private function saveProjectFile($files, Projects $projects): Projects
    {
        $folder = UPLOAD_FOLDER . 'projetos/';
        $projectsFile = $files['projectFile'];
        if ($projectsFile && $projectsFile->getClientFilename()) {
            $time = time();
            $extension = explode('.', $projectsFile->getClientFilename());
            $extension = end($extension);
            $target = "{$folder}{$time}projectFile.{$extension}";
            $projectsFile->moveTo($target);
            $projects->setProjectFile($target);
        }
        return $projects;

    }
    public function saveProjects(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $this->em->beginTransaction();
            $data = (array)$request->getParams();
            $files = $request->getUploadedFiles();
            $fields = [
                'type' => 'Tipo',
                'subjectMatter' => 'Assunto',
                'description' => 'Descrição',
                'status' => 'Status'
            ];
            Validator::requireValidator($fields, $data);
            $projects = new Projects();
            if ($data['id'] > 0) {
                $projects = $this->em->getRepository(Projects::class)->find($data['id']);
            }
            $projects = $this->saveProjectFile($files, $projects);
            $projects->setType( $this->em->getReference(TypeOfProject::class, $data['type']))
                ->setResponsible($user)
                ->setSubjectMatter($data['subjectMatter'])
                ->setNumber($data['number'])
                ->setCabinet($user->getCabinet())
                ->setStatus($data['status'])
                ->setLink($data['link'])
                ->setDescription($data['description']);
            $this->em->getRepository(Projects::class)->save($projects);
            $this->em->commit();
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Projeto cadastrado com sucesso',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function viewProjects(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $projects = $this->em->getRepository(Projects::class)->find($id);
        if ($id != 0 && $user->getCabinet()->getId() != $projects->getCabinet()->getId()) $this->redirect('projetos');
        return $this->renderer->render($response, 'default.phtml', ['page' => 'projects/viewProjects.phtml', 'menuActive' => ['projects'],
            'user' => $user, 'projects' => $projects,]);
    }

    public function projectCsv(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getQueryParam('id');
        $type = $request->getQueryParam('type');
        $responsible = $request->getQueryParam('responsible');
        $status = $request->getQueryParam('status');
        $start = $request->getQueryParam('start');
        $end = $request->getQueryParam('end');
        $projects = $this->em->getRepository(Projects::class)->list($user, $id, $type, $status, $responsible, $start, $end);
        $filename = "ListaDeAtendimentos-" . time() . ".csv";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: text/csv; charset=UTF-8");
        $out = fopen("php://output", 'w');
        fputs($out, "\xEF\xBB\xBF"); // UTF-8 BOM !!!!!
        fputcsv($out, ['DATA', 'TIPO', 'DESCRIÇÃO', 'RESPONSÁVEL', 'STATUS'], ',', '"');
        foreach ($projects as $project) {
            fputcsv($out, [$project['date'], $project['name'], $project['subjectMatter'], $project['user'], $project['status']]);
        }
        fclose($out);
        exit;
    }

    public function projectPdf(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getQueryParam('id');
        $type = $request->getQueryParam('type');
        $responsible = $request->getQueryParam('responsible');
        $status = $request->getQueryParam('status');
        $start = $request->getQueryParam('start');
        $end = $request->getQueryParam('end');
        $projects = $this->em->getRepository(Projects::class)->list($user, $id, $type, $status, $responsible, $start, $end);
        $cabinetDetail = $this->em->getRepository(CabinetDetails::class)->findOneBy(['cabinet' => $user->getCabinet()->getId()]);
        $html = "
        <h3>Projetos</h3>
        <br>
        <table>
            <thead>
            <tr>
                <th>Data</th>
                <th>Tipo</th>
                <th>Assunto</th>
                <th>Responsável</th>
                <th>Status</th>
            </tr>
            </thead>";
        foreach ($projects as $project):
            $html .= "
         <tr>
               <td>{$project['date']}</td> 
               <td>{$project['name']}</td> 
               <td>{$project['subjectMatter']}</td> 
               <td>{$project['user']}</td> 
               <td>{$project['status']}</td> 
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
        $mpdf->Output("ListaDeProjetos-" . time() . ".pdf", \Mpdf\Output\Destination::INLINE);
        die();
    }
}