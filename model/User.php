<?php

require_once('RegisterUserException.php');

class User {
    private $username = null;
    private $password = null;

    public function __construct($username, $password) {
            $this->username = $username;
            $this->password = $this->hashPassword($password);
    }

    private function isFormValid($username, $password, $repeatedPassword) : bool {
        if (empty($username) && empty($password)) {
            throw new RegisterUserException('Username has too few characters, at least 3 characters.<br>Password has too few characters, at least 6 characters.'); 
        } else if (strlen($username) < self::$minUidLenght) {
            throw new RegisterUserException('Username has too few characters, at least 3 characters.');
        } else if ($username !== htmlentities($username)) {
            throw new RegisterUserException('Username contains invalid characters.');
        } else if (strlen($password) < self::$minPwdLength) {
            throw new RegisterUserException('Password has too few characters, at least 6 characters.');
        } else if ($password !== $repeatedPassword) {
            throw new RegisterUserException('Passwords do not match.');
        } else if ($this->getUserFromDB($username)) {
            throw new RegisterUserException('User exists, pick another username.');
        } else {
            return TRUE;
        }
    }

    private function createNewSession() {
        session_regenerate_id();
        $_SESSION['username'] = $this->username;
    }
    
    private function hashPassword($password) : string {
        return password_hash($password, PASSWORD_DEFAULT); 
     }

     public function verifyPassword ($username, $password) : bool {
        $sql = "SELECT * FROM users WHERE BINARY username=?";
        $userData = $this->getUserFromDB($username, $sql);

        return password_verify($password, $userData['password']);       
    }


     public function getUserFromDB($username) {
        require(__DIR__ . '/../dbproduction.php');
        //require(__DIR__ . '/../dbsettings.php');

        $sql = "SELECT * FROM users WHERE BINARY username=?";

        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            throw new Exception('Something went wrong in getUser.');
            exit();
        } 
        
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $userData = mysqli_fetch_assoc($result);
        return $userData;
    }


    public function saveUserToDB() {
        require(__DIR__ . '/../dbproduction.php');
        //require(__DIR__ . '/../dbsettings.php');
 
         $sql = "INSERT INTO users (username, password) VALUES(?, ?)";
         $stmt = mysqli_stmt_init($conn);
 
         if (!mysqli_stmt_prepare($stmt, $sql)) {
             return "Something went wrong (sql error)";
         } else {
             mysqli_stmt_bind_param($stmt, "ss", $this->username, $this->password);
             mysqli_stmt_execute($stmt);
         }   
    }

}