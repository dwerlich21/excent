<?php


namespace App\Controllers;

use App\helpers\Validator;
use App\Models\Entities\CabinetDetails;
use App\Models\Entities\DocumentType;
use App\Models\Entities\Institution;
use App\Models\Entities\Neighborhood;
use App\Models\Entities\TypeOfProject;
use App\Models\Entities\TypeOfService;
use App\Models\Entities\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ManagementController extends Controller
{
    public function neighborhoods(Request $request, Response $response)
    {
        $user = $this->getLogged();
        return $this->renderer->render($response, 'default.phtml', ['page' => 'management/neighborhoods.phtml',
            'menuActive' => ['neighborhoods', 'edit'], 'user' => $user]);
    }

    public function saveNeighborhoods(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['neighborhoodsId'] ?? 0;
            $fields = [
                'name' => 'Nome',
                'latitude' => 'Latitude',
                'longitude' => 'Longitude'
            ];
            Validator::requireValidator($fields, $data);
            $neighborhood = new Neighborhood();
            if ($data['neighborhoodsId'] > 0) {
                $neighborhood = $this->em->getRepository(Neighborhood::class)->find($data['neighborhoodsId']);
            }
            $neighborhood->setName($data['name'])
                ->setLatitude($data['latitude'])
                ->setCabinet($user->getCabinet())
                ->setLongitude($data['longitude']);
            $this->em->getRepository(Neighborhood::class)->save($neighborhood);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Bairro cadastrado com sucesso',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function documentType(Request $request, Response $response)
    {
        $user = $this->getLogged();
        return $this->renderer->render($response, 'default.phtml', ['page' => 'management/documentType.phtml',
            'menuActive' => ['documentType', 'edit'], 'user' => $user]);
    }

    public function saveDocumentType(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['documentTypeId'] ?? 0;
            $fields = [
                'name' => 'Nome',
                'active' => 'Status'
            ];
            Validator::requireValidator($fields, $data);
            $documentType = new DocumentType();
            if ($data['documentTypeId'] > 0) {
                $documentType = $this->em->getRepository(DocumentType::class)->find($data['documentTypeId']);
            }
            $documentType->setName($data['name'])
                ->setCabinet($user->getCabinet())
                ->setActive($data['active']);
            $this->em->getRepository(DocumentType::class)->save($documentType);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Tipo de Documento cadastrado com sucesso',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function attendanceType(Request $request, Response $response)
    {
        $user = $this->getLogged();
        return $this->renderer->render($response, 'default.phtml', ['page' => 'management/attendanceType.phtml',
            'menuActive' => ['typeOfService', 'edit'], 'user' => $user]);
    }

    public function saveAttendanceType(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['attendanceTypeId'] ?? 0;
            $fields = [
                'name' => 'Nome',
                'active' => 'Status'
            ];
            Validator::requireValidator($fields, $data);
            $documentType = new TypeOfService();
            if ($data['attendanceTypeId'] > 0) {
                $documentType = $this->em->getRepository(TypeOfService::class)->find($data['attendanceTypeId']);
            }
            $documentType->setName($data['name'])
                ->setCabinet($user->getCabinet())
                ->setActive($data['active']);
            $this->em->getRepository(TypeOfService::class)->save($documentType);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Tipo de Atendimento cadastrado com sucesso',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function typeOfProject(Request $request, Response $response)
    {
        $user = $this->getLogged();
        return $this->renderer->render($response, 'default.phtml', ['page' => 'management/typeOfProject.phtml',
            'menuActive' => ['typeOfProject', 'edit'], 'user' => $user]);
    }

    public function saveTypeOfProject(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['typeOfProjectId'] ?? 0;
            $fields = [
                'name' => 'Nome',
                'active' => 'Status'
            ];
            Validator::requireValidator($fields, $data);
            $documentType = new TypeOfProject();
            if ($data['typeOfProjectId'] > 0) {
                $documentType = $this->em->getRepository(TypeOfProject::class)->find($data['typeOfProjectId']);
            }
            $documentType->setName($data['name'])
                ->setCabinet($user->getCabinet())
                ->setActive($data['active']);
            $this->em->getRepository(TypeOfProject::class)->save($documentType);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Tipo de Projeto cadastrado com sucesso',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function cabinetDetails(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $cabinetDetails = $this->em->getRepository(CabinetDetails::class)->findOneBy(['cabinet' => $user->getCabinet()->getId()]);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'management/cabinetDetails.phtml',
            'menuActive' => ['cabinetDetails', 'edit'], 'user' => $user, 'cabinetDetails' => $cabinetDetails]);
    }

    public function saveCabinetDetails(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['id'] ?? 0;
            $fields = [
                'email' => 'E-mail',
                'city' => 'Cidade',
                'header' => 'Cabeçalho',
                'footer' => 'Rodapé',
                'birthdayMessage' => 'Mensagem de Aniversário'
            ];
            Validator::requireValidator($fields, $data);
            $documentType = new CabinetDetails();
            if ($data['id'] > 0) {
                $documentType = $this->em->getRepository(CabinetDetails::class)->find($data['id']);
            }
            $documentType->setEmail($data['email'])
                ->setCabinet($user->getCabinet())
                ->setHeader($data['header'])
                ->setCity($data['city'])
                ->setFooter($data['footer'])
                ->setBirthdayMessage($data['birthdayMessage']);
            $this->em->getRepository(CabinetDetails::class)->save($documentType);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Detalhes do Gabinete cadastrados com sucesso',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function users(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $users = $this->em->getRepository(User::class)->findBy(['cabinet' => $user->getCabinet()->getId()], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'management/user.phtml', 'menuActive' => ['users', 'edit'],
            'user' => $user, 'users' => $users]);
    }

    public function saveUser(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['userId'] ?? 0;
            $fields = [
                'email' => 'E-mail',
                'name' => 'Nome',
                'type' => 'Tipo'
            ];
            Validator::requireValidator($fields, $data);
            $users = new User();
            if ($data['userId'] > 0) {
                $users = $this->em->getRepository(User::class)->find($data['userId']);
            }
            $users->setPassword('123')
                ->setEmail($data['email'])
                ->setName($data['name'])
                ->setActive($data['active'])
                ->setCabinet($user->getCabinet())
                ->setType($data['type']);
            $this->em->getRepository(User::class)->save($users);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Usuário cadastrado com sucesso',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

    public function institution(Request $request, Response $response)
    {
        $user = $this->getLogged();
        return $this->renderer->render($response, 'default.phtml', ['page' => 'management/institution.phtml', 'menuActive' => ['institutions', 'edit'],
            'user' => $user]);
    }

    public function institutionsRegister(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $institutions = $this->em->getRepository(Institution::class)->find($id??0);
        if ($id != 0 && $user->getCabinet()->getId() != $institutions->getCabinet()->getId()) $this->redirect('intituicoes');
        return $this->renderer->render($response, 'default.phtml', ['page' => 'management/institutionRegister.phtml', 'menuActive' => ['institutions', 'edit'],
            'user' => $user, 'institutions' => $institutions]);
    }

    public function saveInstitutions(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['institutionsId'] ?? 0;
            $fields = [
                'name' => 'Nome',
                'email' => 'E-mail',
                'responsible' => 'Responsável',
                'phone' => 'Telefone',
                'zipCode' => 'CEP',
                'ufId' => 'Estado',
                'cityId' => 'Cidade',
                'neighborhood' => 'Bairro',
                'adress' => 'Endereço',
            ];
            Validator::requireValidator($fields, $data);
            $institution = new Institution();
            if ($data['institutionsId'] > 0) {
                $institution = $this->em->getRepository(Institution::class)->find($data['institutionsId']);
            }
            $institution->setEmail($data['email'])
                ->setName($data['name'])
                ->setCabinet($user->getCabinet())
                ->setPhone($data['phone'])
                ->setZipCode($data['zipCode'])
                ->setUfId($data['ufId'])
                ->setCityId($data['cityId'])
                ->setNeighborhood($data['neighborhood'])
                ->setAdress($data['adress'])
                ->setResponsible($data['responsible']);
            $this->em->getRepository(Institution::class)->save($institution);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Instituição cadastrada com sucesso',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }
}