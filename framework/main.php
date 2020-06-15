<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'app.php';
require dirname(__DIR__) . DS . 'Autoloader.php';
require dirname(__DIR__) . DS . 'app' . DS .'app.config.php';

$request_path = $_SERVER['PATH_INFO'];

$app_path = ROOT;

$path = substr($request_path, 1, strlen($request_path) - 1);
$url_segments = explode('/', $path);

$matches  = preg_grep('/dep-/i', $url_segments);

$controller = ucfirst($url_segments[0]) . 'Controller';
$controllerFileName = $controller . '.php';
$action = $url_segments[1];
$param = isset($url_segments[2]) ? $url_segments[2] : null;

if (count($matches) > 0) {
    $app_path = APP__ROOT . substr($matches[0], 4) . DS . $url_segments[1];  
    $controller = ucfirst($url_segments[2]) . 'Controller';
    $controllerFileName = $controller . '.php';
    $action = $url_segments[3];
    $param = isset($url_segments[4]) ? $url_segments[4] : null;
}

if (file_exists($app_path . DS . 'api' . DS . 'controllers' . DS . $controllerFileName)) {

    $ctrl = new $controller();

    if (method_exists($ctrl, $action)) {
        if ($param !== null) {
            $ctrl->$action($param);
        } 
        else {
            $ctrl->$action();
        }
    } 
    else {
        Response::NotFound("Invalid path, method does not exist: $path");
    }
} 
else {
    Response::NotFound("Invalid path: $path");
}
