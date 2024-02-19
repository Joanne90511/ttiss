<?php

namespace App\REDCap;

use GuzzleHttp\Client;

class API {

    /** @var array $default Default Values for the API Call */
    private $default = ['returnFormat' => 'json', 'format' => 'json',];

    private $client;

    public function __construct($url, $api_token = NULL)
    {
        $this->default['token'] = $api_token;
        $this->client           = new Client([
                                                 'base_uri' => $url,
                                             ]);
    }

    /**
     * Uses the API to return information about the project
     *
     * @return mixed
     */
    public function getProjectInfo()
    {
        $data = $this->default;

        $data['content'] = 'project';

        return $this->call($data);
    }

    /**
     * Executes the call to the API at defined by the $url
     *
     * @param      $data
     * @param bool $decode
     *
     * @return mixed
     */
    private function call($data)
    {

        //Make Request. URI is already set
        $output = $this->client->post('', ['form_params' => $data])
                               ->getBody()
                               ->getContents();

        //Always dealing with JSON
        return json_decode($output);
    }

    /**
     * Gets all available IDs using the API. This follows the users data access
     * group
     *
     * @return mixed
     */
    public function getAvailableIds()
    {
        $data            = $this->default;
        $data['content'] = 'record';
	$data['format']  = 'json';
        $data['fields']  = array("case_id", 'ctaerfcanadian_transfusion_reaction_adverse_event_complete');

        return $this->call($data);
    }

    /**
     * Not sure if this is needed yet
     *
     * @return string
     */
    public function getAPIToken()
    {
        return $this->api_token;
    }

    /**
     * Sets the API key used by the call() function
     *
     * @param $api_token
     *
     * @return $this
     */
    public function setAPIToken($api_token)
    {
        $this->default['token'] = $api_token;

        return $this;
    }

    /**
     * Returns a Record based on the ID provided
     *
     * @param $ids
     *
     * @return REDCapRecord
     */
    public function getRecord($ids)
    {
        $data            = $this->default;
        $data['content'] = 'record';
        $data['records'] = array($ids);
        $data['type']    = 'flat';

        $ret = $this->call($data);

        return new Record($ret[0]);
    }

    /**
     * Returns an empty REDCapRecord
     * Not sure this is needed for not
     *
     * @return REDCapRecord
     */
    public function newRecord()
    {
        $fields = $this->getMetadata();
        //TODO Move variable out of API
        $data   = array("ctaerfcanadian_transfusion_reaction_adverse_event_complete" => 1);
        foreach ($fields as $field)
        {
            if ($field->field_type == "checkbox")
            {
                $temp = explode("|", $field->select_choices_or_calculations);
                foreach ($temp as $op)
                {
                    $temp2 = explode(",", $op);

                    $data[$field->field_name . "___" . trim($temp2[0])] = "";
                }
            } else
            {
                $data[$field->field_name] = "";
            }
        }

        return new Record($data);
    }

    public function getMetadata()
    {
        $data            = $this->default;
        $data['content'] = 'metadata';

        return $this->call($data);

    }

    public function saveRecord(Record $record)
    {
        $data                      = $this->default;
        $data['content']           = 'record';
        $data['overwriteBehavior'] = 'overwrite';
        if ($record->case_id == "")
        {
            $record->case_id = $this->getNewRecordId();
        }
        $data['data'] = $record->toJSON(true);

        $ret = $this->call($data);

        if ($ret->count == 1)
        {
            return $record->case_id;
        } else
        {
            return false;
        }
    }

    private function getNewRecordId()
    {
        $data            = $this->default;
        $data['content'] = 'record';
        $data['fields']  = ['case_id'];
        $data['type']    = 'flat';

        $ret = $this->call($data);

        $record = end($ret);

        $id = $record->case_id;

        if (strpos($id, "-") === false)
        {
            return ((int)$id) + 1;
        } else
        {
            $id_parts = explode("-", $id);

            return $id_parts[0] . "-" . (((int)$id_parts[1]) + 1);
        }
    }
}
