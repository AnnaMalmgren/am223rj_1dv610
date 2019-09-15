<?php

class RegisterController {
    private $username = null;
    private $password = null;
    private $passwordRepeat = null;
    private $userModel;

    public function __construct() {
        if (isset($_POST["DoRegistration"])) {
            $this->username = trim($_POST['RegisterView::UserName']);
            $this->password = trim($_POST['RegisterView::Password']);
            $this->passwordRepeat = trim($_POST['RegisterView::PasswordRepeat']);
        }
    }

    public function getUsername() {
        return $this->username;
    }
    
    /**
     * Check entered username already exits in the database.
     * @return int the number of rows with the entered username.
     */
    private function checkIfUserExits() {
        require(__DIR__ . '/../dbproduction.php');

        $sql = "SELECT username FROM users WHERE username=?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return "Something went wrong (sql error)";
        } else {
            mysqli_stmt_bind_param($stmt, "s", $this->username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            return $numOfUsers = mysqli_stmt_num_rows($stmt);
        }
    }
    
    /**
     * Saves username and hashed password to DB
     * @return void
     */
    private function saveUserToDB() {
        require(__DIR__ . '/../dbproduction.php');

        $sql = "INSERT INTO users (username, password) VALUES(?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return "Something went wrong (sql error)";
        } else {
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT); 

            mysqli_stmt_bind_param($stmt, "ss", $this->username, $hashedPassword);
            mysqli_stmt_execute($stmt);
        }   
    }

    /**
     * Validates the userinput in the form.
     * @return string the message to be printed to the user.
     */
    public function validateFormInput() {
        if ($this->username !== null || $this->password !== null || $this->passwordRepeat !== null) {
            if (empty($this->username) && empty($this->password)) {
                return "Username has too few characters, at least 3 characters.
                <br> 
                Password has too few characters, at least 6 characters.";
            } else if (strlen($this->username) < 3) {
                return "Username has too few characters, at least 3 characters.";
            } else if (strlen($this->password) < 6) {
                return "Password has too few characters, at least 6 characters.";
            } else if ($this->password !== $this->passwordRepeat) {
                return "Passwords do not match.";
            } else {
                 if ($this->checkIfUserExits() > 0) {
                    return "User exists, pick another username.";   
                 } 

                $succesMessage = "Registered new user.";
                $this->saveUserToDB();
                header("Location: ?&msg=$succesMessage&username=$this->username");
                exit();
            }
        }
    }
}