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