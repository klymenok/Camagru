<?php
/**
 * Created by PhpStorm.
 * User: oklymeno
 * Date: 11/16/17
 * Time: 5:04 PM
 */

namespace controller;


class Camera extends Controller implements ControllerInterface
{

    private $_photo;

    public function __construct()
    {
        parent::__construct();
        $this->_photo = new \model\Camera();
    }

    public function execute()
    {
        if ($this->isUserLoggedIn()) {
            $this->view->render('main_page');
        } else {
            header('Location: gallery');
        }
    }

    public function getStickers() {
        if (array_key_exists('action', $_GET) && $_GET['action'] == 'ajax') {
            echo $this->_photo->getStickers();
        }
    }

    public function upload() {
        if (array_key_exists('action', $_GET) && $_GET['action'] == 'ajax' && array_key_exists('photo_upload', $_FILES)) {
            echo $this->_photo->upload();
        }
    }

    public function uploadSticker() {
        if (array_key_exists('action', $_GET) && $_GET['action'] == 'ajax' && array_key_exists('sticker_upload', $_FILES)) {
            echo $this->_photo->uploadSticker();
        } else {
            echo 'erros';
        }
    }

    public function createTmp() {
        if (array_key_exists('action', $_GET) && $_GET['action'] === 'ajax' && array_key_exists('img', $_POST)) {
            echo $this->_photo->createTempImage();
        }
    }

    public function clearTmp() {
        if (array_key_exists('action', $_GET) && $_GET['action'] === 'ajax' && array_key_exists('remove', $_POST) && $_POST['remove'] == 'true') {
            $this->_photo->clearTemporaryFolder();
        }
    }

    public function makePost() {
        if (array_key_exists('action', $_GET) && $_GET['action'] === 'ajax' && array_key_exists('photo_name', $_POST)) {
            echo $this->_photo->addPhotoToCollection($_POST['photo_name']);
        }
    }



}