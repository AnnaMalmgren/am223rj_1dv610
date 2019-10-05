<?php
namespace Model;
class LoginUserException extends \Exception{}

class WrongCredentialsException extends LoginUserException {
  protected $message = "Wrong name or password";
}

class WrongCookieInfoException extends LoginUserException {
  protected $message = "Wrong information in cookies";
}


