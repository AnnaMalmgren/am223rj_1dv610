<?php
require_once(__DIR__ . '/../model/User.php');

class RegisterView extends LoginView {
	private static $name = 'RegisterView::UserName';
    private static $password = 'RegisterView::Password';
    private static $passwordRepeat = 'RegisterView::PasswordRepeat';
	private static $messageId = 'RegisterView::Message';
	private static $register = 'RegisterView::Register';
	private $message = "";
	

    /**
	 * Create HTTP response
	 *
	 * Should be called after a register attempt has been determined
	 *
	 * @return  void
	 */
	public function response() {
	
		$message = $this->message;
		

        $response = $this->generateRegisterFormHTML($message);
        
		return $response;
	}

	public function getRequestName() : string {
		return $_POST[self::$name];
	}

	private function getRequestPwd() : string {
		return $_POST[self::$password];
	}

	private function getRequestPwdRepeat() : string {
		return $_POST[self::$passwordRepeat];
	}

	/**
	 * Checks if user has clicked register.
	 * @return bool 
	 */
	public function userWantsToRegister() : bool {
		return isset($_POST[self::$register]);
	}

	/**
	 * Checks if user wants to register and returns the entered username or an empty string.
	 * @return string the user name entered or "".
	 */
	private function getFilteredName() : string {
		if ($this->userWantsToRegister()) {
			return preg_replace("/[^a-zA-Z0-9\s]/", "", $this->getRequestName());
		} else {
			 return "";
		 }
	}

	/**
	 * Creates an User object with entered input
	 * @return User 
	 */
	public function getUser() : User {
		return new User($this->getRequestName(), $this->getRequestPwd(), $this->getRequestPwdRepeat());
	}
	



	/**
	 * Sets a message to be printed to the user.
	 * @param string the message to på written to the user.
	 * @return string message to user.
	 */
	public function setMessage($message) : string {
		return $this->message = $message;
	}

	
    /**
	* Generate HTML code on the output buffer for the register form.
	* @param $message, String output message
	* @return  void
	*/
	private function generateRegisterFormHTML($message) {
		return '
			<form action="?register" method="post" enctype="multipart/form-data"> 
				<fieldset>
					<legend>Register a new user - Write username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getFilteredName() . '" />
                    <br>
					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />
                    <br>
                    <label for="' . self::$passwordRepeat . '">Repeat password :</label>
					<input type="password" id="' . self::$passwordRepeat . '" name="' . self::$passwordRepeat . '" />
                    <br>
                    <input id ="submit" type="submit" name=' . self::$register . ' value="Register" />
                    <br>
				</fieldset>
			</form>
		';
	}

}