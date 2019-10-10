<?php

namespace Model;

require_once('DBconn.php');

 class DbAuthTable extends DBconn {
    private static $colPwd = 'passwordHash';

    public function saveAuthUser(User $user) {
  
        if ($this->getAuthUser($user)) {
            //if user already exist in DB update auth info for user.
            return $this->updateAuthUser($user);
        }

        $sql = "INSERT INTO auth_users (authUsername, passwordHash, expireDate) VALUES(?, ?, ?)";
        $types = "sss";
        $params = [$user->getUsername(), $this->hashPassword($user), $this->setExpireDate()];
        $this->saveToDB($sql, $types, $params);
    }

    public function getAuthUser($user) {
        $sql = "SELECT * FROM auth_users WHERE BINARY authUsername=?";
        $types = "s";
        $param = [$user->getUsername()];
        return $this->getFromDB($sql, $types, $param);
    }


    private function updateAuthUser(User $user) {
        $sql = "UPDATE auth_users SET expireDate = ?, passwordHash = ? WHERE BINARY authUsername = ?";
        $types = "sss";
        $params = [$this->setExpireDate(), $this->hashPassword($user), $user->getUsername()];
        $this->updateDB($sql, $types, $params); 
    }

    private function hashPassword(User $user) {
        $pwd = $user->getTempPassword();
        return password_hash($pwd, PASSWORD_DEFAULT);
    }

    private function setExpireDate() {
        $cookieExpiresIn = time() + (7 * 24 * 60 * 60);
        return date("Y-m-d H:i:s", $cookieExpiresIn);
    }

 }