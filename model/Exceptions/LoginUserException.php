<?php
namespace Model;
class LoginUserException extends \Exception{}

class WrongCredentialsException extends LoginUserException {
  protected $message = "Wrong name or password";
}


