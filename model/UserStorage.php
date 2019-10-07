<?php 

namespace Model;

require_once('Exceptions/LoginUserException.php');
require_once('Authentication.php');
require_once('DAL/DbUserTable.php');

class UserStorage {
    private static $sessionName = 'SessionName';
    private static $userAgent = 'UserAgent';
    private $auth;
    private $loggedInUser;

    public function __construct() {
        $this->auth = new Authentication();
    }

    public function loginUserByRequest(User $user) {
        $this->auth->validateRequestCredentials($user);
        $this->loggedInUser = $user; 
        $this->startNewSession($this->loggedInUser);
    }

    private function startNewSession(User $user) { 
        session_regenerate_id(); 
        $_SESSION[self::$sessionName] = $user->getUsername();
        $_SESSION[self::$userAgent] =  $this->getBrowserName();
    }

    private function getBrowserName() {
        return $_SERVER["HTTP_USER_AGENT"];
    }

    public function keepMeLoggedIn() {
        $this->loggedInUser->setTempPassword();
        $this->auth->saveAuthCredentials($this->loggedInUser);
    }

    public function loginUserByAuth(User $user) {
        $this->auth->validateAuthCredentials($user);
        $this->loggedInUser = $user;
        $this->startNewSession($this->loggedInUser);
    }


    public function getLoggedInUser() {
        return $this->loggedInUser;
    }

    public function endSession() {
        unset($_SESSION[self::$sessionName]);
        unset($_SESSION[self::$userAgent]);
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

    private function checkSession() {
        if (!$this->getBrowserName()) {
            return FALSE;
        }
        if(!isset($_SESSION[self::$userAgent])) {
            return FALSE;
        }
        return $this->getBrowserName() === $_SESSION[self::$userAgent];
    }

}