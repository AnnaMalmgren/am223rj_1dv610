<?php

require_once('LoginUserException.php');

class UserStorage {
    private $username;
    private $password;

    public function __construct($username, $password) {
        $username = trim($username);
        $password = trim($password);

        if (empty($username)) {
            throw new LoginUserException('Username is missing');
        } else if(empty($password)) {
            throw new LoginUserException('Password is missing');
        } else if (!$this->getUserFromDB($username, $password)) {
            throw new LoginUserException('Wrong name or password');
        } else if (!$this->verifyPassword($username, $password)){
            throw new LoginUserException('Wrong name or password');
        } else {
            $this->username = $username;
            $this->password = $password;
            $this->createNewSession();
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

}
