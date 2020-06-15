<?php

require_once dirname(__DIR__) . '/util/app.php';
require_once dirname(__DIR__) . '/Autoloader.php';


class Router
{
    private $url_segments = array();
    private $is_root_app = true;

    private $full_path = '';

    private $controller = null;
    private $action = null;
    private $param = null;

    function __construct($request_url)
    {
        $this->url_segments = $this->getUrlSegments($request_url);
        $this->is_root_app = $this->isRootAppRequested($this->url_segments);
        $this->full_path = $this->getPathToController();
    }

    private function getUrlSegments($request_url)
    {
        $path = substr($request_url, 1, strlen($request_url) - 1);
        return explode('/', $path);
    }

    private function isRootAppRequested($url_segments)
    {
        $matches = preg_grep('/dep-/i', $url_segments);
        if (count($matches) == 0)
            return true;
        else
            return false;
    }

    private function getPathToController()
    {
        $path_to_api = APP__ROOT;
        if (!$this->is_root_app) {

            $department = substr($this->url_segments[0], 4);
            $application = $this->url_segments[1];
            $this->controller = ucfirst($this->url_segments[2]) . 'Controller';
            $this->action = $this->url_segments[3];
            $this->param = isset($this->url_segments[4]) ?  $this->url_segments[4] : null;
            $path_to_api = APP__ROOT . $department . DS . $application . DS;
        } else {
            $this->controller = ucfirst($this->url_segments[0]) . 'Controller';
            $this->action = $this->url_segments[1];
            $this->param = isset($this->url_segments[2]) ?  $this->url_segments[2] : null;
        }

        $full_path = $path_to_api . 'api' . DS . $this->controller . '.php';
        return $full_path;
    }

    public function dispatch()
    {
        if (file_exists($this->full_path)) {

            $ctrl = new $this->controller;
            $method = $this->action;

            if (method_exists($ctrl, $method)) {

                if ($this->param !== null) {
                    $ctrl->$method($this->param);
                } else {
                    $ctrl->$method();
                }
            } 
            else {
                Response::NotFound("Invalid path, method:$this->method does not exist in controller $this->controller");
            }
        } else {
            Response::NotFound("Invalid path to controller: $this->full_path");
        }
    }
}
