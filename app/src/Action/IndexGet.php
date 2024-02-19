<?php
namespace App\Action;

use Slim\Views\Twig;
use App\REDCap;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Client;

final class IndexGet extends BaseAction {

    private $view;
    private $logger;
    private $api;

    public function __construct(Twig $view, LoggerInterface $logger, REDCap\API $api)
    {
        $this->view   = $view;
        $this->logger = $logger;
        $this->api    = $api;

        parent::__construct();
    }

    public function __invoke(Request $request, Response $response, $args)
    {

        $options = $this->api->getAvailableIds();

        usort($options, function ($a, $b)
        {
	    $aparts = explode("-", $a->case_id);
	    $bparts = explode("-", $b->case_id);
	    
            if($aparts[0] == $bparts[0]) {
	       return $aparts[1] < $bparts[1];
	    }
	    else {
               return $a->case_id < $b->case_id; 
            }
        });

        $this->data['options'] = $options;

        $this->view->render($response, 'index.twig', $this->data);

        return $response;
    }
}
