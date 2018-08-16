<?php
/**
 * Created by PhpStorm.
 * User: oklymeno
 * Date: 11/18/17
 * Time: 9:23 PM
 */

namespace model;


class Camera extends Connection
{
    private $_allpwedTypes;

    private $_user;

    private $_table = 'photos';

    public function __construct()
    {
        parent::__construct();
        $this->_allpwedTypes = array('jpg', 'jpeg', 'png');
        $this->_user = new User();
    }

    public function getStickers() {
        $stickers = scandir($this->_stickerPath);
        $urls = array();
        foreach ($stickers as $sticker) {
            if ($sticker !== '.' &&  $sticker !== '..') {
                $urls[] = $this->_stickerPath . $sticker;
            }
        }
        return json_encode($urls);
    }

    public function upload() {
        if (array_key_exists('photo_upload', $_FILES) && $_FILES['photo_upload']['error'] === 0) {
            if ($_FILES['photo_upload']['size'] > 5000000) {
                return json_encode(['error' => 'file too big']);
            } else if (!in_array(explode('.', $_FILES['photo_upload']['name'])[1], $this->_allpwedTypes)) {
                return json_encode(['error' => 'wrong file type']);
            } else {
                return json_encode(['success' => $this->createTemporaryFile()]);
            }
        } else {
            return json_encode(['error' => 'Unexpected error']);
        }
    }

    public function uploadSticker() {
        if (array_key_exists('sticker_upload', $_FILES) && $_FILES['sticker_upload']['error'] === 0) {
            if ($_FILES['sticker_upload']['size'] > 5000000) {
                return json_encode(['error' => 'file too big']);
            } else if (!in_array(explode('.', $_FILES['sticker_upload']['name'])[1], [0 => 'png'])) {
                return json_encode(['error' => 'wrong file type']);
            } else {
                return json_encode(['success' => $this->createSticker()]);
            }
        } else {
            return json_encode(['error' => 'Unexpected error']);
        }
    }

    private function createSticker() {
        $image = imagecreatefrompng($_FILES['sticker_upload']['tmp_name']);
        $resized = imagecreate(128, 128);
        $op = imagecolorallocate($resized, 0, 0, 0);
        $name = $this->getStickerName();
        imagecolortransparent($resized, $op);
        imagecopyresized($resized, $image, 0, 0, 0,0, 128, 128, getimagesize($_FILES['sticker_upload']['tmp_name'])[0], getimagesize($_FILES['sticker_upload']['tmp_name'])[1]);
        imagepng($resized, $this->_stickerPath . $name);
        return $name;
    }

    private function getStickerName() {
       return date("YmdHis") . '.png';
    }

    private function createTemporaryFile() {
        $filetype = explode('.', $_FILES['photo_upload']['name']);
        $resizer = new ImageRedactor($_FILES['photo_upload']['tmp_name'], $filetype[1], $this->_tmpPath);
        $resizer->splitImage();
        $resizer->saveResizedImage();
        $new_filename = $resizer->newImageName;
        unset($_FILES);
        return $new_filename;
    }

    public function createTempImage() {
        $file = explode(',', $_POST['img']);
        $newFilename =  date("YmdHis") . '.png';
        try {
            file_put_contents($this->_tmpPath . $newFilename, base64_decode($file[1]));
            return json_encode(['success' => $newFilename]);
        } catch (\Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    public function clearTemporaryFolder() {
        $files = glob($this->_tmpPath . '*');
        foreach($files as $file){
            if(is_file($file)) {
                unlink($file);
            }
        }
    }

    public function addPhotoToCollection($filename) {
        if (file_exists($this->_tmpPath . $filename)) {
            copy($this->_tmpPath . $filename, $this->_collectionPath . $filename);
            if ($userID = $this->_user->getUserId()) {
                $query = "INSERT INTO " . $this->_table . "(name, user_id) values ('" . $filename . "'," . $this->_user->getUserId() . ")";
                try {
                    $request = $this->pdo->prepare($query);
                    $request->execute();
                } catch (\PDOException $e) {
                    echo "Fail" . $e->getMessage();
                }
                return json_encode(['success' => 'file uploaded']);
            } else {
                return json_encode(['error' => 'cannot find user_id']);
            }

        } else {
            return json_encode(['error' => 'file not found']);
        }
    }

    public function getPhotoIdByName($name) {
        $query = "SELECT photo_id FROM photos WHERE name='" . $name . "'";
        try {
            $request = $this->pdo->prepare($query);
            $request->execute();
            return $request->fetchColumn();
        } catch (\PDOException $e) {
            echo "Fail" . $e->getMessage();
        }
    }
 }