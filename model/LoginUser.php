<?php 

require_once('LoginUserException.php');

class LoginUser {
    private $uid;

    public function __construct($username, $password) {
        if ($this->isFormValid($username, $password)) {
            $this->uid = $username;
        }
    }

    public function getUsername() {
        return $this->uid;
    }

    private function isFormValid($uid, $pwd) {
        if (empty($uid)) {
            throw new LoginUserException('Username is missing');
        } else if(empty($pwd)) {
            throw new LoginUserException('Password is missing');
        } else if (!$this->getUserFromDB($uid)) {
            throw new LoginUserException('Wrong name or password');
        } else if (!$this->verifyPassword($uid, $pwd)){
            throw new LoginUserException('Wrong name or password');
        } else {
            return TRUE;
        }
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

    private function startNewSession($uid) {
        session_regenerate_id();
        $_SESSION['username'] = $uid;
    }


    public function verifyPassword ($uid, $pwd) : bool {
        $sql = "SELECT * FROM users WHERE BINARY username=?";
        $userData = $this->getUserFromDB($uid, $sql);

        return password_verify($pwd, $userData['password']);       
    }

}