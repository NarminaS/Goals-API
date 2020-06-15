<?php
require_once dirname(__DIR__) . '/libs/DbConfig.php';
require_once dirname(__DIR__) . '/libs/rb-mysql.php';

class DbContext  
{
    public $dbhost;
    public $dbuser;
    public $dbpassword;

    function __construct(){
        $this->dbhost = DbConfig::$dbhost;
        $this->dbuser = DbConfig::$dbuser;
        $this->dbpassword = DbConfig::$dbpassword;
    }

}
