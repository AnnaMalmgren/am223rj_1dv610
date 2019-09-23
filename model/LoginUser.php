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
        $sql = "SELECT * FROM users WHERE BINARY username=?";

        if (empty($username)) {
            throw new LoginUserException('Username is missing');
        } else if(empty($password)) {
            throw new LoginUserException('Password is missing');
        } else if (!$this->getUserFromDB($username, $sql)) {
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
        $sql = "SELECT * FROM users WHERE BINARY username=?";
        $userData = $this->getUserFromDB($username, $sql);

        return password_verify($password, $userData['password']);       
    }

    private function getUserFromDB($username, $sql) {
        require(__DIR__ . '/../dbproduction.php');
        //require(__DIR__ . '/../dbsettings.php');

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

    public function saveAuthToDB() {
        require(__DIR__ . '/../dbproduction.php');
        //require(__DIR__ . '/../dbsettings.php');
        $expireDate = date("Y-m-d H:i:s", $this->cookieExpiresIn);

        if ($this->isAuthInDB()) {
            return $this->updateAuthUser();
        }

         $sql = "INSERT INTO auth_users (authUsername, passwordHash, expireDate) VALUES(?, ?, ?)";
         $stmt = mysqli_stmt_init($conn);

         if (!mysqli_stmt_prepare($stmt, $sql)) {
             return "Something went wrong (sql error)";
         } else {
             mysqli_stmt_bind_param($stmt, "sss", $this->username, $this->hashedPassword, $expireDate);
             mysqli_stmt_execute($stmt);
         }   
    }

    private function isAuthInDB() : bool {
        $sql = "SELECT * FROM auth_users WHERE BINARY authUsername=?";
        
        if ($this->getUserFromDB($this->username, $sql)) {
           return TRUE;
        } else {
           return FALSE;
        }
    }

    public function getHashedPassword () {
        return $this->hashedPassword;
    }

    public function getExpireDate() {
        $sql = "SELECT * FROM auth_users WHERE BINARY authUsername=?";
        $currentDate = date("Y-m-d H:i:s", time());
        $authUser = $this->getUserFromDB($this->username, $sql);

        return $authUser['expireDate'];
    }


    public function createCookies($cookieName, $cookiePassword) {
        $this->cookieExpiresIn = time() + 3600 * 24;
        $bytesLength = 16;
        $randomPassword = bin2hex(random_bytes($bytesLength));
        $this->hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);

        setcookie($cookieName, $this->username, $this->cookieExpiresIn, "/", "", TRUE, TRUE);
        setcookie($cookiePassword, $randomPassword, $this->cookieExpiresIn, "/", "",  TRUE, TRUE);
    }

    public function updateAuthUser() {
        require(__DIR__ . '/../dbproduction.php');
        //require(__DIR__ . '/../dbsettings.php');
        $expireDate = date("Y-m-d H:i:s", $this->cookieExpiresIn);

         $sql = "UPDATE auth_users SET expireDate = ? WHERE authUsername = ?";
         $stmt = mysqli_stmt_init($conn);
 
         if (!mysqli_stmt_prepare($stmt, $sql)) {
             return "Something went wrong (sql error)";
         } else {
             mysqli_stmt_bind_param($stmt, "ss", $expireDate, $this->username);
             mysqli_stmt_execute($stmt);
         }   

    }

}
