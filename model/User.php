<?php
require_once(__DIR__ . '/../dbproduction.php');
class User {
    public function registerUser() {
        $sql = "INSERT INTO users (username, 'password')
        VALUES ('Lisa', 'password2')";

        if ($conn->query($sql) === TRUE) {
            return "User registrated";
        } else {
            return "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}