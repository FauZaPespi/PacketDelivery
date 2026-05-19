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
$app->get('/admin/paquet/add', [ MainController::class, 'paquetAddForm' ]);
$app->post('/admin/paquet/add', [ MainController::class, 'paquetAdd' ]);
$app->get('/admin/paquet/edit/{id}', [ MainController::class, 'paquetEditForm' ]);
$app->post('/admin/paquet/edit/{id}', [ MainController::class, 'paquetEdit' ]);
$app->post('/admin/paquet/delete/{id}', [ MainController::class, 'paquetDelete' ]);
$app->get('/admin/livreur/{id}/paquets', [ MainController::class, 'livreurPaquets' ]);
$app->post('/admin/paquets/search', [ MainController::class, 'searchPaquets' ]);
$app->post('/admin/livreurs/search', [ MainController::class, 'searchLivreurs' ]);

// Routes Livreur
$app->get('/delivery', [ MainController::class, 'deliverWindow' ]);
