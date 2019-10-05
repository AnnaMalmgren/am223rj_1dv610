<?php 

namespace Model; 

require_once('Exceptions/RegisterUserException.php');

class User {
    private $username;
    private $password;
    private $minUsernameLength = 3;
    private $minPassswordLength = 6;
    public $tempPassword;

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
        return password_hash($this->password, PASSWORD_DEFAULT); 
        return $this->hashPassword();
    }

    public function getHashedTempPassword() {
        return password_hash($this->tempPassword, PASSWORD_DEFAULT); 
    }


     public function setTempPassword() {
        $this->tempPassword = bin2hex(random_bytes($this->bytesLength));
     }
}