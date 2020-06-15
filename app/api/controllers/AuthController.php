<?php

class AuthController extends ApiController
{
    private $repo;
    //private $pdo;

    function __construct()
    {
        $this->authorize = false;
        parent::__construct();
        $this->repo = new AppDbContext('userstore');
        //$this->pdo = $this->repo->getPdo();
    }

    public function login()
    {
        $model = new AuthModel();
        $email = $model->Email;
        $password = $model->Password;

        $user = $this->repo->findUser($email);
        if (count($user) != 0) {
            if ($this->repo->validatePassword($password, $user[0]['passwordhash'])) {

                $role = R::getCell('SELECT roles.name FROM `userroles` LEFT JOIN roles ON userroles.role_id=roles.id WHERE userroles.user_id=? LIMIT 1', array($user[0]['id']));
                $token = Auth::generateToken($user[0], $role);
                $returnArray = Auth::getTokenPayload($token);
                Response::Ok($returnArray);
            }
            $this->defaultAdminLogin($email, $password);
            Response::Unauthorized("Invalid password");
        }
        Response::NotFound("$email not found");
    }

    private function defaultAdminLogin($email, $password)
    {
        $adminCount = count($this->repo->FindAll("SELECT * FROM `userroles` LEFT JOIN roles ON userroles.role_id=roles.id  WHERE roles.name=?", ['Admin']));
        if (!Auth::isDefaultLocked($adminCount)) {

            $returnArray = Auth::tryAuthenticateDefaultAdmin($email, $password);
            if ($returnArray !== false) {
                Response::Ok($returnArray);
            }
            Response::BadRequest('Email or password are invalid');
        }
        Response::Forbidden("Default admin is locked");
    }
}
