<?php

namespace App\Action;

use Slim\Views\Twig;
use Slim\Flash\Messages;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LoginGet extends BaseAction {

    private $view;
    private $flash;

    public function __construct(Twig $view, Messages $flash)
    {
        $this->view  = $view;
        $this->flash = $flash;

        parent::__construct();
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $this->data['errors'] = $this->flash->getMessage('errors');

        $this->view->render($response, 'login.twig', $this->data);

        return $response;
    }
}