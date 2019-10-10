<?php 

namespace Model; 

require_once('Exceptions/UserCredentialsException.php');

class UserCredentials {
    private $username;
    private $password;

    public function __construct($username, $password) {
        $this->setUsername($username);
        $this->setPassword($password);
    }

    public function setUsername(string $username) {
        if(empty($username)) {
            throw new UsernameMissingException();
        }

        $this->username = $username;
    }

    public function setPassword(string $password) {
        if(empty($password)) {
            throw new PasswordMissingException();
        }

        $this->password = $password;
    }

    public function getUsername() : string {
        return $this->username;
    }

    public function getPassword() : string {
        return $this->password;
    }
}