<?php

class LoginController {
    private $view;

    public function __construct(LoginView $view) {
        $this->view = $view;
    }

    public function loginUser() {
        if ($this->view->userWantsToLogin()) {
            try {
                $user = $this->view->getUser();
            } catch(\Exception $e) {
                $message = $e->getMessage();
                $this->view->setMessage($message);
            }
        }
    }
}