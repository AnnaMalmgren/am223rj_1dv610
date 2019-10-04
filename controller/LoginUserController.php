<?php

namespace Controller;

require_once(__DIR__ . '/../model/UserStorage.php');
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
                $loggedInUser = new \Model\UserStorage($userCredentials);
                $this->checkRemberMe($userCredentials);
                $this->view->setWelcomeMessage();
                $storage->startNewSession();
            } 
        } catch (\Model\LoginUserException $e) {
            $message = $e->getMessage();
            $this->view->setMessage($message);
        }
    }

    public function authUser() {
        try {
            if ($this->view->userWantsToAuthenticate() && !$this->view->isLoggedIn()) {
                $cookieName = $this->view->getCookieNameValue();
                $this->verifyCookies($cookieName);
                $this->view->setWelcomeMessage();
                //$this->user->startNewSession($uid);
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
            $randomPassword = bin2hex(random_bytes($this->bytesLength));
            $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
            $this->view->setCookies($randomPassword, $this->cookieExpiresIn);
            $this->auth->saveAuthUser($user->getUsername(), $hashedPassword);
         }
    }

}