<?php

require_once 'database.php';

try {
    $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
} catch (PDOException $e) {
    die("Connection error". $e->getMessage());
}

try {
    $query = $pdo->prepare("SHOW TABLES");
    $query->execute();
    $tables = $query->fetchAll();
} catch (\PDOException $e) {
    die("Connection error". $e->getMessage());
}

if (!in_array("users", $tables)) {
    try {
        $query =$pdo->prepare("CREATE TABLE `users` (
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
    } catch (\PDOException $e) {
            die("Connection error". $e->getMessage());
    }
}

if (!in_array("photos", $tables)) {
    try {
        $query = $pdo->prepare("CREATE TABLE `photos` (
            photo_id INT auto_increment NOT NULL,
            name VARCHAR (100),
            user_id INT,
            PRIMARY KEY (photo_id)
            )"
        );
        $query->execute();
    } catch (\PDOException $e) {
        die("Connection error". $e->getMessage());
    }
}

if (!in_array("comments", $tables)) {
    try {

        $query = $pdo->prepare("CREATE TABLE `comments` (
            comment_id INT auto_increment NOT NULL,
            user_id INT,
            photo_id INT,
            comment VARCHAR (1024),
            PRIMARY KEY (comment_id)
            )"
        );
        $query->execute();
    } catch (\PDOException $e) {
        die("Connection error". $e->getMessage());
    }
}

if (!in_array("likes", $tables)) {
    try {
        $query = $pdo->prepare("CREATE TABLE `likes` (
            like_id INT auto_increment NOT NULL,
            user_id INT,
            photo_id INT,
            PRIMARY KEY (like_id)
            )"
        );
        $query->execute();
    } catch (\PDOException $e) {
        die("Connection error". $e->getMessage());
    }
}

if (in_array("users", $tables) && in_array("photos", $tables)) {
    try {
        $query = $pdo->prepare("ALTER TABLE `photos` ADD FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE");
        $query->execute();
    } catch (\PDOException $e) {
        die("Connection error". $e->getMessage());
    }
}
if (in_array("comments", $tables) && in_array("photos", $tables)) {

    try {
        $query = $pdo->prepare("ALTER TABLE `comments` ADD FOREIGN KEY (photo_id) REFERENCES photos(photo_id) ON DELETE CASCADE");
        $query->execute();
    } catch (\PDOException $e) {
        die("Connection error". $e->getMessage());
    }
}
if (in_array("likes", $tables) && in_array("photos", $tables)) {

    try {
        $query = $pdo->prepare("ALTER TABLE `likes` ADD FOREIGN KEY (photo_id) REFERENCES photos(photo_id) ON DELETE CASCADE");
        $query->execute();
    } catch (\PDOException $e) {
        die("Connection error". $e->getMessage());
    }
}
if (in_array("comments", $tables) && in_array("photos", $tables)) {

    try {
        $query = $pdo->prepare("ALTER TABLE `comments` ADD FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE");
        $query->execute();
    } catch (\PDOException $e) {
        die("Connection error". $e->getMessage());
    }
}
if (in_array("likes", $tables) && in_array("photos", $tables)) {

    try {
        $query = $pdo->prepare("ALTER TABLE `likes` ADD FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE");
        $query->execute();
    } catch (\PDOException $e) {
        die("Connection error". $e->getMessage());
    }
}


