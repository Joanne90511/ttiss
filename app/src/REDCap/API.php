<?php
namespace App\REDCap;

class API {

    /** @var  string $api_token Key used for API calls */
    private $api_token;

    /** @var string $url URL used for API calls */
    private $url = "http://redcap.local/api/";

    /** @var array $default Default Values for the API Call */
    private $default = array('returnFormat' => 'json', 'format' => 'json',);

    public function __construct($url, $api_token = NULL)
    {
        $this->url       = $url;
        $this->api_token = $api_token;
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
    private function call($data, $decode = true)
    {

        $data['token'] = $this->api_token;
        $ch            = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Set to TRUE for production use
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        $output = curl_exec($ch);
        if ($decode)
        {
            return $this->decode($output);
        } else
        {
            return $output;
        }
    }

    /**
     * Internally breaks up return string into another format
     * Currently only JSON
     *
     * @param $data
     *
     * @return mixed
     */
    private function decode($data)
    {
        //Do all data cleaning
        return json_decode($data);
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
        $data['fields']  = array("case_id", "phac_number");

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
        $this->api_token = $api_token;

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
        $data   = array();
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

    public function saveRecord(REDCapRecord $record)
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
