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

    public function validateRequestCredentials(User $user) {
        if (!$this->userDAL->fetchUser($user)) {
            throw new WrongCredentialsException();
        }

        if (!$this->verifyPassword($user)) {
            throw new WrongCredentialsException();
        }
    }

    private function verifyPassword (User $user) : bool {
        $userData = $this->userDAL->fetchUser($user);
        return password_verify($user->getPassword(), $userData[self::$colPwd]);       
    }

    public function validateAuthCredentials(User $user) {
        $expireDateCheck = $this->verifyExpireDate($user);
        $pwdTokenCheck = $this->verifyTempPwd($user);

        if (!$expireDateCheck || !$pwdTokenCheck ) {
            throw new \Model\WrongCookieInfoException();
        }
    }

    private function verifyExpireDate($user) : bool {
        $this->userData = $this->authDAL->getAuthUser($user); 
        $currentDate = date("Y-m-d H:i:s", time());
        $expireDate = $this->userData[self::$colExpireDate];
        return $expireDate > $currentDate;
    }  

    private function verifyTempPwd(User $user) : bool {
        $this->userData = $this->authDAL->getAuthUser($user); 
        return password_verify($user->getPassword(), $this->userData[self::$colTempPwd]);
    }

    public function saveAuthCredentials($user) {
        $this->authDAL->saveAuthUser($user);
    }

}