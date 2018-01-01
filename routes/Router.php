<?php

namespace routes;

use controller\Index;
//use controller\Login;
use controller\Noroute;

class Router
{
    private $controller;

    private $action;

    public $params;


    public function __construct()
    {
        $this->getUrl();

        if (!$this->controller) {
            $this->controller = new Index();
            $this->controller->execute();
        } elseif (file_exists(RT . 'controller/' . $this->controller . '.php')) {
            $controller_name = 'controller\\' . ucfirst($this->controller);
            $this->controller = new $controller_name();
            if (!$this->action) {
                $this->controller->execute();
            } elseif (method_exists($controller_name, $this->action)) {
                $this->controller = new $controller_name();
                call_user_func_array(array($this->controller, $this->action), array($this->params));
            } else {
                $this->controller = new Noroute();
                $this->controller->execute();
            }
        } else {
            $this->controller = new Noroute();
            $this->controller->execute();
        }
    }

    private function getUrl()
    {
        $i = 1;
        $url = explode(DS, trim($_SERVER['REQUEST_URI'], DS));
        $this->controller = isset($url[$i]) ? explode('?', ucfirst($url[$i]))[0] : null;
        $method = isset($url[$i + 1]) ? explode('?', $url[$i + 1]) : null;
        $this->action = isset($method[0]) ? $method[0] : null;
        unset($url[$i], $url[$i + 1]);
        $this->params = isset($method[1]) ? $this->getDataArray($method[1]) : null;
    }

    private function getDataArray($data)
    {
        $items = explode('&', $data);
        $result = array();
        foreach ($items as $item)
        {
            $temp = explode('=', $item);
            if (!empty($temp[0]) && !empty($temp[1])) {
                $result[$temp[0]] = $temp[1];
            }
        }
        return $result;
    }
}