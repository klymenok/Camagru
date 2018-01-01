<?php


namespace controller;


class User extends Controller
{
    private $_user;

    public function __construct() {
        $this->_user = new \model\User();
        parent::__construct();
    }

    public function execute() {
        if ($this->isUserLoggedIn()) {
            $this->view->render('user_page');
        } else {
            header('Location: gallery');
        }
    }

    public function login() {
        if (array_key_exists('action', $_GET) && $_GET['action'] == 'ajax') {
            echo $this->_user->login();
        }
    }

    public function create() {
        echo $this->_user->createUser();
    }

    public function logout() {
        $this->_user->logout();
        header("Location: ../");
    }

    public function resetPassword() {
        echo $this->_user->resetPassword();
    }

    public function getUser() {
        if (array_key_exists('action', $_GET) && $_GET['action'] == 'ajax') {
            echo $this->_user->getUser();
        }
    }

    public function isLogged() {
        if ($this->isUserLoggedIn()) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function getEmail() {
        if (array_key_exists('action', $_GET) && $_GET['action'] == 'ajax') {
            echo $this->_user->getEmail();
        }
    }

    public function changeInformation() {
        if (array_key_exists('action', $_GET) && $_GET['action'] == 'ajax' && array_key_exists('type', $_GET)) {
            echo $this->_user->changeInformation($_GET['type']);
        }
    }

    public function activate() {
        if (array_key_exists('id', $_GET)) {
            $this->_user->activate($_GET['id']);
        }
        header('Location: /camagru/login');
    }
}