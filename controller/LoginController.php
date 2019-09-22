<?php

class LoginController {
    private $view;

    public function __construct(LoginView $view) {
        $this->view = $view;
    }

    public function loginUser() {
        if ($this->view->userWantsToLogin()) {
            try {

                if (!$this->view->isLoggedIn()) {
                    $this->view->setMessage("Welcome");
                }

                $user = $this->view->getLoginUser();

                if ($this->view->rememberMe() && $user) {
                   $user->createCookies($this->view->getCookieName(), $this->view->getCookiePassword());
                   $user->saveAuthToDB();
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

    public function validateAuthCookies() : bool {
        return $this->user->validateCookies($this->view->getCookiePassword());
    }

    public function logoutUser () {
        if ($this->view->userWantsToLogout() && $this->view->isLoggedIn()) {
            session_unset();
            session_destroy();
            $this->view->setMessage("Bye bye!");
        }
    }
}