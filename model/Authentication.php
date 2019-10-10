<?php

namespace Model;

require_once('DAL/DbUserTable.php');
require_once('DAL/DbAuthTable.php');

class Authentication {
    private $auhtDAL;
    private $userDAL;
    private static $colTempPwd= 'passwordHash';
    private static $colPwd = 'password';
    private static $colExpireDate = 'expireDate';


    public function __construct() {
        $this->authDAL = new \Model\DbAuthTable();
        $this->userDAL = new \Model\DbUserTable();
    }

    public function validateRequestCredentials(UserCredentials $credentials) : User {
        $user = $this->userDAL->getUser($credentials);
        if (!$user) {
            throw new WrongCredentialsException();
        }

        if (!$this->verifyPassword($credentials)) {
            throw new WrongCredentialsException();
        }

        return $user;
    }

    private function verifyPassword (UserCredentials $credentials) : bool {
        $userData = $this->userDAL->getUser($credentials);
        return password_verify($credentials->getPassword(), $userData[self::$colPwd]);       
    }

    public function validateAuthCredentials(UserCredentials $credentials) : User{
        $user = $this->authDAL->getAuthUser($credentials);

        $expireDateCheck = $this->verifyExpireDate($credentials);
        $pwdTokenCheck = $this->verifyTempPwd($credentials);

        if (!$expireDateCheck || !$pwdTokenCheck ) {
            throw new \Model\WrongAuthCredentialsException();
        }

        return $user;
    }

    private function verifyExpireDate(UserCredentials $credentials) : bool {
        $userData = $this->authDAL->getAuthUser($credentials); 
        $currentDate = date("Y-m-d H:i:s", time());
        $expireDate = $userData[self::$colExpireDate];
        return $expireDate > $currentDate;
    }  

    private function verifyTempPwd(UserCredentials $credentials) : bool {
        $userData = $this->authDAL->getAuthUser($credentials); 
        return password_verify($credentials->getPassword(), $userData[self::$colTempPwd]);
    }

    public function saveAuthCredentials(User $user) {
        $this->authDAL->saveAuthUser($user);
    }

}