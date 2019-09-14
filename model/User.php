<?php
require_once(__DIR__ . '/../dbproduction.php');
class User {
    public function registerUser($username, $password) {
        $sql = "INSERT INTO users (username, 'password')
        VALUES ($username, '$passwors')";

        if ($conn->query($sql) === TRUE) {
            return "User registrated";
        } else {
            return "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}