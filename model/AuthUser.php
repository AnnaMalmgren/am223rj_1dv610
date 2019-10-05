<?php

namespace Model;

require_once('LoginUser.php');
require_once('DAL/DbAuthTable.php');

class AuthUser extends LoginUser {
    private $authenticatedUser;
    private $storage;
    private static $colPwd = 'passwordHash';
    private static $colExpireDate = 'expireDate';

    public function __construct(User $user) {
        $this->storage = new \Model\DbAuthTable();
        $this->setUser($user);
    }

    private function setUser(User $user) {
       $this->verifyCookies($user);
       $this->authenticatedUser = $user;
    }

    private function verifyPwdToken($user) : bool {
        $userData = $this->storage->getAuthUser($user);  
        return password_verify($user->tempPassword, $userData[self::$colPwd]);
    }

    private function verifyExpireDate($user) : bool {
        $userData = $this->storage->getAuthUser($user);
        $currentDate = date("Y-m-d H:i:s", time());
        $expireDate = $userData[self::$colExpireDate];
        return $expireDate > $currentDate;
    }  

    private function verifyCookies($user) {
        // Check if cookie expiredate and pwd is valid.
        $expireDateCheck = $this->verifyExpireDate($user);
        $pwdTokenCheck = $this->verifyPwdToken($user);

        if (!$expireDateCheck || !$pwdTokenCheck ) {
            throw new \Model\LoginUserException('Wrong information in cookies');
        }
    }
    
   
}