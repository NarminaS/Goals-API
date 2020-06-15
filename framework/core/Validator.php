<?php
require_once 'Response.php';

class Validator
{
    private $model;
    function __construct()
    {
        $this->model = json_decode(file_get_contents('php://input'), true);
    }


    public function Required($props)
    {
        foreach ($props as $prop) {
            if (array_key_exists($prop, $this->model)) {
                if (!isset($this->model[$prop]) || strlen($this->model[$prop]) == 0) {
                    Response::BadRequest("$prop is required");
                }
            } 
            else {
                Response::BadRequest("$prop is required");
            }
        }
    }

    public function Number($props)
    {
        foreach ($props as $prop) {
            if (array_key_exists($prop, $this->model)) {
                if (filter_var($this->model[$prop], FILTER_VALIDATE_INT) !== false) {
                    if (intval($this->model[$prop]) < 0) {
                        Response::BadRequest("Number must be positive");
                    }
                } 
                else {
                    Response::BadRequest("You must provide number");
                }
            }
        }
    }

    public function Email($props)
    {
        foreach ($props as $prop) {
            if (array_key_exists($prop, $this->model)) {
                if (filter_var($this->model[$prop], FILTER_VALIDATE_EMAIL) == false) {
                    Response::BadRequest("Email is not in correct format");
                }
            }
        }
    }

    public function get($prop)
    {
        if (array_key_exists($prop, $this->model)) {
            if (isset($this->model[$prop])) {
                if (!is_array($this->model[$prop])) {
                    return htmlspecialchars($this->model[$prop]);
                }
                return $this->model[$prop];
            }
        }
        return null;
    }

    public function sanitize($value)
    {
        return htmlspecialchars($value);
    }

    public function model()
    {
        return $this->model;
    }
}
