<?php 

namespace Model;

require_once('Exceptions/LoginUserException.php');
require_once('Authentication.php');

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
    }

    public function setUserSession() {
        session_regenerate_id(); 
        $_SESSION[self::$sessionName] = $this->loggedInUser->getUsername();
        $_SESSION[self::$userAgent] =  $this->getClientsBrowserName();
    }

    private function getClientsBrowserName() {
        return $_SERVER["HTTP_USER_AGENT"];
    }

    public function saveCredentials() {
        $this->loggedInUser->setTempPassword();
        $this->auth->saveAuthCredentials($this->loggedInUser);
    }

    public function loginUserByCookies(User $user) {
        $this->auth->validateAuthCredentials($user);
        $this->loggedInUser = $user;
    }


    public function getLoggedInUser() {
        return $this->loggedInUser;
    }

    public function endSession() {
        unset($_SESSION[self::$sessionName]);
        unset($_SESSION[self::$userAgent]);
    }

    public function isUserLoggedIn() : bool {
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
        if (!$this->getClientsBrowserName()) {
            return FALSE;
        }
        if(!isset($_SESSION[self::$userAgent])) {
            return FALSE;
        }
        return $this->getClientsBrowserName() === $_SESSION[self::$userAgent];
    }

}