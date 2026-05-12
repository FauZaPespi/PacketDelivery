<?php
namespace Fauza\Template\Config;
use Fauza\Template\Controllers\MainController;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$app->get('/', [ MainController::class, 'home' ]);
$app->post('/login', [ MainController::class, 'login' ]);
$app->get('/admin', [ MainController::class, 'adminWindow' ]);
$app->get('/delivery', [ MainController::class, 'deliverWindow' ]);
$app->get('/api', [ MainController::class, 'api' ]);
