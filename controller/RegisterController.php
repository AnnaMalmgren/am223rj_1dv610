<?php

class RegisterController {
    private $username = null;
    private $password = null;

    public function __construct() {
        if (isset($_POST["DoRegistration"])) {
            $this->username = $_POST['RegisterView::UserName'];
            $this->password = $_POST['RegisterView::Password'];
        }
    }

    public function validateFormInput() {
        if (empty(trim($this->username)) && $this->username !== null) {
            return "Username is Missing";
        } else if (empty(trim($this->password)) && $this->password !==null) {
            return "Password is Missing";
        } else {
            return "";
        }
    }
}