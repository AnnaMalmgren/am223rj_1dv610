<?php 

namespace Model;

require_once('Exceptions/LoginUserException.php');
require_once('DAL/DbUserTable.php');

class UserStorage {
    private static $sessionName = 'SessionName';
    private static $userAgent = 'UserAgent';
    private $loggedInUser;
    private $storage;

    public function __construct(User $user) {
        $this->storage = new \Model\DbUserTable();
        $this->setUser($user);
    }

    private function setUser($user) {
        if (!$this->storage->fetchUser($user)) {
            throw new WrongCredentialsException();
        }

        if (!$this->storage->verifyPassword($user)) {
            throw new WrongCredentialsException();
        }

        $this->loggedInUser = $user;
    }
    
    private function checkSession() {
        if (!isset($_SERVER["HTTP_USER_AGENT"])) {
            return FALSE;
        }
        if(!isset($_SESSION[self::$userAgent])) {
            return FALSE;
        }
        return $_SERVER["HTTP_USER_AGENT"] === $_SESSION[self::$userAgent];
    }

   
    public function startNewSession() {
        session_regenerate_id();
        $_SESSION[self::$sessionName] = $this->loggedInUser.getUsername();
        $_SESSION[self::$userAgent] = $_SERVER["HTTP_USER_AGENT"];
    }

    public static function isUserLoggedIn() : bool {
       return isset($_SESSION[self::$sessionName]);
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