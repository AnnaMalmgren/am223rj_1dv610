<?php
namespace Model;

require_once('RegisterUserException.php');
require_once('DbUserTable.php');

class RegisterUser {
    private $uid;
    private $hashedPwd;
    private $sto;
    private $minUidLength = 3;
    private $minPwdLength = 6;



    public function __construct($username, $password, $passwordRepeat) {
        $this->storage = new DbUserTable();

        if ($this->isUserValid($username, $password, $passwordRepeat)) {
            $this->uid = $username;
            $this->hashedPwd = $this->hashPassword($password);
        }
    }

    public function getUsername() {
        return $this->uid;
    }

    private function isUserValid($uid, $pwd, $pwdRepeat) : bool {
        if (empty($uid) && empty($pwd)) {
            throw new RegisterUserException('Username has too few characters, at least 3 characters.<br>Password has too few characters, at least 6 characters.'); 
        } else if (strlen($uid) < $this->minUidLength) {
            throw new RegisterUserException('Username has too few characters, at least 3 characters.');
        } else if ($uid !== htmlentities($uid)) {
            throw new RegisterUserException('Username contains invalid characters.');
        } else if (strlen($pwd) < $this->minPwdLength) {
            throw new RegisterUserException('Password has too few characters, at least 6 characters.');
        } else if ($pwd !== $pwdRepeat) {
            throw new RegisterUserException('Passwords do not match.');
        } else if ($this->storage->fetchUser($uid)) {
            throw new RegisterUserException('User exists, pick another username.');
        } else {
            return TRUE;
        }
    }
    
    public function registerNewUser()
    {
        $this->storage->saveUser($this->uid, $this->hashedPwd);
    }

    private function hashPassword($pwd) : string {
        return password_hash($pwd, PASSWORD_DEFAULT); 
     }
}