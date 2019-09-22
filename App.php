<?php
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('view/RegisterView.php');
require_once('controller/RegisterController.php');
require_once('controller/LoginController.php');

class App {
    private $view;
    private $registerForm;
    private $loginForm;
    private $timeView;
    private $registerController;
    private $loginController;

    public function __construct() {
        $this->loginView = new LoginView();
        $this->regView = new RegisterView();
        $this->timeView = new DateTimeView();
        $this->view = new LayoutView();

        $this->registerController = new RegisterController($this->regView, $this->loginView);
        $this->loginController = new LoginController($this->loginView);
    }

    public function runApp() {
        session_start();
        $this->changeState();
        $this->renderViews();
    }

    private function changeState() {
        $this->registerController->registerUser();
        $this->loginController->loginUser();
        $this->loginController->logoutUser();
    }

    private function renderViews() {
        if ($this->loginController->isCookieNameSet() && $this->loginController->isCookiePasswordSet()) {
            $this->loginController->validateAuthCookies() ? 
            $this->renderLoginView() :  $this->renderRegisterView();
        } else if ($this->registerController->getUserIsRegistered()) {
            $this->renderLoginView(); 
        } else {
            $this->view->userClicksRegisterLink() ? 
                $this->renderRegisterView() : $this->renderLoginView();
        }
    }

    private function renderRegisterView() {
        $link = '<a href="?">Back to login</a>';
        $this->view->setLink($link);
        $this->view->render($this->loginView->isLoggedIn(), $this->regView, $this->timeView);
    }

    private function renderLoginView() {
        $link = '<a href="?register">Register a new user</a>';
        
        if(!$this->loginView->isLoggedIn()) {
            $this->view->setLink($link);
        }
       
        $this->view->render($this->loginView->isLoggedIn(), $this->loginView, $this->timeView);
    }
}
