<?php

namespace Controller;

require_once(__DIR__ . '/../model/Auth.php');

class LoginUserController {

    private $view;
    private $auth;
    private static $sessionId = 'username';
    private static $sessionAgent = 'user_agent';
    private $bytesLength = 12;
    private $cookieExpiresIn;

    public function __construct(\View\LoginView $loginView) {
        $this->view = $loginView;
        $this->auth = new \Model\Auth();
        $this->cookieExpiresIn = time() + (7 * 24 * 60 * 60);
    }
    
   
    public function loginUser() {
        try {
            if ($this->view->userWantsToLogin()) {
                $user = $this->view->getLoginUser();   
                $this->view->setWelcomeMessage();
                $this->startNewSession($user->getUsername());
                // if "keep me logged in" is checked creates cookies and save auth info.
                $this->checkRemberMe($user);
                $this->view->setUserName($user->getUsername());
            } 
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->view->setMessage($message);
        }
    }

    public function authUser() {
        try {
            if ($this->view->userWantsToAuthenticate()) {
                $uid = $this->view->getCookieNameValue();
                if (isset($_SESSION['username'])) {
                    if (!$this->auth->verifyUserAgent($uid, $_SERVER['HTTP_USER_AGENT'])) {
                        setcookie($this->view->getCookieName(), "", time() - 3600);
                        setcookie($this->view->getCookiePassword(), "", time() - 3600);
                        unset($_SESSION[self::$sessionId]);
                    }
                } else if (!isset($_SESSION['username'])) {
                    $this->verifyCookies($uid);
                    $this->view->setWelcomeMessage();
                    $this->startNewSession($uid);
                }
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->view->setMessage($message);
        }
    }

    public function logoutUser() {
        //user can only logout if is logged in-
        if ($this->view->userWantsToLogout() && $this->view->isLoggedIn()) {
            // removes cookies and session variable.
            setcookie($this->view->getCookieName(), "", time() - 3600);
            setcookie($this->view->getCookiePassword(), "", time() - 3600);
            unset($_SESSION[self::$sessionId]);
            $this->view->setMessage("Bye bye!");
        }
    }

    private function verifyUserAgent() : bool {
        $browserUserAgent = $_SERVER['HTTP_USER_AGENT'];
        return $browserUserAgent === $this->userAgent;
    }

    private function verifyCookies($uid) {
        $pwd = $this->view->getCookiePasswordValue();
        // Check if cookie expiredate and pwd is valid.
        $expireDateCheck = $this->auth->verifyExpireDate($uid);
        $pwdTokenCheck = $this->auth->verifyPwdToken($uid, $pwd);
        if (!$expireDateCheck || !$pwdTokenCheck ) {
            throw new \Model\LoginUserException('Wrong information in cookies');
        }
    }
    
    private function checkRemberMe($user) {
        // User has checked "Keep me logged" in and entered correct login information.
        if ($this->view->rememberMe() && $user) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        //generates cookies and saves auth info to DB.
            $randomPassword = bin2hex(random_bytes($this->bytesLength));
            $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
            $this->createCookies($randomPassword, $user->getUsername());
            $this->auth->saveAuthToDB($user->getUsername(), $hashedPassword, $userAgent);
         }
    }

    private function createCookies($randomPwd, $uid) {
        setcookie($this->view->getCookieName(), $uid,  $this->cookieExpiresIn);
        setcookie($this->view->getCookiePassword(), $randomPwd,  $this->cookieExpiresIn);
    }

    private function startNewSession($uid) {
        session_regenerate_id();
        $_SESSION[self::$sessionId] = $uid;
    }
}