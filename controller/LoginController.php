<?php

class LoginController {
    public function checkLoginForm() {
        if (isset($_POST['LoginView::Login'])) {
            if (!isset($_POST['LoginView::UserName']) || trim($_POST['LoginView::UserName']) === 0) {
                return "Username is Missing";
            } else {
                return "";
            }
        }
    }
}
