<?php
namespace Fauza\Template\Controllers;

use Fauza\Template\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Fauza\Template\Utils\SessionHandler;
use Slim\Views\PhpRenderer;

class MainController
{
    private function checkAdminAccess(Request $req, Response $resp): ?Response
    {
        SessionHandler::initSession();
        if (!SessionHandler::IsLoggedIn()) {
            return $resp->withHeader('Location', '/')->withStatus(302);
        }
        $user = SessionHandler::GetRawDataFromSession('user');
        if ($user->estLivreur) {
            return $resp->withHeader('Location', '/delivery')->withStatus(302);
        }
        return null;
    }

    private function checkLivreurAccess(Request $req, Response $resp): ?Response
    {
        SessionHandler::initSession();
        if (!SessionHandler::IsLoggedIn()) {
            return $resp->withHeader('Location', '/')->withStatus(302);
        }
        $user = SessionHandler::GetRawDataFromSession('user');
        if (!$user->estLivreur) {
            return $resp->withHeader('Location', '/admin')->withStatus(302);
        }
        return null;
    }

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
        $check = $this->checkAdminAccess($req, $resp);
        if ($check !== null) {
            return $check;
        }

        $user = SessionHandler::GetRawDataFromSession('user');
        $db = Database::getInstance();

        // Récupérer les paramètres de recherche
        $paquetQuery = $req->getQueryParams()['paquet_search'] ?? '';
        $livreurQuery = $req->getQueryParams()['livreur_search'] ?? '';

        // Rechercher ou lister
        $paquets = $paquetQuery
            ? $db->searchPaquets($paquetQuery)
            : $db->getAllPaquets();

