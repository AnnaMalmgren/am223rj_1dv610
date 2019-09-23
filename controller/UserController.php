<?php
require_once(__DIR__ . '/../model/Auth.php');
require_once(__DIR__ . '/../model/LoginUserException.php');
require_once(__DIR__ . '/../model/RegisterUserException.php');

class UserController {

    private $registerView;
    private $loginView;
    private $auth;

    private $userIsRegistered = FALSE;
    private $minUidLenght = 3;
    private $minPwdLenght = 6;

    public function __construct(RegisterView $registerView, LoginView $loginView) {
        $this->registerView = $registerView;
        $this->loginView = $loginView;
    }

    public function getUserIsRegistered() {
        return $this->userIsRegistered;
    }
    
    public function registerUser () {
        if($this->registerView->userWantsToRegister()) {
  
            $regUid = $this->registerView->getRequestName();
            $regPwd = $this->registerView->getRequestPwd();
            $regPwdRepeat = $this->registerView->getRequestPwdRepeat();

            try {
                if(!$this->isRegFormValid($regUid, $regPwd, $regPwdRepeat)) {
                    return;
                }

                $user = $this->registerView->getUser();

                if ($user->getUserFromDB($regUid)) {
                    throw new RegisterUserException('User exists, pick another username.');
                }

               $this->saveRegisteredUser($user, $regUid);

            } catch(\Exception $e) {
                $message = $e->getMessage();
                $this->registerView->setMessage($message);
            }
            
        }
    }

    private function saveRegisteredUser($user, $uid) {
        $user->saveUserToDB();
        $this->loginView->setMessage("Registered new user.");
        $this->loginView->setUsername($uid);
        $this->userIsRegistered = TRUE;
    }

    private function isRegFormValid($uid, $pwd, $repeatedPwd) : bool {
        if (empty($uid) && empty($password)) {
            throw new RegisterUserException('Username has too few characters, at least 3 characters.<br>Password has too few characters, at least 6 characters.'); 
        } else if (strlen($uid) < $this->minUidLenght) {
            throw new RegisterUserException('Username has too few characters, at least 3 characters.');
        } else if ($uid!== htmlentities($uid)) {
            throw new RegisterUserException('Username contains invalid characters.');
        } else if (strlen($pwd) < $this->minPwdLenght) {
            throw new RegisterUserException('Password has too few characters, at least 6 characters.');
        } else if ($pwd !== $repeatedPwd) {
            throw new RegisterUserException('Passwords do not match.');
        } else {
            return TRUE;
        }
    }

    public function loginUser() {
        if ($this->loginView->userWantsToLogin()) {

            $loginUid = $this->loginView->getRequestName();
            $loginPwd =  $this->loginView->getRequestPwd();

            try {

               $this->setLoginMessage();

                if (!$this->isLoginFormValid($loginUid, $loginPwd)) {
                    return;
                }

                $user = $this->loginView->getLoginUser();

                $this->verifyLoginUser($user, $loginUid, $loginPwd);

                $this->setAuthCookies($user, $loginUid, $loginPwd);

                $this->loginView->setUserName($loginUid);

                $this->startNewSession($loginUid);
                

            } catch(\Exception $e) {
                $message = $e->getMessage();
                $this->loginView->setMessage($message);
            }
        }
    }

    private function verifyLoginUser($user, $loginUid, $loginPwd) {
        if (!$user->getUserFromDB($loginUid)) {
            throw new LoginUserException('Wrong name or password');
        } else if (!$user->verifyPassword($loginUid, $loginPwd)){
            throw new LoginUserException('Wrong name or password');
        }
    }

    private function setAuthCookies($user, $loginUid, $loginPwd) {
        if ($this->loginView->rememberMe() && $user) {
            $this->auth = new Auth($loginUid, $loginPwd);
            $this->auth->createCookies($this->loginView->getCookieName(), $this->loginView->getCookiePassword());
            $this->auth->saveAuthToDB();
         }
    }

    private function isLoginFormValid($uid, $pwd) {
        if (empty($uid)) {
            throw new LoginUserException('Username is missing');
        } else if(empty($pwd)) {
            throw new LoginUserException('Password is missing');
        } else {
            return TRUE;
        }

    }

    private function setLoginMessage() {
        if (!$this->loginView->isLoggedIn()) {
            $this->loginView->setMessage("Welcome");
        }
    }

    private function startNewSession($uid) {
        session_regenerate_id();
        $_SESSION['username'] = $uid;
    }

    public function logoutUser() {
        if ($this->loginView->userWantsToLogout() && $this->loginView->isLoggedIn()) {
            session_unset();
            session_destroy();
            $this->loginView->setMessage("Bye bye!");
        }
    }
}