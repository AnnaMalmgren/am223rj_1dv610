<?php
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('view/RegisterView.php');
require_once('controller/UserController.php');

class App {
    private $view;
    private $registerForm;
    private $loginForm;
    private $timeView;
    private $controller;

    public function __construct() {
        $this->loginView = new LoginView();
        $this->regView = new RegisterView();
        $this->timeView = new DateTimeView();
        $this->view = new LayoutView();

        $this->controller = new UserController($this->regView, $this->loginView);
    }

    public function runApp() {
        session_start();
        $this->changeState();
        $this->renderViews();
    }

    private function changeState() {
        $this->controller->registerUser();
        $this->controller->loginUser();
        $this->controller->logoutUser();
    }

    private function renderViews() {
        
        if ($this->controller->getUserIsRegistered()) {
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
