<?php

namespace Controller;

require_once(__DIR__ . '/../model/LoginUser.php');
require_once(__DIR__ . '/../model/AuthUser.php');
require_once(__DIR__ . '/../model/DAL/DbAuthTable.php');

class LoginUserController {

    private $view;
    private $auth;
    private $bytesLength = 12;
    private $cookieExpiresIn; 

    public function __construct(\View\LoginView $loginView) {
        $this->view = $loginView;
        $this->auth = new \Model\DbAuthTable();
        $this->cookieExpiresIn = time() + (7 * 24 * 60 * 60);
    }
    
    public function loginUser() {
        try {
            if ($this->view->userWantsToLogin()) {

                $userCredentials = $this->view->getUserCredentials();
                $loggedInUser = new \Model\LoginUser($userCredentials);
                $this->keepMeLoggedIn($userCredentials);
                $this->view->setWelcomeMessage();
                $loggedInUser->startNewSession($userCredentials);
            } 
        } catch (\Model\LoginUserException $e) {
            $message = $e->getMessage();
            $this->view->setMessage($message);
        }
    }

    public function authUser() {
        try {
            if ($this->view->userWantsToAuthenticate() && !$this->view->isLoggedIn()) {
                $authCredentials = $this->view->getAuthCredentials();
                $authenticatedUser = new \Model\AuthUser($authCredentials);
                $this->view->setWelcomeMessage();
                $authenticatedUser->startNewSession($authCredentials);
            }
        } catch (\Model\LoginUserException $e) {
            $message = $e->getMessage();
            $this->view->setMessage($message);
        }
    }

    public function logoutUser() {
        if ($this->view->userWantsToLogout() && $this->view->isLoggedIn()) {
            $this->view->removeCookies();
            //TODO Flytta till userstorage bort med string beroende
            unset($_SESSION['SessionName']);
            $this->view->setMessage("Bye bye!");
        }
    }

    private function keepMeLoggedIn($user) {
        // User has checked "Keep me logged" in and entered correct login information.
        if ($this->view->rememberMe() && $user) {
            $user->setTempPassword();
            $this->view->setCookies($user, $this->cookieExpiresIn);
            $this->auth->saveAuthUser($user);
         }
    }

}