<?php

namespace Controller;

require_once(__DIR__ . '/../model/UserStorage.php');

class LoginController {

    private $view;
    private $cookieExpiresIn; 
    private $userStorage;
    private $welcomeMsgShown = TRUE;

    public function __construct(\View\LoginView $loginView) {
        $this->view = $loginView;
        $this->userStorage = new \Model\UserStorage();
        $this->cookieExpiresIn = time() + (7 * 24 * 60 * 60);
    }
    
    public function loginUserByRequest() {
        try {
                $this->tryLoginUser();

        } catch (\Model\LoginUserException $e) {
            $this->setLoginErrorMsg($e);
        } catch (\Model\RegisterUserException $e) {}
    }
   
    //TODO BREAK OUT TO SMALLER FUNCTION HELP WITH NAMING.
    private function tryLoginUser() {
        if ($this->view->userWantsToLogin()) {
            if ($this->view->isCredentialsSet()) {
                $userCredentials = $this->view->getUserCredentials();
                $this->userStorage->loginUserByRequest($userCredentials);
                $this->view->setWelcomeMsg();
                $this->doKeepMeLoggedIn();
                $this->userStorage->setUserSession();
            } else {
                $this->view->setCredentialsMissingMsg();
            }
        }
    }

    private function doKeepMeLoggedIn() {
        if ($this->view->rememberMe()) {
            $this->userStorage->saveCredentials();
            $this->view->setCookies($this->userStorage->getLoggedInUser(), $this->cookieExpiresIn);
            $this->view->setRememberMeWelcomeMsg();
        }
    }

    private function setLoginErrorMsg(\Model\LoginUserException $e) {
        if ($e instanceof \Model\WrongCredentialsException) {
            $this->view->setWrongNameOrPwd();
        }
    }

    private function setCookieLoginErrorMsg(\Model\LoginUserException $e) {
        if ($e instanceof \Model\WrongCookieInfoException) {
            $this->view->setWrongInfoInCookies();
        }
    }

    public function loginUserByAuth() {
        try {  
                $this->tryAuthAndLogin();

        } catch (\Model\LoginUserException $e) {
            $this->setCookieLoginErrorMsg($e);
        } catch (\Model\RegisterUserException $e) {}
    }

    private function tryAuthAndLogin() {
        if ($this->userWantsToLoginWithCookies()) {
            $authCredentials = $this->view->getCookieCredentials();
            $this->userStorage->loginUserByCookies($authCredentials);
            $this->view->setWelcomeBackMsg();
            $this->userStorage->setUserSession();
        }
    }

    private function userWantsToLoginWithCookies() : bool  {
        return $this->view->userWantsToAuthenticate() && !$this->userStorage->isUserLoggedIn();
    }

    public function logoutUser() {
        if ($this->userWantsToLogout()) {
            $this->view->removeCookies();
            $this->userStorage->endSession();
            $this->view->setByeMessage();
        }
    }

    private function userWantsToLogout() : bool {
        return $this->view->userWantsToLogout() && $this->userStorage->isUserLoggedIn();
    }
}