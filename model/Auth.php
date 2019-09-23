<?php

class Auth {

    private $cookiePassword;
    private $authUser;
    private $cookieExpiresIn;
    private $hashedPassword;

    public function __construct($cookieName, $cookiePassword) {
        $this->authUser = $cookieName;
        $this->cookiePassword = $cookiePassword;
    }

    public function authUser() : Bool {
        $user = $this->getAuthUserFromDB();
        $currentDate = date("Y-m-d H:i:s", time());
 
       if($user['expireDate'] > $currentDate && $this->verifyPassword($user)) {
           session_regenerate_id();
           $_SESSION['username'] = $this->authUser;
           return TRUE;
       } else {
           return FALSE;
       }
    }

    private function verifyPassword($user) : bool {
        return password_verify($user['passwordHash'], $this->cookiePassword);
    }

    private function getAuthUserFromDB() {
        require(__DIR__ . '/../dbproduction.php');
        //require(__DIR__ . '/../dbsettings.php');

        $sql = "SELECT * FROM auth_users WHERE BINARY authUsername=?";

        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            throw new Exception('Something went wrong in getUser.');
            exit();
        } 
        
        mysqli_stmt_bind_param($stmt, 's', $this->authUser);
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
             mysqli_stmt_bind_param($stmt, "sss", $this->authUser, $this->hashedPassword, $expireDate);
             mysqli_stmt_execute($stmt);
         }   
    }

    private function updateAuthUser() {
        require(__DIR__ . '/../dbproduction.php');
        //require(__DIR__ . '/../dbsettings.php');

        $sql = "UPDATE auth_users SET expireDate = ? WHERE authUsername = ?";
         $stmt = mysqli_stmt_init($conn);

         if (!mysqli_stmt_prepare($stmt, $sql)) {
             return "Something went wrong (sql error)";
         } else {
             mysqli_stmt_bind_param($stmt, "ss", $expireDate, $this->authUser);
             mysqli_stmt_execute($stmt);
         }   

    }

    private function isAuthInDB() : bool {   
        if ($this->getAuthUserFromDB()) {
           return TRUE;
        } else {
           return FALSE;
        }
    }


    public function createCookies($cookieName, $cookiePassword) {
        $this->cookieExpiresIn = time() + 3600 * 24;
        $bytesLength = 16;
        $randomPassword = bin2hex(random_bytes($bytesLength));
        $this->hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);

        setcookie($cookieName, $this->authUser, $this->cookieExpiresIn, "/", "", TRUE, TRUE);
        setcookie($cookiePassword, $randomPassword, $this->cookieExpiresIn, "/", "",  TRUE, TRUE);
    }


    
}