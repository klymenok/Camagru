<?php
/**
 * Created by PhpStorm.
 * User: oklymeno
 * Date: 11/23/17
 * Time: 4:21 AM
 */

namespace model;


class Mail
{
    public function __construct()
    {
    }

    public function sendEmail($email, $subject, $message) {
        $encoding = "utf-8";
        $subject_preferences = array(
            "input-charset" => $encoding,
            "output-charset" => $encoding,
            "line-break-chars" => "\r\n"
        );
        $header = "Content-type: text/html; charset=".$encoding." \r\n";
        $header .= "From: Camagru <noreply@camagru.com> \r\n";
        $header .= "MIME-Version: 1.0 \r\n";
        $header .= "Content-Transfer-Encoding: 8bit \r\n";
        $header .= "Date: ".date("r (T)")." \r\n";
        $header .= iconv_mime_encode("Subject", $subject, $subject_preferences);
        $send = mail($email, $subject, $message, $header);
        if($send == true )
            return true;
        else
            return false;
    }
}