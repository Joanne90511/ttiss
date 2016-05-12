<?php

namespace App\Action;

use App\REDCap\Utilities as Utilities;
use App\REDCap\API as API;
use Psr\Log\LoggerInterface;
use Slim\Http\Request as Request;
use Psr\Http\Message\ResponseInterface as Response;

class OutputGet {

    private $logger;
    private $api;
    private $pdf;

    public function __construct(LoggerInterface $logger, API $api, \mPDF $pdf)
    {
        $this->logger = $logger;
        $this->api    = $api;
        $this->pdf    = $pdf;

    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $case_id = $args['case_id'];

        $record                       = $this->api->getRecord($case_id);
        $record->first_name           = $request->getQueryParam('first_name', 'First Name:');
        $record->last_name            = $request->getQueryParam('last_name', 'Last Name:');
        $record->health_card_number   = $request->getQueryParam('health_card_number', 'Health Card Num:');
        $record->hospital_card_number = $request->getQueryParam('hospital_card_number', 'Hospital Card Num:');
        $request->getQueryParam('first_name', 'Sucker');
        $fields = Utilities::format_fields($this->api->getMetadata());


        $contents = Utilities::renderTemplateToString('../app/templates/pdf/body.phtml', ['record' => $record, "fields" => $fields]);
        if (isset($args['test']))
        {

            $response->getBody()->write($contents);
            $response->getBody()->write("<a href='/output/{$case_id}'><h1>PDF</h1></a>");

            return $response;
        } else
        {
            $header = Utilities::renderTemplateToString('../app/templates/pdf/header.phtml', ['record' => $record, "fields" => $fields]);
            $this->pdf->SetHTMLHeader($header);
            /** @var \Slim\Http\Response $response */
            //    $response = $this->renderer->render($response, 'pdf.phtml',array());
            $this->pdf->WriteHTML($contents);
            $this->pdf->Output('record.pdf', 'D');
        }

    }
}