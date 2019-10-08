<?php
namespace Model;
class LoginUserException extends \Exception{}

class WrongCredentialsException extends LoginUserException {}

class WrongCookieInfoException extends LoginUserException {}


