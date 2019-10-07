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
            $this->handleLoginException($e);
        } catch (\Model\RegisterUserException $e) {
            $this->handleRegisterException($e); 
        }
    }
   
    private function tryLoginUser() {
        if ($this->view->userWantsToLogin()) {
            $userCredentials = $this->view->getUserCredentials();
            $this->userStorage->loginUserByRequest($userCredentials);
            $this->doKeepMeLoggedIn();
            $this->setWelcomeMsgAndUserSession();
        }
    }

    private function doKeepMeLoggedIn() {
        if ($this->view->rememberMe()) {
            $this->userStorage->keepMeLoggedIn();
            $this->view->setCookies($this->userStorage->getLoggedInUser(), $this->cookieExpiresIn);
        }
    }

    private function handleLoginException(\Model\LoginUserException $e) {
        $message = $e->getMessage();
        $this->view->setMessage($message);
    }

    private function handleRegisterException(\Model\RegisterUserException $e) {
       return;
    }

    private function setWelcomeMsgAndUserSession() {
        $this->view->setWelcomeMessage();
        $this->userStorage->setUserSession();
    }

    public function loginUserByAuth() {
        try {  
                $this->tryAuthAndLogin();

        } catch (\Model\LoginUserException $e) {
            $this->handleLoginException($e);
        } catch (\Model\RegisterUserException $e) {
            $this->handleRegisterException($e); 
        }
    }

    private function tryAuthAndLogin() {
        if ($this->userWantsToAuthenticate()) {
            $authCredentials = $this->view->getAuthCredentials();
            $this->userStorage->loginUserByAuth($authCredentials);
            $this->setWelcomeMsgAndUserSession();
        }
    }

    private function userWantsToAuthenticate() : bool  {
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