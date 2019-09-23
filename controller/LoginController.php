<?php

class LoginController {
    private $view;
    private $user;

    public function __construct(LoginView $view) {
        $this->view = $view;
    }

    public function loginUser() {
        if ($this->view->userWantsToLogin()) {
            try {

                if (!$this->view->isLoggedIn()) {
                    $this->view->setMessage("Welcome");
                }

                $this->user = $this->view->getLoginUser();

                if ($this->view->rememberMe() && $user) {
                   $this->user->createCookies($this->view->getCookieName(), $this->view->getCookiePassword());
                   $this->user->saveAuthToDB();
                }

                $this->view->setUserName($this->view->getRequestName()); 
            } catch(\Exception $e) {
                $message = $e->getMessage();
                $this->view->setMessage($message);
            }
        }
    }

    public function isCookieNameSet() : bool {
        return isset($_COOKIE[$this->view->getCookieName()]);
    }

    public function isCookiePasswordSet() : bool {
        return isset($_COOKIE[$this->view->getCookiePassword()]);
    }

    private function authPasswordVerify() {
        return password_verify($_COOKIE[$this->view->getCookiePassword()], $this->user->getHashedPassword);
    }

    private function authExpireDate() {
        $currentDate = date("Y-m-d H:i:s", time());

        if ($this->user->getExpireDate() > $currentDate ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    public function validateAuthCookies() : bool {
        if ($this->authExpireDate() && $this->authPasswordVerify()) {
            return TRUE;
        }
    }

    public function logoutUser () {
        if ($this->view->userWantsToLogout() && $this->view->isLoggedIn()) {
            session_unset();
            session_destroy();
            $this->view->setMessage("Bye bye!");
        }
    }
}