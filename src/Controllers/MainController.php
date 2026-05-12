<?php
namespace Fauza\Template\Controllers;

use Fauza\Template\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Fauza\Template\Utils\SessionHandler;
use Slim\Views\PhpRenderer;
class MainController
{
    function home(Request $req, Response $resp, array $args): Response
    {
        SessionHandler::initSession();
        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title' => "Packet Delivery — Connexion",
            'year'  => date('Y'),
            'csrf'  => SessionHandler::CsrfToken(),
        ];
        return $view->render($resp, 'login.php', $data);
    }

    function login(Request $req, Response $resp, array $args): Response
    {
        SessionHandler::initSession();
        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title' => "Packet Delivery — Connexion",
            'year'  => date('Y'),
            'csrf'  => SessionHandler::CsrfToken(),
        ];

        $body = $req->getParsedBody() ?? [];

        if (!SessionHandler::VerifyCsrf($body['_csrf'] ?? null)) {
            $data['message'] = "Requête invalide.";
            return $view->render($resp, 'login.php', $data);
        }

        $email = filter_var($body['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = (string)($body['password'] ?? '');
        if ($email === false || $password === '') {
            $data['message'] = "Email ou mot de passe invalide.";
            return $view->render($resp, 'login.php', $data);
        }

        $emp = Database::getInstance()->loginCheck($email, $password);
        if ($emp === null) {
            $data['message'] = "Utilisateur non trouvé.";
            return $view->render($resp, 'login.php', $data);
        }

        SessionHandler::RegenerateId();
        SessionHandler::SaveInSession('user', $emp);

        $target = $emp->estLivreur ? '/delivery' : '/admin';
        return $resp->withHeader('Location', $target)->withStatus(302);
    }

    function adminWindow(Request $req, Response $resp, array $args): Response
    {
        SessionHandler::initSession();
        if (!SessionHandler::IsLoggedIn()) {
            return $resp->withHeader('Location', '/')->withStatus(302);
        }
        $user = SessionHandler::GetRawDataFromSession('user');
        if ($user->estLivreur) {
            return $resp->withHeader('Location', '/delivery')->withStatus(302);
        }

        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title'    => "Packet Delivery — Espace administratif",
            'year'     => date('Y'),
            'user'     => $user,
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
        SessionHandler::initSession();
        if (!SessionHandler::IsLoggedIn()) {
            return $resp->withHeader('Location', '/')->withStatus(302);
        }
        $user = SessionHandler::GetRawDataFromSession('user');
        if (!$user->estLivreur) {
            return $resp->withHeader('Location', '/admin')->withStatus(302);
        }

        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title'        => "Packet Delivery — Espace livreur",
            'year'         => date('Y'),
            'user'         => $user,
            'dateAffichee' => 'Mardi 12 mai 2026',
            'message'      => '',
        ];
        return $view->render($resp, 'deliverWindow.php', $data);
    }

    function logout(Request $req, Response $resp, array $args): Response
    {
        SessionHandler::initSession();
        SessionHandler::DestroySession();
        return $resp->withHeader('Location', '/')->withStatus(302);
    }
}
