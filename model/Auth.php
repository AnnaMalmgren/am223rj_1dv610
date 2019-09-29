<?php

 namespace Model;

 class Auth {
    private $conn;

    public function __construct()
    {
        require(__DIR__ . '/../dbproduction.php');
        //require(__DIR__ . '/../dbsettings.php');
        $this->conn = $conn;
    }

    public function verifyPwdToken($uid, $pwd) : bool {
        $userData = $this->getAuthUserFromDB($uid);
        return password_verify($pwd, $userData['passwordHash']);
    }

    public function verifyExpireDate($uid) : bool {
        $userData = $this->getAuthUserFromDB($uid);
        $currentDate = date("Y-m-d H:i:s", time());
        $expireDate = $userData['expireDate'];
        // check that cookie hasn't expired.
        return $expireDate > $currentDate;
    }

    public function verifyUserAgent($uid, $serverAgent) : bool {
        $userData = $this->getAuthUserFromDB($uid);
        return $userData['user_agent'] === $serverAgent;
    }

    private function getAuthUserFromDB($uid) {
        $sql = "SELECT * FROM auth_users WHERE BINARY authUsername=?";

        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            throw new Exception('Something went wrong');
            exit();
        } 
        
        mysqli_stmt_bind_param($stmt, 's', $uid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $userData = mysqli_fetch_assoc($result);
        return $userData;
    }

    public function saveAuthToDB($uid, $pwd, $userAgent) {
        $cookieExpiresIn = time() + (7 * 24 * 60 * 60);
        $expireDate = date("Y-m-d H:i:s", $cookieExpiresIn);
        
        if ($this->isAuthInDB($uid)) {
            //if user already exist in DB update auth info for user.
            return $this->updateAuthUser($uid, $pwd, $userAgent);
        }

         $sql = "INSERT INTO auth_users (authUsername, passwordHash, expireDate, user_agent) VALUES(?, ?, ?, ?)";
         $stmt = mysqli_stmt_init($this->conn);

         if (!mysqli_stmt_prepare($stmt, $sql)) {
             return "Something went wrong (sql error)";
         } else {
             mysqli_stmt_bind_param($stmt, "ssss", $uid, $pwd, $expireDate, $userAgent);
             mysqli_stmt_execute($stmt);
         }   
    }

    private function updateAuthUser($uid, $pwd, $userAgent) {
        $cookieExpiresIn = time() + (7 * 24 * 60 * 60);
        $expireDate = date("Y-m-d H:i:s", $cookieExpiresIn);

        $sql = "UPDATE auth_users SET expireDate = ?, passwordHash = ?, user_agent = ? WHERE BINARY authUsername = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return "Something went wrong";
        }

        mysqli_stmt_bind_param($stmt, "ssss", $expireDate, $pwd, $userAgent, $uid);
        mysqli_stmt_execute($stmt);  
    }

    private function isAuthInDB($uid) : bool {
        // check if user is authenticated
        if ($this->getAuthUserFromDB($uid)) {
           return TRUE;
        } else {
           return FALSE;
        }
    }   
}