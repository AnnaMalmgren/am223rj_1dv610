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
    private $regController;
    private $LoginController;

    public function __construct() {
        $this->loginView = new \View\LoginView();
        $this->regView = new \View\RegisterView();
        $this->timeView = new \View\DateTimeView();
        $this->view = new \View\LayoutView();

        $this->regController = new \Controller\RegisterController($this->regView, $this->loginView);
        $this->loginController = new \Controller\LoginController($this->loginView);
    }

    public function runApp() {
        session_start();
        $this->changeState();
        $this->renderViews();
    }

    private function changeState() {
        if (!$this->loginView->isLoggedIn()) {
            $this->loginController->loginUserByRequest();
            $this->loginController->loginUserByAuth();
            $this->regController->registerUser();
        } else {
            $this->loginController->logoutUser();
        }
    }

    private function renderViews() {   
        if ($this->regController->getUserIsRegistered()) {
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
        $regLink = '<a href="?register">Register a new user</a>';
        
        if(!$this->loginView->isLoggedIn()) {
            $this->view->setLink($regLink);
        }
 
        $this->view->render($this->loginView->isLoggedIn(), $this->loginView, $this->timeView);
    }
}
