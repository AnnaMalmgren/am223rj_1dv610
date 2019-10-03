<?php

namespace Model;

require_once('DBconn.php');

 class DbAuthTable extends DBconn {

    private static $colPwd = 'passwordHash';

    private static $colExpireDate = 'expireDate';

    public function getAuthUser($uid) {
        $sql = "SELECT * FROM auth_users WHERE BINARY authUsername=?";
        $types = "s";
        $param = [$uid];
        return $this->getFromDB($sql, $types, $param);
    }

    public function saveAuthUser($uid, $pwd) {
        $cookieExpiresIn = time() + (7 * 24 * 60 * 60);
        $expireDate = date("Y-m-d H:i:s", $cookieExpiresIn);
        
        if ($this->getAuthUser($uid)) {
            //if user already exist in DB update auth info for user.
            return $this->updateAuthUser($uid, $pwd);
        }

        $sql = "INSERT INTO auth_users (authUsername, passwordHash, expireDate) VALUES(?, ?, ?)";
        $types = "sss";
        $params = [$uid, $pwd, $expireDate];
        $this->saveToDB($sql, $types, $params);
    }

    private function updateAuthUser($uid, $pwd) {
        $cookieExpiresIn = time() + (7 * 24 * 60 * 60);
        $expireDate = date("Y-m-d H:i:s", $cookieExpiresIn);

        $sql = "UPDATE auth_users SET expireDate = ?, passwordHash = ? WHERE BINARY authUsername = ?";
        $types = "sss";
        $params = [$expireDate, $pwd, $uid];
        $this->updateDB($sql, $types, $params); 
    }

    public function verifyPwdToken($uid, $pwd) : bool {
        $userData = $this->getAuthUser($uid);
        return password_verify($pwd, $userData[self::$colPwd]);
    }

    public function verifyExpireDate($uid) : bool {
        $userData = $this->getAuthUser($uid);
        $currentDate = date("Y-m-d H:i:s", time());
        $expireDate = $userData[self::$colExpireDate];
        // check that cookie hasn't expired.
        return $expireDate > $currentDate;
    }  

 }