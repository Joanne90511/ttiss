<?php
/**
 * Created by PhpStorm.
 * User: mat
 * Date: 16/05/16
 * Time: 12:25 PM
 */

namespace App\Action;


class BaseAction {

    protected $data = [];

    public function __construct() {

        $this->data['auth'] = !empty($_SESSION['username']);

    }
    
}