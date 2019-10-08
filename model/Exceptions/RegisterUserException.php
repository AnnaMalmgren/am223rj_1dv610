<?php

namespace Model;

class RegisterUserException extends \Exception {}

class ToShortUserNameException extends RegisterUserException {}

class InvalidCharactersException extends RegisterUserException {}

class ToShortPasswordException extends RegisterUserException {}

class UsernameExistsException extends RegisterUserException {}

class PasswordsDontMatchException extends RegisterUserException {}
