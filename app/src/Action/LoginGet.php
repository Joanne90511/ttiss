<?php

namespace App\Action;

use Slim\Views\Twig;
use Slim\Flash\Messages;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LoginGet {

    private $view;
    private $flash;

    public function __construct(Twig $view, Messages $flash)
    {
        $this->view  = $view;
        $this->flash = $flash;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        //$this->logger->info("Home page action dispatched");

        $this->view->render($response, 'login.twig', [
            'errors' => $this->flash->getMessage('errors')
        ]);

        return $response;
    }
}