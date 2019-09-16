<?php

class RegisterController {
    private $registerView;
    private $loginView;
    private $userIsRegistered = FALSE;

    public function __construct(RegisterView $registerView, LoginView $loginView) {
        $this->registerView = $registerView;
        $this->loginView = $loginView;
    }

    public function getUserIsRegistered() {
        return $this->userIsRegistered;
    }
    
    /**
     * Creates a user and saves the username and password to the DB.
     * @return void
     */
    public function registerUser () {
        if($this->registerView->userWantsToRegister()) {
            try {
                $user = $this->registerView->getUser();
                $user->saveUserToDB();
                $this->loginView->setMessage("User registered");
                $this->loginView->setUsername($user->getUsername());
                $this->userIsRegistered = TRUE;  
            } catch(\Exception $e) {
                $message = $e->getMessage();
                $this->registerView->setMessage($message);
            }
            
        }
    }
    
}