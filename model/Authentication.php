<?php

namespace Model;

require_once('DAL/DbAuthTable.php');

class Authentication {
    private $storage;
    private static $colPwd = 'passwordHash';
    private static $colExpireDate = 'expireDate';


    public function __construct() {
        $this->storage = new \Model\DbAuthTable(); 
    }

    public function validateCredentials(User $user) {
       $this->verifyCookies($user);
       session_regenerate_id();
    }

    private function verifyPwdToken(User $user) : bool {
        $this->userData = $this->storage->getAuthUser($user); 
        return password_verify($user->getPassword(), $this->userData[self::$colPwd]);
    }

    private function verifyExpireDate($user) : bool {
        $this->userData = $this->storage->getAuthUser($user); 
        $currentDate = date("Y-m-d H:i:s", time());
        $expireDate = $this->userData[self::$colExpireDate];
        return $expireDate > $currentDate;
    }  

    private function verifyCookies(User $user) {
        // Check if cookie expiredate and pwd is valid.
        $expireDateCheck = $this->verifyExpireDate($user);
        $pwdTokenCheck = $this->verifyPwdToken($user);

        if (!$expireDateCheck || !$pwdTokenCheck ) {
            throw new \Model\WrongCookieInfoException();
        }
    }

    public function saveAuthCredentials($user) {
        $this->storage->saveAuthUser($user);
    }

}