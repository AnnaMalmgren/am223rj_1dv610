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
                $this->doRegisterUser();

        } catch (\Model\RegisterUserException $e) {
            $this->setRegisterErrorMsg($e);
        }   
    }
    
    //TODO BREAK OUT TO SMALLER FUNCTION HELP WITH NAMING.
    private function doRegisterUser() {
        if($this->view->userWantsToRegister()) {
            if ($this->isCredentialsValid()) {
                $userCredentials = $this->view->getUser();
                $registeredUser = new \Model\RegisteredUser($userCredentials);
                $this->setSuccesfullRegisterView($userCredentials);
            } else {
                $this->setNotValidCredentialsMsg();
            }
        }
    }

    private function isCredentialsValid() {
       return !$this->view->isFieldMissing() && $this->view->doesPasswordsMatch();
    }

    private function setSuccesfullRegsiterView(\Model\User $user) {
        $this->loginView->setMessage(self::$successMsg);
        $this->loginView->setUsername($user->getUsername());
        $this->userIsRegistered = TRUE;   
    }

    private function setNotValidCredentialsMsg() {
        if ($this->view->isFieldMissing()) {
            $this->view->setCredentialsMissingMsg();
        } else if (!$this->view->doesPasswordsMatch()) {
            $this->view->setPwdsDontMatchMessage();
        }
    }

    private function setRegisterErrorMsg(\Model\RegisterUserException $e) {
        if ($e instanceof \Model\ToShortUserNameException) {
            $this->view->setToShortUsernameMessage();
        } else if ($e instanceof \Model\ToShortPasswordException) {
            $this->view->setToShortPwdMessage();
        } else if ($e instanceof \Model\InvalidCharactersException) {
            $this->view->setInvalidCharactersMessage();
        } else if ($e instanceof \Model\UsernameExistsException) {
            $this->view->setUserExistsMessage();
        } 
    }

}