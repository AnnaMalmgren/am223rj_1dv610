<?php

class LoginController {
    private $view;

    public function __construct(LoginView $view) {
        $this->view = $view;
    }

    public function loginUser() {
        if ($this->view->userWantsToLogin()) {
            try {
                $user = $this->view->getUserStorage();
                $this->view->setUserName($this->view->getRequestName());
                $this->view->setMessage("Welcome");
            } catch(\Exception $e) {
                $message = $e->getMessage();
                $this->view->setMessage($message);
            }
        }
    }

    public function logoutUser () {
        if ($this->view->userWantsToLogout()) {
            session_unset();
            session_destroy();
        }
    }
}