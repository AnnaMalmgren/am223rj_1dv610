<?php

require_once('LoginUserException.php');

class LoginUser {
    private $username;
    private $password;
    private $hashedPassword;
    private $cookieExpiresIn; 

    public function __construct($username, $password) {

        if ($this->isFormValid($username, $password)) {
            $this->username = $username;
            $this->password = $password;
            $this->createNewSession();
        }

    }

    private function isFormValid($username, $password) : Bool {
        if (empty($username)) {
            throw new LoginUserException('Username is missing');
        } else if(empty($password)) {
            throw new LoginUserException('Password is missing');
        } else if (!$this->getUserFromDB($username, $password)) {
            throw new LoginUserException('Wrong name or password');
        } else if (!$this->verifyPassword($username, $password)){
            throw new LoginUserException('Wrong name or password');
        } else {
            return TRUE;
        }

    }

    private function createNewSession() {
        session_regenerate_id();
        $_SESSION['username'] = $this->username;
    }
    
    private function verifyPassword ($username, $password) : bool {
        $userData = $this->getUserFromDB($username, $password);
        return password_verify($password, $userData['password']);       
    }

    private function getUserFromDB($username, $password) {
        //require(__DIR__ . '/../dbproduction.php');
        require(__DIR__ . '/../dbsettings.php');

        $sql = "SELECT username, password FROM users WHERE BINARY username=?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            throw new Exception('Something went wrong.');
            exit();
        } 
        
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $userData = mysqli_fetch_assoc($result);
        return $userData;
    }

        public function createCookies($cookieUsername, $cookiePassword) {
        $this->cookieExpiresIn = time() + 3600 * 24;
        $bytesLength = 16;
        $randomPassword = bin2hex(random_bytes($bytesLength));
        $this->hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
        setcookie($cookiePassword, $randomPassword, $this->cookieExpiresIn, "/", "",  TRUE, TRUE);
        setcookie ($cookieUsername, $this->username, $this->cookieExpiresIn, "/", "", TRUE, TRUE);
    
    }

    public function saveAuthToDB() {
        //require(__DIR__ . '/../dbproduction.php');
        require(__DIR__ . '/../dbsettings.php');
        $expireDate = date("Y-m-d H:i:s", $this->cookieExpiresIn);
 
         $sql = "INSERT INTO auth_users (authUsername, passwordHash, expireDate) VALUES(?, ?, ?)";
         $stmt = mysqli_stmt_init($conn);
 
         if (!mysqli_stmt_prepare($stmt, $sql)) {
             return "Something went wrong (sql error)";
         } else {
             mysqli_stmt_bind_param($stmt, "sss", $this->username, $this->hashedPassword, $expireDate);
             mysqli_stmt_execute($stmt);
         }   
    }


}
