<?php
require(__DIR__ . '/../controller/RegisterController.php');

class RegisterView extends LoginView {
	private static $name = 'RegisterView::UserName';
    private static $password = 'RegisterView::Password';
    private static $passwordRepeat = 'RegisterView::PasswordRepeat';
	private static $messageId = 'RegisterView::Message';
	private $controller;

	public function __construct() {
		$this->controller = new RegisterController();
	}
    
    /**
	* Generate HTML code on the output buffer for the register form.
	* @param $message, String output message
	* @return  void
	*/
	private function generateRegisterFormHTML($message) {
		return '
			<form action="?register" method="post" > 
				<fieldset>
					<legend>Register a new user - Write username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getUsernameValue() .'" />
                    <br>
					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />
                    <br>
                    <label for="' . self::$passwordRepeat . '">Repeat password :</label>
					<input type="password" id="' . self::$passwordRepeat . '" name="' . self::$passwordRepeat . '" />
                    <br>
                    <input id ="submit" type="submit" name="DoRegistration" value="Register" />
                    <br>
				</fieldset>
			</form>
		';
    }
	
	/**
	 * Checks if username was entered and retrives the value.
	 *  @return string the username or an empty string.
	 */
	private function getUsernameValue() {
		return $this->controller->getUsername() !== null ? $this->controller->getUsername() : "";
	}

    /**
	 * Create HTTP response
	 *
	 * Should be called after a register attempt has been determined
	 *
	 * @return  void
	 */
	public function response() {  
		$message = $this->controller->validateFormInput();
		
        $response = $this->generateRegisterFormHTML($message);
        
		return $response;
	}
}