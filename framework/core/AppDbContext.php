<?php

require_once 'DbContext.php';
require_once 'Response.php';

class AppDbContext extends DbContext
{
    public $dbname = 'id12537772_goalsdb';
    function __construct($dbname = 'id12537772_goalsdb')
    {
        parent::__construct();
        $this->dbname = $dbname;
        $this->SetupDb();
    }

    function __destruct()
    {
        R::close();
    }

    private function SetupDb()
    {
        if (!R::testConnection()) {
            R::setup("mysql:host=$this->dbhost;dbname=$this->dbname", $this->dbuser, $this->dbpassword, true);
            //Response::InternalServerError('No database connection');
        }
    }

    public function getPdo()
    {
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        return new PDO("mysql:host=$this->dbhost;dbname=$this->dbname", $this->dbuser, $this->dbpassword, $opt);
    }

    public function GetAll($sql)
    {
        return R::getAll($sql);
    }

    public function Find($type, $id)
    {
        return R::load($type, $id);
    }

    public function findUser($email)
    {
        $user = R::find("users", "email=?", array($email));
        return R::exportAll($user);
    }

    public function validatePassword($providedPassword, $hash)
    {
        $valid = password_verify($providedPassword, $hash);
        return $valid;
    }

    public function FindAll($sql, $bindings = array())
    {
        return R::getAll($sql, $bindings);
    }

    public function Saved($entity)
    {
        return R::store($entity) > 0;
    }

    public function isNull($data)
    {
        return $data['id'] == 0;
    }

    public function hasDublicate($table, $col, $val)
    {
        $dublicate = R::findAndExport($table, "$col = ?", array($val));
        return count($dublicate) != 0;
    }
}
