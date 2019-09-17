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
        $this->loginForm = new LoginView();
        $this->registerForm = new RegisterView();
        $this->timeView = new DateTimeView();
        $this->view = new LayoutView();

        $this->registerController = new RegisterController($this->registerForm, $this->loginForm);
        $this->loginController = new LoginController($this->loginForm);
    }

    public function changeState() {
        $this->registerController->registerUser();
        $this->loginController->loginUser();
    }

    public function runApp() {
        $this->changeState();

        if ($this->registerController->getUserIsRegistered()) {
            $this->renderLoginView();
        } else {
            $this->view->userClicksRegisterLink() ? $this->renderRegisterView() : $this->renderLoginView();
        }
    }

    private function renderRegisterView() {
        $this->view->setLinkGoBack();
        return $this->view->render(false, $this->registerForm, $this->timeView);
    }

    private function renderLoginView() {
        $this->view->setLinkRegister();
        return $this->view->render(false, $this->loginForm, $this->timeView);
    }
}
