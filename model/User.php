<?php 

namespace Model; 

require_once('Exceptions/RegisterUserException.php');
require_once('DAL/DbUserTable.php');

class User {
    private $username;
    private $password;
    private $tempPassword;
    private $minUsernameLength = 3;
    private $minPassswordLength = 6;
    private $bytesLength = 12;

    public function __construct($username, $password) {
        $this->setUsername($username);
        $this->setPassword($password);
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

    private function setPassword($password) {
        if (strlen($password) < $this->minPassswordLength) {
            throw new ToShortPasswordException();
        }

        $this->password = $password;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getTempPassword() {
        return $this->tempPassword;
    }

     public function setTempPassword() {
        $this->tempPassword = bin2hex(random_bytes($this->bytesLength));
     }

}