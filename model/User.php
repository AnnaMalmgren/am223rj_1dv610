<?php 

namespace Model; 

require_once('Exceptions/RegisterUserException.php');

class User {
    private $username;
    private $password;
    private $tempPassword;
    private $minUsernameLength = 3;
    private $minPassswordLength = 6;

    public function __construct($username, $password) {
            $this->setUsername($username);
            $this->setPassword($password);
    }

    public function getUsername() {
        return $this->username;
    }

    private function setUsername($username) {
        if (strlen($username) < $this->minUsernameLength) {
            throw new ToShortUserNameException();
        }
        if ($username !== htmlentities($username)) {
            throw new InvalidCharactersException();
        }

        $this->username = $username;
    }

    public function getPassword() {
        return $this->password;
    }

    private function setPassword($password) {
        if (strlen($password) < $this->minPassswordLength) {
            throw new ToShortPasswordException();
        }

        $this->password = $password;
    }

    public function getHashedPassword() {
        return $this->hashPassword($this->password);
    }

    public function setTempPassword($tempPassword) {
        $this->setTempPassword = $tempPassword;
    }

    private function hashPassword($password) : string {
        return password_hash($password, PASSWORD_DEFAULT); 
     }
}