<?php

require_once('LoginUserException.php');

class UserStorage {
    public function __construct($username, $password) {
        $username = trim($username);
        $password = trim($password);

        if (strlen($username) === 0) {
            throw new LoginUserException('Username is missing');
        } else if(strlen($password) === 0) {
            throw new LoginUserException('Password is missing');
        }

        $this->isUserInfoCorrect($username, $password);
    }

    private function isUserInfoCorrect($username, $password) : bool {
        require(__DIR__ . '/../dbproduction.php');
        //require(__DIR__ . '/../dbsettings.php');

        $sql = "SELECT username, password FROM users WHERE username=? AND password=?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            throw new Exception('Something went wrong.');
        } else {
            mysqli_stmt_bind_param($stmt, 'ss', $username, $password);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $passwordCheck = password_verify($password, $row['password']);
                if (!$passwordCheck) {
                    throw new LoginUserException('Wrong name or password');
                } else if ($passwordCheck) {
                    return "Success";
                }

            } else {
                throw new LoginUserException('Wrong name or password');
            }
        }
    }
}
