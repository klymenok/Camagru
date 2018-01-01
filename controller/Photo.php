<?php
/**
 * Created by PhpStorm.
 * User: oklymeno
 * Date: 11/22/17
 * Time: 12:43 PM
 */

namespace controller;


class Photo extends Controller implements ControllerInterface
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute() {
        $this->view->render('photo');
    }
}