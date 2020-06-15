<?php

class OverviewController extends ApiController
{
    //private $repo;
    //private $pdo;
    function __construct()
    {
        $this->authorize = false;
        parent::__construct();
    }

    public function list($dep)
    {
        if (key_exists($dep, ERP__APPS)) {
            $app_list = ERP__APPS[$dep];
            Response::Ok($app_list);
        }
        Response::NotFound("$dep applications not found");
    }
}
