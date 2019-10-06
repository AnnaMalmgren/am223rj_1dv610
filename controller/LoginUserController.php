<?php

namespace Controller;

require_once(__DIR__ . '/../model/UserStorage.php');
require_once(__DIR__ . '/../model/Authentication.php');

class LoginUserController {

    private $view;
    private $auth;
    private $userStorage;
    private $bytesLength = 12;
    private $cookieExpiresIn; 

    public function __construct(\View\LoginView $loginView) {
        $this->view = $loginView;
        $this->auth = new \Model\Authentication();
        $this->userStorage = new \Model\UserStorage();
        $this->cookieExpiresIn = time() + (7 * 24 * 60 * 60);
    }
    
    public function loginUser() {
        try {
            if ($this->view->userWantsToLogin()) {
                $userCredentials = $this->view->getUserCredentials();
                $this->validateCredentials($this->userStorage, $userCredentials);
                $this->keepMeLoggedIn($userCredentials);
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
                $this->validateCredentials($this->auth, $authCredentials);
            }
        } catch (\Model\LoginUserException $e) {
            $message = $e->getMessage();
            $this->view->setMessage($message);
        }
    }

    private function validateCredentials($validator, \Model\User $user) {
        $validator->validateCredentials($user);
        $this->view->setWelcomeMessage();
        $this->userStorage->startNewSession($user);
    }

    public function logoutUser() {
        if ($this->view->userWantsToLogout() && $this->view->isLoggedIn()) {
            $this->view->removeCookies();
            $this->userStorage->endSession();
            $this->view->setMessage("Bye bye!");
        }
    }

    private function keepMeLoggedIn($user) {
        // User has checked "Keep me logged" in and entered correct login information.
        if ($this->view->rememberMe() && $user) {
            $user->setTempPassword();
            $this->view->setCookies($user, $this->cookieExpiresIn);
            $this->auth->saveAuthCredentials($user);
         }
    }

}