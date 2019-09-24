<?php
require_once(__DIR__ . '/../model/Auth.php');

class LoginUserController {

    private $view;
    private $auth;
    private static $sessionId = 'username';
    private $bytesLength = 12;
    private  $cookieExpiresIn;

    public function __construct(LoginView $loginView) {
        $this->view = $loginView;
        $this->auth = new Auth();
        $this->cookieExpiresIn = time() + (7 * 24 * 60 * 60);
    }

    public function loginUser() {
        try {
            if ($this->view->userWantsToLogin()) {
                $user = $this->view->getLoginUser();

                $this->view->setWelcomeMessage();  
                $this->startNewSession($user->getUsername());
                $this->setCookiesSaveToDB($user);
                $this->view->setUserName($user->getUsername());
            } 
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->view->setMessage($message);
        }
    }

    public function authUser() {
        if($this->view->userWantsToAuthenticate() && !isset($_SESSION[self::$sessionId])) {
            try {
                $uid = $this->view->getCookieNameValue();
                $this->verifyCookie($uid);
                $this->view->setWelcomeMessage();
                $this->startNewSession($uid);
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $this->view->setMessage($message);
            }
        }
    }

    public function logoutUser() {
        if ($this->view->userWantsToLogout() && $this->view->isLoggedIn()) {
            setcookie($this->view->getCookieName(), "", time() - 3600);
            setcookie($this->view->getCookiePassword(), "", time() - 3600);
            unset($_SESSION[self::$sessionId]);
            $this->view->setMessage("Bye bye!");
        }
    }

    private function verifyCookie($uid) {
        $pwd = $this->view->getCookiePasswordValue();

        $expireDateCheck = $this->auth->verifyExpireDate($uid);
        $pwdTokenCheck = $this->auth->verifyPwdToken($uid, $pwd);
        if (!$expireDateCheck || !$pwdTokenCheck ) {
            throw new LoginUserException('Wrong information in cookies');
        }
    }

    private function setCookiesSaveToDB($user) {
        if ($this->view->rememberMe() && $user) {
            $randomPassword = bin2hex(random_bytes($this->bytesLength));
            $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
            $this->createCookies($randomPassword, $user->getUsername());
            $this->auth->saveAuthToDB($user->getUsername(), $hashedPassword);
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