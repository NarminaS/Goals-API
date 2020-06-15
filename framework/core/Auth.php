<?php
require_once '../Autoloader.php';

use \Firebase\JWT\JWT;

class Auth
{
    private static $secret_key = SECRET__KEY;
    private static $admin_username = "default-username@enc.az";
    private static $admin_password = "default-password";
    private static $admin_name = "Default";
    private static $admin_surname = "Admin";

    private static $max_real_admin_count = 2;

    public static function validatePassword($providedPassword)
    {
        $hash = password_hash($providedPassword, PASSWORD_BCRYPT);
        $valid = password_verify($providedPassword, $hash);
        return $valid;
    }

    public static function Authorize()
    {
        if (!array_key_exists('Authorization', apache_request_headers())) {
            Response::Unauthorized("Permission denied. Go to home page");
        }
        $bearer = apache_request_headers()['Authorization'];
        $token = explode(' ', $bearer)[1];
        try {
            return (array) $decoded = JWT::decode($token, self::$secret_key, array('HS256'));
        } 
        catch (Exception $e) {
            Response::Unauthorized("Expired session");
        }
    }

    public static function generateToken($user, $role, $days = 1, $is_default_admin = false)
    {
        $token = array(
            "iss"  => "erp.encotec.az",
            "aud"  => "erp.encotec.az",
            "iat"  => time(),
            "nbf"  => time(),
            "exp"  => (time() + (86400 * $days)) * 1000,
            "name" => $user['fullname'],
            "id"   => $user['id'],
            "role" => $role,
            "is_default_admin" => $is_default_admin
        );
        return $token;
    }

    public static function getTokenPayload($token)
    {
        $jwt = JWT::encode($token, self::$secret_key);
        $returnArray = array('token' => $jwt);
        return $returnArray;
    }

    public static function tryAuthenticateDefaultAdmin($provided_username, $provided_password)
    {
        $admin_hash = hash_pbkdf2('sha256', self::$admin_password, self::$admin_username, 20, 20);
        $input_hash = hash_pbkdf2('sha256', $provided_password, $provided_username, 20, 20);

        if ($admin_hash == $input_hash) {

            $user = array('fullname' => self::$admin_name . " " . self::$admin_surname, 'id' => 0);
            $role = 'Default Admin';
            $token = self::generateToken($user, $role, 1, true);
            $payload = self::getTokenPayload($token);
            return $payload;
        }
        return false;
    }

    public static function isDefaultLocked($real)
    {
        return $real >= self::$max_real_admin_count;
    }
}
