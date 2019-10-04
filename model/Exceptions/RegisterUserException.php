<?php

namespace Model;

class RegisterUserException extends \Exception {}

class ToShortUserNameException extends RegisterUserException {
    protected $message = 'Username has too few characters, at least 3 characters.';
}

class InvalidCharactersException extends RegisterUserException {
    protected $message = 'Username contains invalid characters';
}

class ToShortPasswordException extends RegisterUserException {
    protected $message = 'Password has too few characters, at least 6 characters.';
}

class UsernameExistsException extends RegisterUserException {
    protected $message = 'User exists, pick another username.';
}

class PasswordsDontMatchException extends RegisterUserException {
    protected $message = 'Passwords do not match.';
}
