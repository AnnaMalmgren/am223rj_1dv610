<?php

namespace Controller;

require_once(__DIR__ . '/../model/UserStorage.php');

class LoginController {

    private $view;
    private $userStorage;

    public function __construct(\View\LoginView $loginView) {
        $this->view = $loginView;
        $this->userStorage = new \Model\UserStorage();
    }
    
    public function loginUserByRequest() {
        try {
                $this->tryLoginUser();

        } catch (\Model\UsernameMissingException $e) {
            $this->view->setNoUsernamegMsg();
        } catch (\Model\PasswordMissingException $e) {
            $this->view->setNoPasswordMsg();
        } catch (\Model\WrongCredentialsException $e) {
            $this->view->setWrongNameOrPwdMsg();
        } 
    }
   

    private function tryLoginUser() {
        if ($this->view->userWantsToLogin()) {
            $userCredentials = $this->view->getUserCredentials();
            $this->userStorage->loginUserByRequest($userCredentials, $this->view->rememberMe());
            $this->view->setLoggedInView($this->userStorage->getLoggedInUser());
        }
    }

    public function loginUserByAuth() {
        try {  
                $this->tryAuthAndLogin();

        } catch (\Model\WrongAuthCredentialsException $e) {
            $this->view->setWrongAuthCredentialsMsg();
        }
    }

    private function tryAuthAndLogin() {
        if ($this->userWantsLoginByAuth()) {
            $authCredentials = $this->view->getUserCredentials();
            $this->userStorage->loginUserByAuth($authCredentials);
            $this->view->setWelcomeBackMsg();
        }
    }

    private function userWantsLoginByAuth() : bool  {
        return $this->view->userWantsToAuthenticate() && !$this->userStorage->isUserLoggedIn();
    }

    public function logoutUser() {
        if ($this->userWantsToLogout()) {
            $this->view->setLogoutUI();
            $this->userStorage->endSession();
        }
    }

    private function userWantsToLogout() : bool {
        return $this->view->userWantsToLogout() && $this->userStorage->isUserLoggedIn();
    }
}