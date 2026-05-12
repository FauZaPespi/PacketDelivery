<?php
namespace Fauza\Template\Config;
use Fauza\Template\Controllers\MainController;
use Fauza\Template\Utils\SessionHandler;

SessionHandler::initSession();

$app->get('/', [ MainController::class, 'home' ]);
$app->post('/login', [ MainController::class, 'login' ]);
$app->get('/logout', [ MainController::class, 'logout' ]);
$app->get('/admin', [ MainController::class, 'adminWindow' ]);
$app->get('/delivery', [ MainController::class, 'deliverWindow' ]);
