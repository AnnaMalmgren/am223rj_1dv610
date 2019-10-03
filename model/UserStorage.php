<?php 

namespace Model;

require_once('LoginUserException.php');
require_once('DbUserTable.php');

class UserStorage {
    private static $sessionName = 'SessionName';
    private static $userAgent = 'UserAgent';
    private $uid;
    private $storage;

    public function __construct($uid, $pwd) {
        $this->storage = new \Model\DbUserTable();
        $this->setUsername($uid);
        $this->setPassword($uid, $pwd);
    }

    public function getUsername() {
        return $this->uid;
    }

    public function setUsername($uid) {
        if (!$this->storage->fetchUser($uid)) {
            throw new LoginUserException('Wrong name or password');
        }

        $this->uid = $uid;
    }

    public function setPassword($uid, $pwd) {
       if (!$this->storage->verifyPassword($uid, $pwd)) {
            throw new LoginUserException('Wrong name or password');
        }
        $this->pwd = $pwd;
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
        $_SESSION[self::$sessionName] = $this->uid;
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