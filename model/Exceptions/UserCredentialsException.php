<?php
namespace Model;
class UserCredentialException extends \Exception{}

class UsernameMissingException extends LoginUserException {}

class PasswordMissingException extends LoginUserException {}