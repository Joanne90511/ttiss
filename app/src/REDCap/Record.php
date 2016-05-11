<?php
namespace App\REDCap;

class Record {

    /** @var array $data Holds all the data */
    private $data = array();

    /** @var string $record_id_field The identifying field */
    private $record_id_field = 'case_id';

    public function __construct($data = NULL)
    {
        if ($data !== NULL)
        {
            //Will be getting an assoc array
            foreach ($data as $field => $val)
            {
                $this->$field = $val;
            }
        }
    }

    /**
     * Magic Method to return data
     *
     * @param $name
     *
     * @return bool|string
     */
    public function __get($name)
    {

        //Returns the data if it exists
        if (isset($this->data[$name]))
        {
            return $this->data[$name];
        }
        //Returns the empty string if this is a new record
        if (empty($this->data[$this->record_id_field]))
        {
            return "";
        }

        //Otherwise false
        return false;
    }

    /**
     * Magic method to set values of $data
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {

        $this->data[$name] = $value;
    }

    public function get($name)
    {
        //Returns the data if it exists
        if (isset($this->data[$name]))
        {
            return $this->data[$name];
        }
        //Returns the empty string if this is a new record
        if (empty($this->data[$this->record_id_field]))
        {
            return "";
        }

        //Otherwise false
        return false;
    }

    /**
     * Encodes the data into a JSON string
     *
     * @param bool $arrayed whether the output needs to be wrapped in an array
     *                      before encoding
     *
     * @return string
     */
    public function toJSON($arrayed = false)
    {
        if ($arrayed)
        {
            return json_encode([$this->data]);
        }

        return json_encode($this->data);
    }

    /**
     * Return the id field values
     *
     * @return bool|string
     */
    public function getId()
    {
        $id = $this->record_id_field;

        return $this->$id;
    }


}
