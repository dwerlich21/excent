<?php


namespace App\Controllers;


use App\Helpers\Date;
use App\Helpers\Utils;
use App\helpers\Validator;
use App\Models\Entities\Contacts;
use App\Models\Entities\TypeContact;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ContactsController extends Controller
{
    public function contacts(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $typeContacts = $this->em->getRepository(TypeContact::class)->findBy([], ['name' => 'asc']);
        return $this->renderer->render($response, 'default.phtml', ['page' => 'contacts/contacts.phtml', 'menuActive' => ['contacts'],
            'user' => $user, 'typeContacts' => $typeContacts,]);
    }

    public function contactsRegister(Request $request, Response $response)
    {
        $user = $this->getLogged();
        $id = $request->getAttribute('route')->getArgument('id');
        $contacts = $this->em->getRepository(Contacts::class)->find($id??0);
        $typeContacts = $this->em->getRepository(TypeContact::class)->findBy([], ['name' => 'asc']);
        if ($id != 0 && $user->getCabinet()->getId() != $contacts->getCabinet()->getId()) $this->redirect('contatos');
        return $this->renderer->render($response, 'default.phtml', ['page' => 'contacts/contactsRegister.phtml', 'menuActive' => ['contacts'],
            'user' => $user, 'typeContacts' => $typeContacts, 'contacts' => $contacts]);
    }


    public function saveContacts(Request $request, Response $response)
    {
        try {
            $user = $this->getLogged();
            $data = (array)$request->getParams();
            $data['userId'] ?? 0;
            $fields = [
                'name' => 'Nome',
                'phone' => 'Telefone',
                'email' => 'E-mail',
                'dateOfBirth' => 'Data de Nascimento',
                'typeContact' => 'Tipo de Contato',
                'zipCode' => 'CEP',
                'ufId' => 'Estado',
                'cityId' => 'Cidade',
                'neighborhood' => 'Bairro',
                'adress' => 'Endereço',
            ];
            Validator::requireValidator($fields, $data);
            $contacts = new Contacts();
            if ($data['userId'] > 0) {
                $contacts = $this->em->getRepository(Contacts::class)->find($data['userId']);
            }
            $contacts->setName(ucwords(strtolower($data['name'])))
                ->setDateOfBirth(\DateTime::createFromFormat('d/m/Y', $data['dateOfBirth']))
                ->setTypeContact($this->em->getReference(TypeContact::class, $data['typeContact']))
                ->setZipCode($data['zipCode'])
                ->setUfId($data['ufId'])
                ->setCityId($data['cityId'])
                ->setNeighborhood($data['neighborhood'])
                ->setAdress($data['adress'])
                ->setPhone($data['phone'])
                ->setCabinet($user->getCabinet())
                ->setEmail($data['email']);
            $this->em->getRepository(Contacts::class)->save($contacts);
            return $response->withJson([
                'status' => 'ok',
                'message' => 'Contato cadastrado com sucesso',
            ], 201)
                ->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            return $response->withJson(['status' => 'error',
                'message' => $e->getMessage(),])->withStatus(500);
        }
    }

}