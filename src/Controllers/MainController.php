<?php
namespace Fauza\Template\Controllers;
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
            'title' => "Homepage",
        ];
        return $view->render($resp, 'home.php', $data);
    }
    function api(Request $req, Response $resp, array $args): Response{
        $resp->getBody()->write("Slim API is working !!");
        return $resp->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
    }
    
}
