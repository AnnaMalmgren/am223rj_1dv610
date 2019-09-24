<?php

require_once('RegisterUserException.php');

class RegisterUser {
    private $uid;
    private $hashedPwd;
    private $minUidLength = 3;
    private $minPwdLength = 6;


    public function __construct($username, $password, $passwordRepeat) {
        if ($this->isFormValid($username, $password, $passwordRepeat)) {
            $this->uid = $username;
            $this->hashedPwd = $this->hashPassword($password);
        }
    }

    public function getUsername() {
        return $this->uid;
    }

    private function isFormValid($uid, $pwd, $pwdRepeat) : bool {
        if (empty($uid) && empty($pwd)) {
            throw new RegisterUserException('Username has too few characters, at least 3 characters.<br>Password has too few characters, at least 6 characters.'); 
        } else if (strlen($uid) < $this->minUidLength) {
            throw new RegisterUserException('Username has too few characters, at least 3 characters.');
        } else if ($uid !== htmlentities($uid)) {
            throw new RegisterUserException('Username contains invalid characters.');
        } else if (strlen($pwd) < $this->minPwdLength) {
            throw new RegisterUserException('Password has too few characters, at least 6 characters.');
        } else if ($pwd !== $pwdRepeat) {
            throw new RegisterUserException('Passwords do not match.');
        } else if ($this->getUserFromDB($uid)) {
            throw new RegisterUserException('User exists, pick another username.');
        } else {
            return TRUE;
        }
    }

    private function hashPassword($pwd) : string {
        return password_hash($pwd, PASSWORD_DEFAULT); 
     }

    public function getUserFromDB($uid) {
        //require(__DIR__ . '/../dbproduction.php');
        require(__DIR__ . '/../dbsettings.php');

        $sql = "SELECT * FROM users WHERE BINARY username=?";

        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            throw new Exception('Something went wrong in getUser.');
            exit();
        } 
        
        mysqli_stmt_bind_param($stmt, 's', $uid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $userData = mysqli_fetch_assoc($result);
        return $userData;
    }

    public function saveUserToDB() {
        //require(__DIR__ . '/../dbproduction.php');
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