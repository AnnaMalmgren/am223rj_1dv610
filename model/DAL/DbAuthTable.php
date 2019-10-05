<?php

namespace Model;

require_once('DBconn.php');

 class DbAuthTable extends DBconn {
    private static $colPwd = 'passwordHash';

    public function getAuthUser(User $user) {
        $sql = "SELECT * FROM auth_users WHERE BINARY authUsername=?";
        $types = "s";
        $param = [$user->getUsername()];
        return $this->getFromDB($sql, $types, $param);
    }

    public function saveAuthUser($user) {
        $cookieExpiresIn = time() + (7 * 24 * 60 * 60);
        $expireDate = date("Y-m-d H:i:s", $cookieExpiresIn);
        
        if ($this->getAuthUser($user)) {
            //if user already exist in DB update auth info for user.
            return $this->updateAuthUser($user);
        }

        $sql = "INSERT INTO auth_users (authUsername, passwordHash, expireDate) VALUES(?, ?, ?)";
        $types = "sss";
        $params = [$user->getUsername(), $user->getHashedTempPassword(), $expireDate];
        $this->saveToDB($sql, $types, $params);
    }

    private function updateAuthUser($user) {
        $cookieExpiresIn = time() + (7 * 24 * 60 * 60);
        $expireDate = date("Y-m-d H:i:s", $cookieExpiresIn);

        $sql = "UPDATE auth_users SET expireDate = ?, passwordHash = ? WHERE BINARY authUsername = ?";
        $types = "sss";
        $params = [$expireDate, $user->getHashedTempPassword(), $user->getUsername()];
        $this->updateDB($sql, $types, $params); 
    }

 }