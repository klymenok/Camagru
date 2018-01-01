<?php

namespace model;

class User extends Connection
{

    private $_table;

    private $_mail;

    public function __construct() {
        parent::__construct();
        $this->_table = 'users';
        $this->_mail = new Mail();
    }

    private function auth() {
        $login = "'" . $_POST['login'] . "'";
        $password = $_POST['passwd'];
        if (strlen($password) < 6) {
            return -2;
        }
        $query = "SELECT password, confirmed from " . $this->_table . " WHERE login=" . $login . " OR email=" . $login;
        try {
            $response = $this->pdo->prepare($query);
            $response->execute();
            $data = $response->fetch(\PDO::FETCH_ASSOC);
            if (!empty($data)) {
                if ($data['confirmed'] == 0) {
                    return -1;
                }
                if ($data['password'] === hash('whirlpool', $password)) {
                    return 1;
                }
            }
            return 0;
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function login() {
        if (session_id() === '') {
            session_start();
        }
        $authCode = $this->auth();
        if ($authCode === 1) {
            $_SESSION['logged_in_user'] = $_POST['login'];
            return json_encode(['success' => 'success']);
        } else if ($authCode === -1) {
            return json_encode(['error' => 'Please, activate your account - check your mail']);
        } else if ($authCode === -2) {
            return json_encode(['error' => 'Password is too short, use at least 6 symbols']);
        } else {
            $_SESSION['logged_in_user'] = NULL;
            if ($this->isUserExist()) {
                return json_encode(['error' => 'Invalid password']);
            } else {
                return json_encode(['error' => 'User not found. Please, check username / email']);
            }
        }
    }

    public function getUser() {
        if (array_key_exists('logged_in_user', $_SESSION) && $_SESSION['logged_in_user'] !== null) {
            return json_encode(['success' => $_SESSION['logged_in_user']]);
        }
        else {
            return json_encode(['error' => "guest"]);
        }
    }

    public function getUserName() {
        if (array_key_exists('logged_in_user', $_SESSION) && $_SESSION['logged_in_user'] !== null) {
            return $_SESSION['logged_in_user'];
        }
    }

    public function getUserId() {
        if ($this->isUserLoggedIn()) {
            $query = "SELECT user_id FROM " . $this->_table . " WHERE login='" . $this->getUserName() . "'";
            try {
                $request = $this->pdo->prepare($query);
                $request->execute();
                $userId = $request->fetchColumn();
            } catch (\PDOException $e) {}
            if (isset($userId) && is_string($userId)) {
                return $userId;
            }
        }
        return 0;
    }

    public function createUser() {
        $pass = hash('whirlpool', $_POST['passwd']);
        $login = $_POST['login'];
        $confirm = hash('whirlpool', $_POST['passwd_conf']);
        $email = $_POST['email'];
        if ($pass !== $confirm) {
            return "Passwords doesn't match";
        }
        if ($this->isUserAlreadyExist($_POST['login'], $_POST['email'])) {
            return $this->isUserAlreadyExist($_POST['login'], $_POST['email']);
        }
        $query = "INSERT INTO " . $this->_table ." (login, email, password)  VALUES ('"
            . $login . "', '"
            . $email . "', '"
            . $pass . "')";
        try {
            $this->pdo->prepare($query)->execute();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        $confirmationCode = $this->createConfirmationCode($email);
        $this->sendConfirmationEmail($email, $confirmationCode);
        return "success";
    }

    private function isUserAlreadyExist($login, $email) {
        $loginQuery = "SELECT * from " . $this->_table . " WHERE login='" . $login . "'";
        /** @var /model/Connection $_connection */
        try {
            $result = $this->pdo->prepare($loginQuery);
            $result->execute();
            $data = $result->fetch();
        } catch (\PDOException $e) {}
        if (isset($data) && $data) {
            return "This login is already used";
        }
        if (array_key_exists('email', $_POST)) {
            $emailQuery = "SELECT * from " . $this->_table . " WHERE email='" . $email . "'";
            try {
                $result = $this->pdo->prepare($emailQuery);
                $result->execute();
                $data = $result->fetch();
            } catch (\PDOException $e) {}
            if (isset($data) && $data) {
                return "This email is already used";
            }
        }
        return false;
    }

    private function isUserLoggedIn() {
        if (array_key_exists('logged_in_user', $_SESSION) && $_SESSION['logged_in_user'] != null)
            return true;
        return false;
    }

    public function logout() {
        $_SESSION['logged_in_user'] = null;
    }

    public function isUserExist() {

        if (!$this->isUserAlreadyExist($_POST['login'], $_POST['email'])) {
            return false;
        } else {
            return true;
        }
    }

    public function getUsernameById($id) {
        $query = "SELECT login from " . $this->_table . " WHERE user_id=" . $id;
        /** @var /model/Connection $_connection */
        try {
            $result = $this->pdo->prepare($query);
            $result->execute();
            return $result->fetchColumn();
        } catch (\PDOException $e) {}
    }

    public function getEmailById($id) {
        $query = "SELECT email from " . $this->_table . " WHERE user_id=" . $id;
        /** @var /model/Connection $_connection */
        try {
            $result = $this->pdo->prepare($query);
            $result->execute();
            return $result->fetchColumn();
        } catch (\PDOException $e) {}
    }

    private function createConfirmationCode($email) {
        $code = rand(100000, 999999);
        $this->pdo->prepare("UPDATE " . $this->_table . " SET confirmation_code=" . $code . " WHERE email='" . $email . "'")->execute();
        return $code;
    }

    private function sendConfirmationEmail($email, $confirmationCode) {
        $link = 'http://' . $_SERVER['HTTP_HOST'] . '/camagru/user/activate?id=' . $confirmationCode ;
        $subject = 'Camagru new account';
        $message = '<h2> To activate your account, please follow the link</h2></br>' . $link;
        return $this->_mail->sendEmail($email, $subject, $message);
    }

    private function sendResetEmail($email, $confirmationCode) {
        $link = $confirmationCode;
        $subject = 'Camagru password reset';
        $message = '<h2> Your new password is: </h2></br>' . $link . '<br>Please, change in sa soon as you can';
        return $this->_mail->sendEmail($email, $subject, $message);
    }

    public function resetPassword() {
        $login = "'" . $_POST['login'] . "'";
        $query = "SELECT email FROM " . $this->_table . " WHERE login=" . $login . " OR email=" . $login;
        $response = $this->pdo->prepare($query);
        $response->execute();
        $email = $response->fetch()['email'];
        if (!$email) {
            return "Username not found";
        } else {
            $confirmationCode = $this->createConfirmationCode($email);
            if ($this->setNewPassword($login, hash('whirlpool', $confirmationCode))) {
                $this->sendResetEmail($email, $confirmationCode);
                return "success";
            } else {
                return 'error';
            }
        }
    }

    private function setNewPassword($login, $password) {
        if (!empty($login)) {
            $query = "UPDATE users SET password='" . $password . "' WHERE login=" . $login . " OR email=" . $login;
            try {
                $response = $this->pdo->prepare($query);
                return $response->execute();
            } catch (\PDOException $e) {
                return false;
            }
        }
    }

    public function getEmail() {
        $username = $this->getUserName();
        $query = "SELECT login, email FROM " . $this->_table . " WHERE login='" . $username . "'";
        try {
            $response = $this->pdo->prepare($query);
            $response->execute();
            $data = $response->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($data)) {
                return json_encode(['success' => $data]);
            } else {
                return json_encode(['error' => 'error1']);
            }
        } catch (\PDOException $e) {
            return json_encode(['error' => 'error']);
        }
    }

    private function _getEmail() {
        $username = $this->getUserName();
        $query = "SELECT email FROM " . $this->_table . " WHERE login='" . $username . "'";
        try {
            $response = $this->pdo->prepare($query);
            $response->execute();
            $data = $response->fetchColumn();
            if (!empty($data)) {
                return $data;
            } else {
                return "";
            }
        } catch (\PDOException $e) {
            return "";
        }
    }


    public function changeInformation($type) {
        if ($type === 'password') {
            if (array_key_exists('old_password', $_POST) && !empty($_POST['old_password']) &&
                array_key_exists('new_password', $_POST) && !empty($_POST['new_password']) &&
                array_key_exists('new_password_confirm', $_POST) && !empty($_POST['new_password_confirm'])){
                if ($_POST['new_password'] !== $_POST['new_password_confirm']) {
                    return json_encode(['error' => "New passwords does't match"]);
                }
                if ($_POST['new_password'] === $_POST['old_password']) {
                    return json_encode(['error' => "Old password and new password are equal"]);
                }
                $password =  $this->getUserPassword();
                if ($password !== hash('whirlpool', $_POST['old_password'])) {
                    return json_encode(['error' => "Wrong old password"]);
                }
                if (!$this->changeData('password', hash('whirlpool', $_POST['new_password']))) {
                    return json_encode(['error' => "Cannot update password"]);
                } else {
                    $this->_mail->sendEmail($this->_getEmail(), 'Camagru password changed', 'Your password has been changed');
                    return json_encode(['success' => "Password updated"]);
                }
            } else {
                return json_encode(['error' => 'Fill all fields']);
            }
        } else if ($type === 'data') {
            if (array_key_exists('username', $_POST) && !empty($_POST['username']) &&
                array_key_exists('email', $_POST) && !empty($_POST['email'])){

                $old_username = $this->getUserName();
                $old_email = $this->_getEmailByUsername($old_username);
                if ($old_username === $_POST['username'] && $old_email === $_POST['email']) {
                    return json_encode(['error' => 'Nothing to change']);
                } else {
                    if ($old_username !== $_POST['username']) {
                        if ($this->isUserAlreadyExist($_POST['username'], $_POST['username'])) {
                            return json_encode(['error' => 'Username is already in use']);
                        } elseif (!$this->changeData('login', $_POST['username'])) {
                            return json_encode(['error' => 'error']);
                        }
                        $_SESSION['logged_in_user'] = $_POST['username'];
                    }
                    if ($old_email !== $_POST['email']) {
                        if ($this->isUserAlreadyExist($_POST['email'], $_POST['email'])) {
                            return json_encode(['error' => 'Email is already in use']);
                        }
                        if (!$this->changeData('email', $_POST['email'])) {
                            return json_encode(['error' => 'error']);
                        }
                    }
                    return json_encode(['success' => 'success']);
                }
            } else {
                return json_encode(['error' => 'Fill all fields']);
            }
        } else {
            return json_encode(['error' => 'unknown data to change']);
        }
    }

    private function getUserPassword() {
        $userId = $this->getUserId();
        $query = "SELECT password FROM users WHERE user_id=" . $userId;
        try {
            $response = $this->pdo->prepare($query);
            $response->execute();
            return $response->fetchColumn();
        } catch (\PDOException $e) {
            return "";
        }
    }

    private function changeData($data, $value) {
        if (!empty($data)) {
            $userId = $this->getUserId();
            $query = "UPDATE users SET $data='" . $value . "' WHERE user_id=" . $userId;
            try {
                $response = $this->pdo->prepare($query);
                return $response->execute();
            } catch (\PDOException $e) {
                return false;
            }
        }
    }

    private function _getEmailByUsername($username) {
        if (!empty($username)) {
            $query = "SELECT email FROM " . $this->_table . " WHERE login='" . $username . "'";
            try {
                $response = $this->pdo->prepare($query);
                $response->execute();
                $data = $response->fetchColumn();
                if (!empty($data)) {
                    return $data;
                } else {
                    return "";
                }
            } catch (\PDOException $e) {
                return $query;
            }
        } else {
            return "";
        }

    }

    public function activate($id) {
        $selectQuery = "SELECT user_id FROM users WHERE confirmation_code='" . $id . "'";
        try {
            $response = $this->pdo->prepare($selectQuery);
            $response->execute();
            if ($response->fetchColumn()) {
                $query = "UPDATE users SET confirmed=1 WHERE confirmation_code='" . $id . "'";

                try {
                    $response = $this->pdo->prepare($query);
                    $response->execute();
                } catch(\PDOException $e) {

                }
            }
        } catch (\PDOException $e) {

        }
    }
}