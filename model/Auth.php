<?php

class Auth {

    public function verifyUser($uid, $pwd) {
        $user = $this->getAuthUserFromDB($uid);
        $currentDate = date("Y-m-d H:i:s", time());
        $expireDate = $user['expireDate'];

        //IMPLEMENT PASSWORD VERIFY!!  
        
        if($expireDate > $currentDate && $passwordCheck) {
            $_SESSION['username'] = $uid;
        }
    }

    private function getAuthUserFromDB($uid) {
        //require(__DIR__ . '/../dbproduction.php');
        require(__DIR__ . '/../dbsettings.php');

        $sql = "SELECT * FROM auth_users WHERE BINARY authUsername=?";

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

    public function saveAuthToDB($uid, $pwd) {
        //require(__DIR__ . '/../dbproduction.php');
        require(__DIR__ . '/../dbsettings.php');

        $cookieExpiresIn = time() + (7 * 24 * 60 * 60);
        $expireDate = date("Y-m-d H:i:s", $cookieExpiresIn);

        if ($this->isAuthInDB($uid)) {
            return $this->updateAuthUser($uid);
        }

         $sql = "INSERT INTO auth_users (authUsername, passwordHash, expireDate) VALUES(?, ?, ?)";
         $stmt = mysqli_stmt_init($conn);

         if (!mysqli_stmt_prepare($stmt, $sql)) {
             return "Something went wrong (sql error)";
         } else {
             mysqli_stmt_bind_param($stmt, "sss", $uid, $pwd, $expireDate);
             mysqli_stmt_execute($stmt);
         }   
    }

    private function updateAuthUser($uid) {
        //require(__DIR__ . '/../dbproduction.php');
        require(__DIR__ . '/../dbsettings.php');

        $cookieExpiresIn = time() + (7 * 24 * 60 * 60);

        $expireDate = date("Y-m-d H:i:s", $cookieExpiresIn);

        $sql = "UPDATE auth_users SET expireDate = ? WHERE BINARY authUsername = ?";
         $stmt = mysqli_stmt_init($conn);

         if (!mysqli_stmt_prepare($stmt, $sql)) {
             return "Something went wrong (sql error)";
         } else {
             mysqli_stmt_bind_param($stmt, "ss", $expireDate, $uid);
             mysqli_stmt_execute($stmt);
         }   

    }

    private function isAuthInDB($uid) : bool {   
        if ($this->getAuthUserFromDB($uid)) {
           return TRUE;
        } else {
           return FALSE;
        }
    }   
}