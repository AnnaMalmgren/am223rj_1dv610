<?php
namespace Model;

require_once('Exceptions/RegisterUserException.php');
require_once('DAL/DbUserTable.php');

class RegisterUser {
    private $storage;
    private $registeredUser;

    public function __construct(User $user) {
        $this->storage = new DbUserTable();
        $this->setRegisteredUser($user);
    }

    private function setRegisteredUser($user) {
        if ($this->storage->fetchUser($user)) {
            throw new UsernameExistsException();
        }

        $this->registeredUser = $user;
        $this->storage->saveUser($this->registeredUser);
    }
}
