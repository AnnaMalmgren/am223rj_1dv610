<?php 
namespace Controller;

require_once(__DIR__ . '/../model/User.php');

class RegUserController {
    private $userIsRegistered = FALSE;
    private $view;
    private $loginView;
    private static $successMsg = "Registered new user.";

    public function __construct(\View\RegisterView $registerView, \View\LoginView $loginView) {
        $this->view = $registerView;
        $this->loginView = $loginView;
    }

    public function getUserIsRegistered() {
        return $this->userIsRegistered;
    }
    
    public function doRegisterUser () {
        if($this->view->userWantsToRegister()) {
          $this->registerUser();
        }
    }

    private function registerUser() {
        try {
            $userCredentials = $this->view->getUser();
            $userCredentials->saveRegisteredUser($userCredentials);
            $this->setSuccesScenario($userCredentials);
        } catch (\Model\RegisterUserException $e) {
            $message = $e->getMessage();
            $this->view->setMessage($message);
        }
    }

    private function setSuccesScenario(\Model\User $user) {
        $this->loginView->setMessage(self::$successMsg);
        $this->loginView->setUsername($user->getUsername());
         $this->userIsRegistered = TRUE;   
    }
}