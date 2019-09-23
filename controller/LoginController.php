<?php
require_once(__DIR__ . '/../model/Auth.php');

class LoginController {
    private $view;
    private $Auth;

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
                   $this->auth = new Auth($this->view->getRequestName(), $this->view->getRequestPwd());
                   $this->auth->createCookies($this->view->getCookieName(), $this->view->getCookiePassword());
                   $this->auth->saveAuthToDB();
                }

                $this->view->setUserName($this->view->getRequestName()); 
            } catch(\Exception $e) {
                $message = $e->getMessage();
                $this->view->setMessage($message);
            }
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