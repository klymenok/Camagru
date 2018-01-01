<?php

namespace controller;


class Login extends Controller implements ControllerInterface {

    private $_user;

    public function __construct()
    {
        parent::__construct();
        $this->_user = new \model\User();
    }

    public function execute()
    {
        if ($this->isUserLoggedIn()) {
            header('Location: gallery');
        } else {
            $this->view->render('login');
        }
    }
}