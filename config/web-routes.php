<?php
namespace Fauza\Template\Config;
use Fauza\Template\Controllers\MainController;
use Fauza\Template\Utils\SessionHandler;

SessionHandler::initSession();

$app->get('/', [ MainController::class, 'home' ]);
$app->post('/login', [ MainController::class, 'login' ]);
$app->get('/logout', [ MainController::class, 'logout' ]);

// Routes Admin
$app->get('/admin', [ MainController::class, 'adminWindow' ]);
$app->get('/admin/paquet/add', [ MainController::class, 'paketAddForm' ]);
$app->post('/admin/paquet/add', [ MainController::class, 'paketAdd' ]);
$app->get('/admin/paquet/edit/{id}', [ MainController::class, 'paketEditForm' ]);
$app->post('/admin/paquet/edit/{id}', [ MainController::class, 'paketEdit' ]);
$app->post('/admin/paquet/delete/{id}', [ MainController::class, 'paketDelete' ]);
$app->get('/admin/livreur/{id}/paquets', [ MainController::class, 'paketLivreurPaquets' ]);
$app->post('/admin/paquets/search', [ MainController::class, 'searchPaquets' ]);
$app->post('/admin/livreurs/search', [ MainController::class, 'searchLivreurs' ]);

// Routes Livreur
$app->get('/delivery', [ MainController::class, 'deliverWindow' ]);
$app->post('/delivery/paquet/livre/{id}', [ MainController::class, 'paketLivre' ]);
