<?php

namespace Model;

require_once('DBconn.php');

 class DbUserTable extends DBconn {
     private static $colPwd = 'password';

    public function fetchUser($uid) {
        $sql = "SELECT * FROM users WHERE BINARY username=?";
        $types = "s";
        $param = [$uid];
        return $this->getFromDB($sql, $types, $param);
    }

    public function saveUser($uid, $hashedPwd) {
        $sql = "INSERT INTO users (username, password) VALUES(?, ?)";
        $types = "ss";
        $params = [$uid, $hashedPwd];
        $this->saveToDB($sql, $types, $params);
    }

    public function verifyPassword ($uid, $pwd) : bool {
        $userData = $this->fetchUser($uid);
        return password_verify($pwd, $userData[self::$colPwd]);       
    }
 }