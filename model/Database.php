<?php


namespace model;

use PDO;

class Database extends \model\Connection
{

    public function __construct()
    {
        parent::__construct();
    }

    private function getTables($option = null)
    {
        try {
            $query = $this->pdo->prepare("SHOW TABLES");
            $query->execute();
            $result = $query->fetchAll($option);
            return $result;
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function createTables() {
        $tables = $this->getTables(PDO::FETCH_COLUMN);

        if (!in_array("users", $tables)) {
            $query = $this->pdo->prepare("CREATE TABLE `users` (
            user_id INT auto_increment NOT NULL,
            login VARCHAR (100),
            email VARCHAR (100),
            password VARCHAR (128),
            confirmed INT DEFAULT 0,
            confirmation_code VARCHAR (10),
            PRIMARY KEY (user_id)
            )"
            );
            $query->execute();
        }
        if (!in_array("photos", $tables)) {
            $query = $this->pdo->prepare("CREATE TABLE `photos` (
            photo_id INT auto_increment NOT NULL,
            name VARCHAR (100),
            user_id INT,
            PRIMARY KEY (photo_id)
            )"
            );
            $query->execute();
        }
        if (!in_array("comments", $tables)) {
            $query = $this->pdo->prepare("CREATE TABLE `comments` (
            comment_id INT auto_increment NOT NULL,
            user_id INT,
            photo_id INT,
            comment VARCHAR (1024),
            PRIMARY KEY (comment_id)
            )"
            );
            $query->execute();
        }
        if (!in_array("likes", $tables)) {
            $query = $this->pdo->prepare("CREATE TABLE `likes` (
            like_id INT auto_increment NOT NULL,
            user_id INT,
            photo_id INT,
            PRIMARY KEY (like_id)
            )"
            );
            $query->execute();
        }

        $this->createlinks();
    }

    private function createLinks() {
        $tables = $this->getTables(PDO::FETCH_COLUMN);
        if (in_array("users", $tables) && in_array("photos", $tables)) {
            $query = $this->pdo->prepare("ALTER TABLE `photos` ADD FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE");
            $query->execute();
        }
        if (in_array("comments", $tables) && in_array("photos", $tables)) {
            $query = $this->pdo->prepare("ALTER TABLE `comments` ADD FOREIGN KEY (photo_id) REFERENCES photos(photo_id) ON DELETE CASCADE");
            $query->execute();
        }
        if (in_array("likes", $tables) && in_array("photos", $tables)) {
            $query = $this->pdo->prepare("ALTER TABLE `likes` ADD FOREIGN KEY (photo_id) REFERENCES photos(photo_id) ON DELETE CASCADE");
            $query->execute();
        }
        if (in_array("comments", $tables) && in_array("photos", $tables)) {
            $query = $this->pdo->prepare("ALTER TABLE `comments` ADD FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE");
            $query->execute();
        }
        if (in_array("likes", $tables) && in_array("photos", $tables)) {
            $query = $this->pdo->prepare("ALTER TABLE `likes` ADD FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE");
            $query->execute();
        }
    }

    public function createDump()
    {
        system("mysqldump camagru > dump/backup-file_" . time() . ".sql");
    }
}