<?php 

namespace Controller;

class RegUserController {
    private $userIsRegistered = FALSE;
    private $view;
    private $loginView;
    private $conn;
    private static $successMsg = "Registered new user.";

    public function __construct(\View\RegisterView $registerView, \View\LoginView $loginView) {
        $this->view = $registerView;
        $this->loginView = $loginView;
    }

    public function getUserIsRegistered() {
        return $this->userIsRegistered;
    }
    
    public function registerUser () {
        if($this->view->userWantsToRegister()) {
            try {
                // creates user, throws exception if register form is invalid.
                $user = $this->view->getUser();
                $this->saveRegisteredUser($user);
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $this->view->setMessage($message);
            }
        }
    }

    private function saveRegisteredUser($user) {
        $user->saveUserToDB();
        $this->loginView->setMessage(self::$successMsg);
        $this->loginView->setUsername($user->getUsername());
        $this->userIsRegistered = TRUE;
    }
}