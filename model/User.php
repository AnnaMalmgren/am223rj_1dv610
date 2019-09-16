<?php

require_once('RegisterUserException.php');

class User {
    private static $minUidLenght = 3;
    private static $minPwdLength = 6;
    private $username = null;
    private $password = null;

    public function __construct($username, $password, $repeatedPassword) {

        if (strlen($username) === 0 && strlen($password) === 0) {
            throw new RegisterUserException('Username has too few characters, at least 3 characters.<br>Password has too few characters, at least 6 characters.'); 
        } else if (strlen($username) < self::$minUidLenght) {
            throw new RegisterUserException('Username has too few characters, at least 3 characters.');
        } else if ($this->doesUserExits($username)) {
            throw new RegisterUserException('User exists, pick another username.');
        } else if (strlen($password) < self::$minPwdLength) {
            throw new RegisterUserException('Password has too few characters, at least 6 characters.');
        } else if ($password !== $repeatedPassword) {
            throw new RegisterUserException('Passwords do not match.');
        } else {
            $this->username = $username;
            $this->password = $this->hashPassword($password);
        }
    }
    
    /**
     * Gets the username.
     * @return string
     */
    public function getUsername () : string {
        return $this->username;
    }
    
    /**
     * Gets the password
     * @return string
     */
    public function getPassword() : string {
        return $this->password;
    }
    
    /**
     * Hashes the password
     * @return string
     */
    private function hashPassword($password) : string {
        return password_hash($password, PASSWORD_DEFAULT); 
     }

    /**
     * Check entered username already exits in the database.
     * @return int the number of rows with the entered username.
     */
    private function doesUserExits($username) : bool {
        require(__DIR__ . '/../dbsettings.php');

        $sql = "SELECT username FROM users WHERE username=?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            throw new Exception('Something went wrong.');
        } else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            return $numOfUsers = mysqli_stmt_num_rows($stmt);
        }
    }

    /**
     * Saves a user to the DB
     * @return void
     */
    public function saveUserToDB() {
        require(__DIR__ . '/../dbsettings.php');
 
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