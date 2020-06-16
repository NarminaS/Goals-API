<?php

require_once  dirname(__DIR__) . DS .'vendor/autoload.php';

class ApiController
{
    protected $authorize = true;
    protected $loggedIn = false;

    function __construct()
    {
        if ($this->authorize == true) $this->loggedIn = Auth::Authorize();
    }
}
