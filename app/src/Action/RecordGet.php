<?php
/**
 * Created by PhpStorm.
 * User: mat
 * Date: 11/05/16
 * Time: 8:55 PM
 */

namespace App\Action;

use Slim\Views\Twig;
use App\REDCap;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class RecordGet {

    private $view;
    private $logger;
    private $api;

    public function __construct(Twig $view, LoggerInterface $logger, REDCap\API $api)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->api = $api;
    }

    public function __invoke(Request $request, Response $response, $args)
    {

        if ($args['record'] === 'new') {
            $record = $this->api->newRecord();
        } else {
            //    TODO add validation
            $record = $this->api->getRecord($args['record']);
        }

        $response->getBody()->write($record->toJSON());

        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->withAddedHeader('Cache-Control', 'no-cache, must-revalidate')
            ->withAddedHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
    }
}
