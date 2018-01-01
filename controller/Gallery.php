<?php

namespace controller;


class Gallery extends Controller implements ControllerInterface
{
    private $_model;

    public function __construct()
    {
        parent::__construct();
        $this->_model = new \model\Gallery();
    }

    public function execute() {
        if (array_key_exists('photo', $_GET)) {
            if ($this->_model->isPhotoExist($_GET['photo'])) {
                $this->view->render('photo');
            } else {
                $this->view->render('photos_collection');
            }
        } else {
            $this->view->render('photos_collection');
        }
    }

    public function getData() {
        if (array_key_exists('action', $_GET) && $_GET['action'] = 'ajax') {
            if (array_key_exists('type', $_GET) && $_GET['type'] = 'single') {
                if (array_key_exists('photo', $_POST)) {
                    echo $this->_model->getSingleData($_POST['photo']);
                }
            } else {
                echo $this->_model->getData();
            }
        }
    }

    public function like() {
        if (array_key_exists('action', $_GET) && $_GET['action'] = 'ajax') {
            $filename = explode('/', $_POST['photo']);
            $this->_model->setLike($filename[count($filename) - 1]);
        }
    }

    public function getComments() {
        if (array_key_exists('action', $_GET) && $_GET['action'] = 'ajax') {
            echo $this->_model->getComments($_POST['photo']);
        }
    }

    public function addComment() {
        if (array_key_exists('action', $_GET) && $_GET['action'] = 'ajax') {
            $filename = explode('/', $_POST['photo']);
            echo $this->_model->addComment($filename[count($filename) - 1]);
        }
    }

    public function delete() {
        if (array_key_exists('action', $_GET) && $_GET['action'] = 'ajax') {
            $filename = explode('/', $_POST['photo']);
            echo $this->_model->deletePhoto($filename[count($filename) - 1]);
        }
    }



}