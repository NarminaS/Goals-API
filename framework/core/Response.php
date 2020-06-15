<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: token, Content-Type');
    header('Access-Control-Max-Age: 1728000');
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    die();
}

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

class Response
{
    public static function Ok($data)
    {
        http_response_code(200);
        echo json_encode($data);
        exit();
    }

    public static function Created($message)
    {
        http_response_code(201);
        echo $message;
        exit();
    }

    public static function BadRequest($message)
    {
        http_response_code(400);
        echo $message;
        exit();
    }

    public static function Unauthorized($message)
    {
        http_response_code(401);
        echo $message;
        exit();
    }

    public static function Forbidden($message)
    {
        http_response_code(403);
        echo $message;
        exit();
    }

    public static function NotFound($message)
    {
        http_response_code(404);
        echo $message;
        exit();
    }

    public static function InternalServerError($message)
    {
        http_response_code(500);
        echo $message;
        exit();
    }
}

