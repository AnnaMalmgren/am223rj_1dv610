<?php
require_once('dbproduction.php');
class User {
    public function registerUser($username, $password) {
        $sql = "INSERT INTO users (username, 'password')
        VALUES ($username, '$password')";

        if ($conn->query($sql) === TRUE) {
            return "User registrated";
        } else {
            return "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}