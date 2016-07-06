<?php

namespace App\Action;

use Slim\Exception\SlimException;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LoginPost {

    private $view;
    private $logger;
    private $db;
    private $project_id;

    public function __construct(Twig $view, LoggerInterface $logger, \mysqli $db, Messages $flash, $project_id)
    {
        $this->view       = $view;
        $this->logger     = $logger;
        $this->db         = $db;
        $this->flash      = $flash;
        $this->project_id = $project_id;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        if ($this->db->connect_error)
        {
            $this->logger->critical("Failed database connection");

            throw new \Exception('Unable to connect to the database');
        }

        $input = $request->getParsedBody();

        $username = $input['username'] ? trim($input['username']) : '';
        $password = $input['username'] ? trim($input['password']) : '';

        $query = $this->db->prepare("SELECT a.`password`,a.`password_salt`, b.`user_firstname` FROM `redcap_auth` a JOIN `redcap_user_information` b ON a.`username`=b.`username` WHERE a.`username`=?");

        $query->bind_param('s', $username);

        $query->execute();

        $query->bind_result($stored_password, $stored_salt, $stored_firstname);

        if (!$query->fetch())
        {
            //Deal with login issue
        }

        $query->free_result();

        if ($stored_password === hash('sha512', $password . $stored_salt))
        {
            //Login Successful
            $_SESSION['username'] = $username;

            $query = $this->db->prepare('SELECT `api_token`, `app_title` FROM `redcap_user_rights` a JOIN `redcap_projects` b ON a.project_id=b.project_id  WHERE a.`username`=? AND a.`project_id`=?');

            $query->bind_param('si', $username, $this->project_id);

            $query->execute();

            $query->bind_result($api_token, $project_name);

            $query->fetch();

            $query->free_result();

            $_SESSION['api_token'] = $api_token;

            //TODO URL needs to be dynamic
            return $response->withStatus(302)->withHeader('Location', '/form');
        }

        $this->flash->addMessage('errors', 'Login not successful. Incorrect username and/or password');
        $this->logger->alert('Unsuccessful login for ' . $username);

        //TODO URL needs to be dynamic
        return $response->withStatus(302)->withHeader('Location', '/form/login');

    }
}