<?php

namespace Controller;

require_once(__DIR__ . '/../model/UserStorage.php');

class LoginUserController {

    private $view;
    private $cookieExpiresIn; 
    private $userStorage;

    public function __construct(\View\LoginView $loginView) {
        $this->view = $loginView;
        $this->userStorage = new \Model\UserStorage();
        $this->cookieExpiresIn = time() + (7 * 24 * 60 * 60);
    }
    
    public function loginUser() {
        try {
            if ($this->view->userWantsToLogin()) {
                $this->doLoginUser();
                $this->view->setWelcomeMessage();
            } 
        } catch (\Model\LoginUserException $e) {
            $message = $e->getMessage();
            $this->view->setMessage($message);
        }
    }

    private function doLoginUser() {
        $userCredentials = $this->view->getUserCredentials();
        $this->userStorage->loginUserByRequest($userCredentials);
        if ($this->view->rememberMe()) {
            $this->doKeepMeLoggedIn();
        }
    }

    private function doKeepMeLoggedIn() {
        $this->userStorage->keepMeLoggedIn();
        $this->view->setCookies($this->userStorage->getLoggedInUser(), $this->cookieExpiresIn);
    }

    public function authUser() {
        try {
                if ($this->view->userWantsToAuthenticate() && !$this->userStorage->isUserLoggedIn()) {
                    $this->doAuthentication();
                    $this->view->setWelcomeMessage();
                }
        } catch (\Model\LoginUserException $e) {
            $message = $e->getMessage();
            $this->view->setMessage($message);
        }
    }

    private function doAuthentication() {
        $authCredentials = $this->view->getAuthCredentials();
        $this->userStorage->loginUserByAuth($authCredentials);
    }

    public function logoutUser() {
        if ($this->view->userWantsToLogout() && $this->userStorage->isUserLoggedIn()) {
            $this->view->removeCookies();
            $this->userStorage->endSession();
            $this->view->setMessage("Bye bye!");
        }
    }
}