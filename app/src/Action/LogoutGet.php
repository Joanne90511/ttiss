<?php
/**
 * Created by PhpStorm.
 * User: mat
 * Date: 11/05/16
 * Time: 8:40 PM
 */

namespace App\Action;

use Slim\Flash\Messages;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LogoutGet {

    private $flash;

    public function __construct(Messages $flash)
    {
        $this->flash = $flash;
    }

    public function __invoke(Request $request, Response $response, $args)
    {

        session_destroy();

        //TODO Add Flash Message about success
        return $response->withStatus(302)->withHeader('Location','/');
    }
    
}