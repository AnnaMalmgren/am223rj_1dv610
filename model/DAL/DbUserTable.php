<?php

namespace Model;

require_once('DBconn.php');

 class DbUserTable extends DBconn {

    public function getUser(UserCredentials $credentials) {
        $sql = "SELECT * FROM users WHERE BINARY username=?";
        $types = "s";
        $param = [$credentials->getUsername()];
        return $this->getFromDB($sql, $types, $param);
    }

    public function saveUser(User $user) {
        $sql = "INSERT INTO users (username, password) VALUES(?, ?)";
        $types = "ss";
        $params = [$user->getUsername(), $this->hashedPassword($user)];
        $this->saveToDB($sql, $types, $params);
    }

    private function hashedPassword(User $user) {
        return password_hash($user->getPassword(), PASSWORD_DEFAULT); 
    }
 }