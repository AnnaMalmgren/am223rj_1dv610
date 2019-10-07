<?php 
namespace Controller;

require_once(__DIR__ . '/../model/RegisteredUser.php');
require_once(__DIR__ . '/../model/User.php');

class RegisterController {
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
    
    public function registerUser () {
        try {
                if($this->view->userWantsToRegister()) {
                    $this->doRegisterUser();
                }

        } catch (\Model\RegisterUserException $e) {
                $message = $e->getMessage();
                $this->view->setMessage($message);
        }   
    }

    private function doRegisterUser() {
        $userCredentials = $this->view->getUser();
        $registeredUser = new \Model\RegisteredUser($userCredentials);
        $this->setSuccesfulRegisterView($userCredentials);
    }

    private function setSuccesfulRegsiterView(\Model\User $user) {
        $this->loginView->setMessage(self::$successMsg);
        $this->loginView->setUsername($user->getUsername());
        $this->userIsRegistered = TRUE;   
    }

}