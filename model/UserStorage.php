<?php 

namespace Model;

require_once('Exceptions/LoginUserException.php');
require_once('DAL/DbUserTable.php');

class UserStorage {
    private static $sessionName = 'SessionName';
    private static $userAgent = 'UserAgent';
    private $loggedInUser;
    private $storage;

    public function __construct() {
        $this->storage = new \Model\DbUserTable();
    }

    public function validateCredentials($user) {
        if (!$this->storage->fetchUser($user)) {
            throw new WrongCredentialsException();
        }

        if (!$this->storage->verifyPassword($user)) {
            throw new WrongCredentialsException();
        }

        session_regenerate_id();
    }
  
    public function startNewSession(User $user) {  
        $_SESSION[self::$sessionName] = $user->getUsername();
        $_SESSION[self::$userAgent] =  $this->getBrowserName();
    }

    public function endSession() {
        unset($_SESSION[self::$sessionName]);
        unset($_SESSION[self::$userAgent]);
    }

    public static function isUserLoggedIn() : bool {
       return isset($_SESSION[self::$sessionName]);
    }

    private function getBrowserName() {
        return $_SERVER["HTTP_USER_AGENT"];
    }

    private function checkSession() {
        if (!$this->getBrowserName()) {
            return FALSE;
        }
        if(!isset($_SESSION[self::$userAgent])) {
            return FALSE;
        }
        return $this->getBrowserName() === $_SESSION[self::$userAgent];
    }
    
    //TODO FIX!!
    public function controllSession() {
    if (isset($_SESSION[self::$sessionId])){
        if(!$this->checkSession()) {
            return FALSE;
        } else {
            return TRUE;
        }

    }
}
}