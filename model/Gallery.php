<?php
/**
 * Created by PhpStorm.
 * User: oklymeno
 * Date: 11/16/17
 * Time: 5:05 PM
 */

namespace model;


class Gallery extends Connection
{
    private $_mainTable = 'photos';
    private $_likes = 'likes';
    private $_comments = 'comments';

    private $_user;
    private $_photo;

    public function __construct()
    {
        parent::__construct();
        $this->_user = new User();
        $this->_photo = new Camera();
    }

    public function getData() {
        $offset = $_GET['items'];
        $userId = $this->_user->getUserId();
        $query = "SELECT users.login, 
                  (select count(likes.photo_id) FROM `likes` WHERE photos.photo_id = likes.photo_id) as `likes`,
                  (select count(comments.photo_id) FROM `comments` WHERE photos.photo_id = comments.photo_id) as `comments_count`,
                  (select count(likes.user_id) FROM `likes` WHERE photos.photo_id = likes.photo_id and likes.user_id = " . $userId . ") as `liked`,
                  photos.name as 'photo',
                  (SELECT COUNT(photos.photo_id) FROM photos) as 'total'
                  FROM `photos`
                  LEFT JOIN users on users.user_id = photos.user_id
                  LIMIT 12 OFFSET ". $offset;
        $request = $this->pdo->prepare($query);
        $request->execute();
        $data = $request->fetchAll(\PDO::FETCH_ASSOC);
        $result = $this->createFilenames($data);
        return json_encode($result);
    }

    public function getSingleData($name = null) {
        $userId = $this->_user->getUserId();
        $query = "SELECT users.login, 
                  (select count(likes.photo_id) FROM `likes` WHERE photos.photo_id = likes.photo_id) as `likes`,
                  (select count(comments.photo_id) FROM `comments` WHERE photos.photo_id = comments.photo_id) as `comments_count`,
                  (select count(likes.user_id) FROM `likes` WHERE photos.photo_id = likes.photo_id and likes.user_id = " . $userId . ") as `liked`,
                  photos.name as 'photo',
                  (SELECT COUNT(photos.photo_id) FROM photos) as 'total'
                  FROM `photos`
                  LEFT JOIN users on users.user_id = photos.user_id
                  WHERE photos.name='". $name . "'";
        $request = $this->pdo->prepare($query);
        $request->execute();
        $data = $request->fetchAll(\PDO::FETCH_ASSOC);
        $result = $this->createFilenames($data);
        return json_encode($result);
    }

    private function createFilenames($data) {
        $result = array();
        foreach ($data as $item) {
            $newItem = $item;
            if (array_key_exists('photo', $item)) {
                $newItem['photo'] = $this->_collectionPath . $item['photo'];
            }
            $result[] = $newItem;
        }
        return $result;
    }

    public function setLike($filename) {
        $userId = $this->_user->getUserId();
        $photoId = $this->_photo->getPhotoIdByName($filename);
        $query = "SELECT COUNT(*) FROM likes WHERE user_id='" . $userId . "' AND photo_id='" . $photoId . "'";
        try {
            $request = $this->pdo->prepare($query);
            $request->execute();
            $count = $request->fetchColumn();
            if ($count == 0) {
                $this->like($userId, $photoId);
            } else {
                $this->dislike($userId, $photoId);

            }
        } catch (\PDOException $e) {}
    }

    private function like($userId, $photoId) {
        $query = "INSERT INTO likes(photo_id, user_id) VALUES (" . $photoId . " , " .$userId . ")";
        try {
            $request = $this->pdo->prepare($query);
            $request->execute();
        } catch (\PDOException $e) {}

    }

    private function dislike($userId, $photoId) {
        $query = "DELETE FROM likes WHERE photo_id=" . $photoId . " AND user_id=" .$userId;
        try {
            $request = $this->pdo->prepare($query);
            $request->execute();
        } catch (\PDOException $e) {}
    }

    public function getComments($name) {
        $query = "SELECT comments.comment,
                    (SELECT users.login from users WHERE users.user_id = comments.user_id) as `login`
                    FROM comments
                    WHERE comments.photo_id = (SELECT photos.photo_id FROM photos WHERE name='" . $name ."')
                    ";
        try {
            $request = $this->pdo->prepare($query);
            $request->execute();
            $data = $request->fetchAll(\PDO::FETCH_ASSOC);
            return json_encode($data);
        } catch (\PDOException $e) {}
    }

    public function addComment($photoName) {
        $userId = $this->_user->getUserId();
        $photoId = $this->_photo->getPhotoIdByName($photoName);
        if (array_key_exists('comment', $_POST)) {
            $comment = $_POST['comment'];
            $query = "INSERT INTO comments(user_id, photo_id, comment) VALUES (" . $userId . ", " . $photoId . ", '" . $comment . "')";
            try {
                $request = $this->pdo->prepare($query);
                $request->execute();
                $this->sendCommentLetter($comment, $photoId);
                return json_encode(['success' => $this->_user->getUserName()]);
            } catch (\PDOException $e) {
                return json_encode(['error' => 'database error']);
            }
        } else {
            return json_encode(['error' => 'empty comment']);
        }
    }


    private function sendCommentLetter($comment, $photoId) {
        $query = "SELECT user_id FROM photos WHERE photo_id=" . $photoId;
        try {
            $response = $this->pdo->prepare($query);
            $response->execute();
            $userId = $response->fetchColumn();
        } catch (\PDOException $e) {
            $userId = 0;
        }
        $email = $this->_user->getEmailById($userId);
        $subject = 'New comment on Camagru';
        $message = $this->_user->getUserName() . " commented your photo: \"" . $comment . "\"";
        $mail = new Mail();
        $mail->sendEmail($email, $subject, $message);
    }

    public function deletePhoto($name) {
        $userId = $this->_user->getUserId();
        $photoId = $this->_photo->getPhotoIdByName($name);
        $query = "SELECT user_id FROM photos WHERE photo_id=" .$photoId;
        try {
            $response = $this->pdo->prepare($query);
            $response->execute();
            $user = $response->fetchColumn();
            if ($user === $userId) {
                $this->delete($photoId);
                return json_encode(['success' => 'deleted']);
            }
            return json_encode(['error' => 'permission denied']);
        } catch (\PDOException $e) {
            return json_encode(['error' => 'permission denied']);
        }
    }

    private function delete($id) {
        $query = "DELETE FROM photos WHERE photo_id=" . $id;
        try {
            $request = $this->pdo->prepare($query);
            $request->execute();
        } catch (\PDOException $e) {}
    }

    public function isPhotoExist($photo) {
        $query = "SELECT COUNT(*) FROM photos WHERE name='" . $photo . "'";
        try {
            $response = $this->pdo->prepare($query);
            $response->execute();
            $count = $response->fetchColumn();
            if ($count > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            return false;
        }
    }
}