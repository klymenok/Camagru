<?php

/**
 * Created by PhpStorm.
 * User: KlymenokAlexey
 * Date: 25.07.17
 * Time: 21:26
 */

namespace model;

class Session
{
    public static function init()
    {
        if (session_id() == '') {
            session_start();
        }
    }

    public function isUserLoggedIn()
    {
        if (!array_key_exists('logged_in_user', $_SESSION) || $_SESSION['logged_in_user'] == null) {
            return false;
        }
        else {
            return true;
        }
    }

    public static function destroy()
    {
        session_destroy();
    }
}