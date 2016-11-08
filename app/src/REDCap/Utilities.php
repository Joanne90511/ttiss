<?php
/**
 * Created by PhpStorm.
 * User: mat
 * Date: 11/05/16
 * Time: 9:49 PM
 */

namespace App\REDCap;


class Utilities {

    static function format_fields($meta, $fields = [])
    {
        foreach ($meta as $field)
        {
            if (in_array($field->field_type, ["radio", "dropdown"]))
            {
                $temp    = explode("|", $field->select_choices_or_calculations);
                $options = [];
                foreach ($temp as $op)
                {
                    $temp2                    = explode(",", $op);
                    $options[trim($temp2[0])] = trim($temp2[1]);
                }
                $fields[$field->field_name] = $options;
            } else if ($field->field_type == "checkbox")
            {
                $temp = explode("|", $field->select_choices_or_calculations);
                foreach ($temp as $op)
                {
                    var_dump($op);
                    $temp2 = explode(",", $op);
//                    var_dump(explode(",", $op, 1));

                    $fields[$field->field_name . "___" . trim($temp2[0])] = [trim($temp2[0]) => trim($temp2[1])];
                    var_dump($temp2);
                }
            }
        }

        return $fields;
    }


    static function renderTemplateToString($template, $data)
    {

        //    Taken from Slim\Views\PhpRenderer
        $render = function ($template, $data, $func = NULL)
        {
            extract($data);
            include $template;
        };

        ob_start();
        $render($template, ['record' => $data['record'], "fields" => $data['fields']]);

        return ob_get_clean();
    }

}