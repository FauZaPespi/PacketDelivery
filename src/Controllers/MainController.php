<?php
namespace Fauza\Template\Controllers;

use Fauza\Template\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
class MainController
{
    function home(Request $req, Response $resp, array $args): Response
    {
        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title' => "Packet Delivery — Connexion",
            'year'  => date('Y'),
        ];
        return $view->render($resp, 'login.php', $data);
    }
    function login(Request $req, Response $resp, array $args): Response
    {
        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title' => "Packet Delivery — Window",
            'year'  => date('Y'),
        ];
        // Check username and password here
        $email = $req->getParsedBody()['email'];
        $password = $req->getParsedBody()['password'];
        
        if (Database::getInstance()->loginCheck($email, $password) === null) {
            $data['message'] = "Utilisateur non trouvé.";
            return $view->render($resp, 'login.php', $data);
        }

        if (Database::getInstance()->loginCheck($email, $password)->role) {
            return $resp->withHeader('Location', '/admin')->withStatus(301);
        } else {
            return $resp->withHeader('Location', '/delivery')->withStatus(301);
        }
    }
    
    function adminWindow(Request $req, Response $resp, array $args): Response
    {
        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title'    => "Packet Delivery — Espace administratif",
            'year'     => date('Y'),
            'user'     => ['prenom' => 'Jean', 'nom' => 'Dupont'],
            'paquets'  => [
                ['numero' => 'PKG-1201-CH', 'statut' => 'Pas encore livré'],
                ['numero' => 'PKG-1219-CH', 'statut' => 'En cours de livraison'],
                ['numero' => 'PKG-1227-CH', 'statut' => 'Livré'],
            ],
            'livreurs' => [
                ['prenom' => 'Alice', 'nom' => 'Martin'],
                ['prenom' => 'Luc',   'nom' => 'Bernard'],
            ],
        ];
        return $view->render($resp, 'adminWindow.php', $data);
    }

    function deliverWindow(Request $req, Response $resp, array $args): Response
    {
        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title'        => "Packet Delivery — Espace livreur",
            'year'         => date('Y'),
            'user'         => ['prenom' => 'Alice', 'nom' => 'Martin'],
            'dateAffichee' => 'Mardi 12 mai 2026',
            'message'      => '',
        ];
        return $view->render($resp, 'deliverWindow.php', $data);
    }
}
