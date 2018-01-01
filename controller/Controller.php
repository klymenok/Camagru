<?php

/**
 * Created by PhpStorm.
 * User: KlymenokAlexey
 * Date: 25.07.17
 * Time: 21:24
 */
namespace controller;

use model\Session;
use view\View;

class Controller
{
    public $view;

    protected function __construct()
    {
        Session::init();
        $this->view = new View();
    }

    protected function isUserLoggedIn() {
        if (array_key_exists('logged_in_user', $_SESSION) && $_SESSION['logged_in_user'] !== null) {
            return true;
        } else {
            return false;
        }
    }
}