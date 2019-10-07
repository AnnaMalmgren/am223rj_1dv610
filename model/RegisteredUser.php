<?php 

namespace Model; 

require_once('Exceptions/RegisterUserException.php');
require_once('DAL/DbUserTable.php');

class RegisteredUser {
    private $registeredUser;
    private $storage;

    public function __construct(User $user) {
            $this->storage = new DbUserTable();
            $this->setRegisteredUser($user);
            $this->storage->saveUser($this->registeredUser);
    }

     public function setRegisteredUser($user) {
        if ($this->storage->fetchUser($user)) {
            throw new UsernameExistsException();
        }
        $this->registeredUser = $user;
    }
}
