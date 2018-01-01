<?php
/**
 * Created by PhpStorm.
 * User: KlymenokAlexey
 * Date: 29.07.17
 * Time: 07:57
 */

namespace controller;


class Noroute extends Controller implements ControllerInterface
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute()
    {
        $this->view->render('noroute');
    }
}