<?php

namespace Model;

require_once('DBconn.php');

 class DbUserTable extends DBconn {
     private static $colPwd = 'password';

    public function fetchUser(User $user) {
        $sql = "SELECT * FROM users WHERE BINARY username=?";
        $types = "s";
        $param = [$user->getUsername()];
        return $this->getFromDB($sql, $types, $param);
    }

    public function saveUser(User $user) {
        $sql = "INSERT INTO users (username, password) VALUES(?, ?)";
        $types = "ss";
        $params = [$user->getUsername(), $this->hashedPassword($user)];
        $this->saveToDB($sql, $types, $params);
    }

    public function verifyPassword (User $user) : bool {
        $userData = $this->fetchUser($user);
        return password_verify($user->getPassword(), $userData[self::$colPwd]);       
    }

    private function hashedPassword($user) {
        return password_hash($user->getPassword(), PASSWORD_DEFAULT); 
    }
 }