        $livreurs = $livreurQuery
            ? $db->searchLivreurs($livreurQuery)
            : $db->getAllLivreurs();

        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title'        => "Packet Delivery — Espace administratif",
            'year'         => date('Y'),
            'user'         => $user,
            'paquets'      => $paquets,
            'livreurs'     => $livreurs,
            'csrf'         => SessionHandler::CsrfToken(),
        ];
        return $view->render($resp, 'adminWindow.php', $data);
    }

    function searchPaquets(Request $req, Response $resp, array $args): Response
    {
        $check = $this->checkAdminAccess($req, $resp);
        if ($check !== null) {
            return $check;
        }

        $body = $req->getParsedBody() ?? [];
        $query = $body['query'] ?? '';
        $db = Database::getInstance();

        $paquets = $query
            ? $db->searchPaquets($query)
            : $db->getAllPaquets();

        // Re-render admin window with results
        $user = SessionHandler::GetRawDataFromSession('user');
        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title'        => "Packet Delivery — Espace administratif",
            'year'         => date('Y'),
            'user'         => $user,
            'paquets'      => $paquets,
            'livreurs'     => $db->getAllLivreurs(),
            'csrf'         => SessionHandler::CsrfToken(),
        ];
        return $view->render($resp, 'adminWindow.php', $data);
    }

    function searchLivreurs(Request $req, Response $resp, array $args): Response
    {
        $check = $this->checkAdminAccess($req, $resp);
        if ($check !== null) {
            return $check;
        }

        $body = $req->getParsedBody() ?? [];
        $query = $body['query'] ?? '';
        $db = Database::getInstance();

        $livreurs = $query
            ? $db->searchLivreurs($query)
            : $db->getAllLivreurs();

        // Re-render admin window with results
        $user = SessionHandler::GetRawDataFromSession('user');
        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title'        => "Packet Delivery — Espace administratif",
            'year'         => date('Y'),
            'user'         => $user,
            'paquets'      => $db->getAllPaquets(),
            'livreurs'     => $livreurs,
            'csrf'         => SessionHandler::CsrfToken(),
        ];
        return $view->render($resp, 'adminWindow.php', $data);
    }

    // ========== GESTION DES PAQUETS (FENÊTRE C) ==========

    function paketAddForm(Request $req, Response $resp, array $args): Response
    {
        $check = $this->checkAdminAccess($req, $resp);
        if ($check !== null) {
            return $check;
        }

        $user = SessionHandler::GetRawDataFromSession('user');
        $db = Database::getInstance();

        // Calculer les dates valides (lendem du lendemain au +3 jours, lundi-vendredi)
        $datesValides = $this->getDatesValides();

        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title'           => "Packet Delivery — Ajouter un paquet",
            'year'            => date('Y'),
            'user'            => $user,
            'mode'            => 'add',
            'csrf'            => SessionHandler::CsrfToken(),
            'dates_valides'   => $datesValides,
            'livreurs'        => $db->getAllLivreurs(),
            'next_numero'     => $db->getNextNumeroPostal(),
        ];
        return $view->render($resp, 'paquetForm.php', $data);
    }

    function paketAdd(Request $req, Response $resp, array $args): Response
    {
        $check = $this->checkAdminAccess($req, $resp);
        if ($check !== null) {
            return $check;
        }

        $user = SessionHandler::GetRawDataFromSession('user');
        $body = $req->getParsedBody() ?? [];

        if (!SessionHandler::VerifyCsrf($body['_csrf'] ?? null)) {
            return $resp->withHeader('Location', '/admin')->withStatus(302);
        }

        $db = Database::getInstance();

        // Validation des champs
        $errors = $this->validatePaquetData($body);
        if (!empty($errors)) {
            $view = new PhpRenderer("../view");
            $view->setLayout("layout.php");
            $data = [
                'title'         => "Packet Delivery — Ajouter un paquet",
                'year'          => date('Y'),
                'user'          => $user,
                'mode'          => 'add',
                'csrf'          => SessionHandler::CsrfToken(),
                'errors'        => $errors,
                'paquet'        => $body,
                'dates_valides' => $this->getDatesValides(),
                'livreurs'      => $db->getAllLivreurs(),
            ];
            return $view->render($resp, 'paquetForm.php', $data);
        }

        // Créer le paquet
        $result = $db->createPaquet([
            'numeroPostal'          => $body['numero_postal'],
            'nomDestinataire'       => $body['nom_destinataire'],
            'prenomDestinataire'    => $body['prenom_destinataire'],
            'adresseDestinataire'   => $body['adresse_destinataire'],
            'latitudeAdresse'      => $body['latitude'],
            'longitudeAdresse'     => $body['longitude'],
            'dateLivraison'        => $body['date_livraison'],
            'id_employe_createur'   => $user->id,
            'id_employe_livreur'    => !empty($body['id_employe_livreur']) ? $body['id_employe_livreur'] : null,
        ]);

        if ($result) {
            return $resp->withHeader('Location', '/admin')->withStatus(302);
        }

        // Erreur lors de la création
        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title'         => "Packet Delivery — Ajouter un paquet",
            'year'          => date('Y'),
            'user'          => $user,
            'mode'          => 'add',
            'csrf'          => SessionHandler::CsrfToken(),
            'errors'        => ['Erreur lors de la création du paquet.'],
            'paquet'        => $body,
            'dates_valides' => $this->getDatesValides(),
            'livreurs'      => $db->getAllLivreurs(),
        ];
        return $view->render($resp, 'paquetForm.php', $data);
    }

    function paketEditForm(Request $req, Response $resp, array $args): Response
    {
        $check = $this->checkAdminAccess($req, $resp);
        if ($check !== null) {
            return $check;
        }

        $user = SessionHandler::GetRawDataFromSession('user');
        $db = Database::getInstance();
        $paquetId = $args['id'] ?? '';

        $paquet = $db->getPaquetById($paquetId);
        if (!$paquet) {
            return $resp->withHeader('Location', '/admin')->withStatus(302);
        }

        // Vérifier si le paquet peut être modifié
        $canModify = $db->canModifyPaquet($paquetId);
        $canDelete = $db->canDeletePaquet($paquetId);

        // Dates valides pour la livraison
        $datesValides = $this->getDatesValides();

        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title'           => "Packet Delivery — Modifier un paquet",
            'year'            => date('Y'),
            'user'            => $user,
            'mode'            => 'edit',
            'csrf'            => SessionHandler::CsrfToken(),
            'paquet'          => $paquet,
            'can_modify'      => $canModify,
            'can_delete'      => $canDelete,
            'dates_valides'   => $datesValides,
            'livreurs'        => $db->getAllLivreurs(),
            'is_view_only'    => !$canModify,
        ];
        return $view->render($resp, 'paquetForm.php', $data);
    }

    function paketEdit(Request $req, Response $resp, array $args): Response
    {
        $check = $this->checkAdminAccess($req, $resp);
        if ($check !== null) {
            return $check;
        }

        $user = SessionHandler::GetRawDataFromSession('user');
        $body = $req->getParsedBody() ?? [];
        $paquetId = $args['id'] ?? '';

        if (!SessionHandler::VerifyCsrf($body['_csrf'] ?? null)) {
            return $resp->withHeader('Location', '/admin')->withStatus(302);
        }

        $db = Database::getInstance();
        $paquet = $db->getPaquetById($paquetId);
        if (!$paquet) {
            return $resp->withHeader('Location', '/admin')->withStatus(302);
        }

        // Vérifier si le paquet peut être modifié
        if (!$db->canModifyPaquet($paquetId)) {
            return $resp->withHeader('Location', '/admin/paquet/edit/' . $paquetId)->withStatus(302);
        }

        // Validation des champs
        $errors = $this->validatePaquetData($body);
        if (!empty($errors)) {
            $view = new PhpRenderer("../view");
            $view->setLayout("layout.php");
            $data = [
                'title'         => "Packet Delivery — Modifier un paquet",
                'year'          => date('Y'),
                'user'          => $user,
                'mode'          => 'edit',
                'csrf'          => SessionHandler::CsrfToken(),
                'errors'        => $errors,
                'paquet'        => array_merge($paquet, $body),
                'can_modify'    => true,
                'can_delete'    => $db->canDeletePaquet($paquetId),
                'dates_valides' => $this->getDatesValides(),
                'livreurs'      => $db->getAllLivreurs(),
            ];
            return $view->render($resp, 'paquetForm.php', $data);
        }

        // Modifier le paquet
        $result = $db->updatePaquet($paquetId, [
            'numeroPostal'          => $body['numero_postal'],
            'nomDestinataire'       => $body['nom_destinataire'],
            'prenomDestinataire'    => $body['prenom_destinataire'],
            'adresseDestinataire'   => $body['adresse_destinataire'],
            'latitudeAdresse'      => $body['latitude'],
            'longitudeAdresse'     => $body['longitude'],
            'dateLivraison'        => $body['date_livraison'],
            'id_employe_livreur'    => !empty($body['id_employe_livreur']) ? $body['id_employe_livreur'] : null,
        ]);

        if ($result) {
            return $resp->withHeader('Location', '/admin')->withStatus(302);
        }

        // Erreur lors de la modification
        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title'         => "Packet Delivery — Modifier un paquet",
            'year'          => date('Y'),
            'user'          => $user,
            'mode'          => 'edit',
            'csrf'          => SessionHandler::CsrfToken(),
            'errors'        => ['Erreur lors de la modification du paquet.'],
            'paquet'        => array_merge($paquet, $body),
            'can_modify'    => true,
            'can_delete'    => $db->canDeletePaquet($paquetId),
            'dates_valides' => $this->getDatesValides(),
            'livreurs'      => $db->getAllLivreurs(),
        ];
        return $view->render($resp, 'paquetForm.php', $data);
    }

    function paketDelete(Request $req, Response $resp, array $args): Response
    {
        $check = $this->checkAdminAccess($req, $resp);
        if ($check !== null) {
            return $check;
        }

        $body = $req->getParsedBody() ?? [];
        $paquetId = $args['id'] ?? '';

        if (!SessionHandler::VerifyCsrf($body['_csrf'] ?? null)) {
            return $resp->withHeader('Location', '/admin')->withStatus(302);
        }

        $db = Database::getInstance();

        // Vérifier si le paquet peut être supprimé
        if (!$db->canDeletePaquet($paquetId)) {
            // Rediriger avec un message d'erreur
            return $resp->withHeader('Location', '/admin')->withStatus(302);
        }

        $db->deletePaquet($paquetId);
        return $resp->withHeader('Location', '/admin')->withStatus(302);
    }

    function paketLivre(Request $req, Response $resp, array $args): Response
    {
        $check = $this->checkLivreurAccess($req, $resp);
        if ($check !== null) {
            return $check;
        }

        $body = $req->getParsedBody() ?? [];
        $paquetId = $args['id'] ?? '';

        if (!SessionHandler::VerifyCsrf($body['_csrf'] ?? null)) {
            return $resp->withHeader('Location', '/delivery')->withStatus(302);
        }

        $user = SessionHandler::GetRawDataFromSession('user');
        $db = Database::getInstance();

        // Vérifier si le paquet existe et n'est pas déjà livré
        $paquet = $db->getPaquetById($paquetId);
        if (!$paquet || $paquet['statutLivraison'] === 'Livré') {
            return $resp->withHeader('Location', '/delivery')->withStatus(302);
        }

        // Marquer comme livré
        $result = $db->marquerLivraison($paquetId, $user->id);

        if ($result) {
            return $resp->withHeader('Location', '/delivery?date=' . $paquet['date_livraison'])->withStatus(302);
        }

        return $resp->withHeader('Location', '/delivery')->withStatus(302);
    }

    // ========== VISUALISATION DES PAQUETS D'UN LIVREUR (FENÊTRE D) ==========

    function paketLivreurPaquets(Request $req, Response $resp, array $args): Response
    {
        // Seul l'admin ou le livreur concerné peut accéder à cette page
        SessionHandler::initSession();
        if (!SessionHandler::IsLoggedIn()) {
            return $resp->withHeader('Location', '/')->withStatus(302);
        }
        $user = SessionHandler::GetRawDataFromSession('user');
        $livreurId = (int)($args['id'] ?? 0);

        // Admin peut voir toutes les pages livreurs, mais livreur doit être celui dont on affiche les paquets
        if ($user->estLivreur && $user->id != $livreurId) {
            return $resp->withHeader('Location', '/delivery')->withStatus(302);
        }

        $db = Database::getInstance();
        $livreur = $db->getEmployeById($livreurId);

        if (!$livreur || !$livreur['estLivreur']) {
            return $resp->withHeader('Location', '/admin')->withStatus(302);
        }

        // Si c'est un livreur, rediriger vers sa propre page
        if ($user->estLivreur && $user->id == $livreurId) {
            return $resp->withHeader('Location', '/delivery')->withStatus(302);
        }

        $date = $req->getQueryParams()['date'] ?? date('Y-m-d');

        $paquets = $db->getPaquetsByLivreur($livreurId, $date);

        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title'           => "Packet Delivery — Paquets de " . htmlspecialchars($livreur['prenom'] . ' ' . $livreur['nom']),
            'year'            => date('Y'),
            'user'            => $user,
            'livreur'         => $livreur,
            'date'            => $date,
            'paquets'         => $paquets,
            'csrf'            => SessionHandler::CsrfToken(),
        ];
        return $view->render($resp, 'livreurPaquets.php', $data);
    }

    // ========== FONCTIONS UTILITAIRES ==========

    private function getDatesValides(): array
    {
        $dates = [];
        $aujourdhui = new \DateTime();
        $aujourdhui->modify('+1 day'); // Commencer demain

        for ($i = 0; $i < 3; $i++) {
            $date = clone $aujourdhui;
            $date->modify("+$i day");
            $jourSemaine = (int)$date->format('N');

            // Lund (1) à Vendr (5)
            if ($jourSemaine >= 1 && $jourSemaine <= 5) {
                $dates[] = $date->format('Y-m-d');
            }
        }

        return $dates;
    }

    private function validatePaquetData(array $data): array
    {
        $errors = [];

        if (empty(trim($data['numero_postal'] ?? ''))) {
            $errors[] = "Le numéro postal est requis.";
        }
        if (empty(trim($data['nom_destinataire'] ?? ''))) {
            $errors[] = "Le nom du destinataire est requis.";
        }
        if (empty(trim($data['prenom_destinataire'] ?? ''))) {
            $errors[] = "Le prénom du destinataire est requis.";
        }
        if (empty(trim($data['adresse_destinataire'] ?? ''))) {
            $errors[] = "L'adresse du destinataire est requise.";
        }
        if (empty($data['latitude'] ?? '') || empty($data['longitude'] ?? '')) {
            $errors[] = "La latitude et la longitude doivent être calculées.";
        }
        if (empty($data['date_livraison'] ?? '')) {
            $errors[] = "La date de livraison est requise.";
        }

        return $errors;
    }

    function deliverWindow(Request $req, Response $resp, array $args): Response
    {
        $check = $this->checkLivreurAccess($req, $resp);
        if ($check !== null) return $check;

        $user = SessionHandler::GetRawDataFromSession('user');
        $db   = Database::getInstance();

        $date = $req->getQueryParams()['date'] ?? date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }

        $paquets          = $db->getPaquetsByLivreur($user->id, $date);
        $isToday          = ($date === date('Y-m-d'));
        $routeComplete    = !empty($paquets) && count(array_filter($paquets, fn($p) => $p['ordreRouteLivraison'] === null)) === 0;
        $deliveriesStarted = count(array_filter($paquets, fn($p) => in_array($p['statutLivraison'], ['Livré', 'En cours de livraison']))) > 0;
        $allDelivered     = !empty($paquets) && count(array_filter($paquets, fn($p) => $p['statutLivraison'] !== 'Livré')) === 0;

        $view = new PhpRenderer("../view");
        $view->setLayout("layout.php");
        $data = [
            'title'            => "Packet Delivery — Espace livreur",
            'year'             => date('Y'),
            'user'             => $user,
            'date'             => $date,
            'dateAffichee'     => $this->formatDateFr($date),
            'isToday'          => $isToday,
            'routeComplete'    => $routeComplete,
            'deliveriesStarted' => $deliveriesStarted,
            'allDelivered'     => $allDelivered,
            'paquets'          => $paquets,
            'csrf'             => SessionHandler::CsrfToken(),
        ];
        return $view->render($resp, 'deliverWindow.php', $data);
    }

    function paketUpdateOrdre(Request $req, Response $resp, array $args): Response
    {
        $check = $this->checkLivreurAccess($req, $resp);
        if ($check !== null) return $check;

        $body     = $req->getParsedBody() ?? [];
        $paquetId = $args['id'] ?? '';
        $date     = $body['date'] ?? date('Y-m-d');

        $redirect = fn() => $resp->withHeader('Location', '/delivery?date=' . urlencode($date))->withStatus(302);

        if (!SessionHandler::VerifyCsrf($body['_csrf'] ?? null)) return $redirect();
        if ($date !== date('Y-m-d')) return $redirect();

        $user   = SessionHandler::GetRawDataFromSession('user');
        $db     = Database::getInstance();
        $paquets = $db->getPaquetsByLivreur($user->id, $date);

        foreach ($paquets as $p) {
            if (in_array($p['statutLivraison'], ['Livré', 'En cours de livraison'])) return $redirect();
        }

        // Vérifier que le colis appartient bien à ce livreur pour cette date (IDOR guard)
        if (!in_array($paquetId, array_column($paquets, 'numeroPostal'), true)) return $redirect();

        $ordre = (int)($body['ordre'] ?? 0);
        if ($ordre < 1 || $ordre > count($paquets)) return $redirect();

        $db->updatePackageOrder($paquetId, $ordre, $user->id, $date);
        return $redirect();
    }

    private function formatDateFr(string $date): string
    {
        $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $mois  = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
                  'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
        $ts    = strtotime($date);
        return $jours[(int)date('w', $ts)]
             . ' ' . date('d', $ts)
             . ' ' . $mois[(int)date('n', $ts)]
             . ' ' . date('Y', $ts);
    }

    function logout(Request $req, Response $resp, array $args): Response
    {
        SessionHandler::initSession();
        SessionHandler::DestroySession();
        return $resp->withHeader('Location', '/')->withStatus(302);
    }
}