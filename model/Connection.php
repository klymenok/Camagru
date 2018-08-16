<?php

namespace model;

use PDO;
use PDOException;

class Connection
{
    private $dsn = "mysql:host=localhost;dbname=camagru;charset=utf8";

    private $user = "root";

    private $passwd = "KfGjX1988Rf";

    protected $pdo;

    protected $_imagesPath = 'view/images/';

    protected $_collectionPath = 'view/images/collection/';

    protected $_tmpPath = 'view/images/tmp/';
    protected $_stickerPath = 'view/images/stickers/';

    public function __construct()
    {
        $this->pdo = $this->initPDO();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (!file_exists(RT . $this->_collectionPath)) {
            mkdir(RT . $this->_collectionPath);
        }
        if (!file_exists(RT . $this->_tmpPath)) {
            mkdir(RT . $this->_tmpPath);
        }
    }

    private function initPDO()
    {
        try {
            $pdo = new PDO($this->dsn, $this->user, $this->passwd);
        } catch (PDOException $e) {
            die("Connection error". $e->getMessage());
        }
        return $pdo;
    }
}