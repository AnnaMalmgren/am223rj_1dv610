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

    public function saveAuthToDB($uid, $pwd) {
        $cookieExpiresIn = time() + (7 * 24 * 60 * 60);
        $expireDate = date("Y-m-d H:i:s", $cookieExpiresIn);
        
        if ($this->isAuthInDB($uid)) {
            //if user already exist in DB update auth info for user.
            return $this->updateAuthUser($uid, $pwd);
        }

         $sql = "INSERT INTO auth_users (authUsername, passwordHash, expireDate) VALUES(?, ?, ?)";
         $stmt = mysqli_stmt_init($this->conn);

         if (!mysqli_stmt_prepare($stmt, $sql)) {
             return "Something went wrong (sql error)";
         } else {
             mysqli_stmt_bind_param($stmt, "sss", $uid, $pwd, $expireDate);
             mysqli_stmt_execute($stmt);
         }   
    }

    private function updateAuthUser($uid, $pwd) {
        $cookieExpiresIn = time() + (7 * 24 * 60 * 60);
        $expireDate = date("Y-m-d H:i:s", $cookieExpiresIn);

        $sql = "UPDATE auth_users SET expireDate = ?, passwordHash = ? WHERE BINARY authUsername = ?";
        $stmt = mysqli_stmt_init($this->conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return "Something went wrong";
        }

        mysqli_stmt_bind_param($stmt, "sss", $expireDate, $pwd, $uid);
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