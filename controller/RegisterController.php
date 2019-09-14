<?php

class RegisterController {
    private $username = null;
    private $password = null;
    private $passwordRepeat = null;

    public function __construct() {
        if (isset($_POST["DoRegistration"])) {
            $this->username = trim($_POST['RegisterView::UserName']);
            $this->password = trim($_POST['RegisterView::Password']);
            $this->passwordRepeat = trim($_POST['RegisterView::PasswordRepeat']);
        }
    }
    public function validateFormInput() {
        if ($this->username !== null || $this->password !== null || $this->passwordRepeat !== null) {
            if (empty($this->username) && empty($this->password)) {
                return "Username has too few characters, at least 3 characters. Password has too few characters, at least 6 characters.";
            } else if (strlen($this->username) < 3) {
                return "Username has too few characters, at least 3 characters.";
            } else if (strlen($this->password) < 6) {
                return "Password has too few characters, at least 6 characters.";
            } else if ($this->password !== $this->passwordRepeat) {
                return "Passwords do not match.";
            } else {
                return "User registrated";
            }
        }
    }
}