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
use App\REDCap\Utilities as Utility;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class FieldsGet {

    private $view;
    private $logger;
    private $api;

    public function __construct(Twig $view, LoggerInterface $logger, REDCap\API $api)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->api = $api;
        $this->uts = new REDCap\Utilities();
    }

    public function __invoke(Request $request, Response $response, $args)
    {

        $meta = $this->api->getMetadata();

//        $fields = format_fields($meta);
        $fields = Utility::format_fields($meta);

        $response->getBody()->write(json_encode($fields));

        return $response->withStatus(200)->withHeader('Content-Type', 'application/json')
                   ->withAddedHeader('Cache-Control', 'no-cache, must-revalidate')
                   ->withAddedHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
    }
}
