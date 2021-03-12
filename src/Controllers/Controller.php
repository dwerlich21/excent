<?php
namespace App\Controllers;
use App\Models\Entities\User;
use Doctrine\ORM\EntityManager;
use App\Models\Entities\Candidate;
use App\Services\NovoService;
use App\Helpers\Session;
abstract class Controller
{
    protected $em;
    protected $renderer;
    protected $baseUrl = BASEURL;
    protected $env = ENV;

    public function __construct(EntityManager $entityManager, $renderer)
    {
        $this->em = $entityManager;
        $this->renderer = $renderer;
    }

    protected function getLogged(bool $excepetion = false)
    {
        $user = Session::get('sisgg');
        if (!$user) {
            if ($excepetion) throw new \Exception("Sessão expirada");
            Session::set('redirect', $_SERVER["REQUEST_URI"]);
            Session::set('errorMsg', 'Você precisa se autenticar');
            $this->redirect('login');
            exit;
        }
        $user = $this->em->getRepository(User::class)->find($user);
        return $user;
    }

    protected function redirect(string $url = '')
    {
        header("Location: {$this->baseUrl}{$url}");
        die();
    }
}