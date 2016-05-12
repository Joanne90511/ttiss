<?php
namespace App\Action;

use Slim\Views\Twig;
use App\REDCap;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Client;

final class IndexGet
{
    private $view;
    private $logger;
    private $api;
    private $data = [
        'token'        => '69C13A4A17BBE19BC1107662FA31239E',
        'format'       => 'json',
        'returnFormat' => 'json'
    ];

    private $data2 = [
        'content'      => 'project',
    ];

    public function __construct(Twig $view, LoggerInterface $logger, REDCap\API $api)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->api = $api;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $options = $this->api->getAvailableIds();

        $this->view->render($response, 'index.twig',[
            'options' => $options
        ]);

        return $response;
    }
}
