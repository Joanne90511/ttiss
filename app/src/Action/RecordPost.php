<?php

namespace App\Action;

use App\REDCap\Record as Record;
use App\REDCap\API as API;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class RecordPost {

    private $logger;
    private $api;

    public function __construct(LoggerInterface $logger, API $api)
    {
        $this->logger = $logger;
        $this->api    = $api;

    }

    public function __invoke(Request $request, Response $response, $args)
    {

        $data = $request->getBody();

        $data   = json_decode($data);
        $record = new Record($data);

        $result = $this->api->saveRecord($record);

        $return = [];

        $status = 500;
        if ($result !== false)
        {
            $status           = 200;
            $return['status'] = 'success';
            $return['id']     = $result;
        } else
        {
            $return['status'] = 'error';
        }
        $response->getBody()->write(json_encode($return));
        $response = $response->withStatus($status);

        return $response;
    }
}