<?php

/**
 * Created by PhpStorm.
 * User: KlymenokAlexey
 * Date: 25.07.17
 * Time: 21:40
 */
namespace controller;

use model\Session;

class Index extends Controller implements ControllerInterface
{
    private $session;

    public function __construct()
    {
        parent::__construct();
    }

    public function execute()
    {
        $this->session = new Session();
        $this->view->renderStartPage();
    }
}