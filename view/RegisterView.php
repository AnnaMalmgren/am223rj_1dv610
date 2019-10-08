<?php
namespace View;

require_once(__DIR__ . '/../model/User.php');

class RegisterView extends LoginView {
	private static $name = 'RegisterView::UserName';
    private static $password = 'RegisterView::Password';
    private static $passwordRepeat = 'RegisterView::PasswordRepeat';
	private static $messageId = 'RegisterView::Message';
	private static $register = 'RegisterView::Register';
	private $message = "";
	

	public function getRequestName() : string {
		return trim($_POST[self::$name]);
	}

	public function getRequestPwd() : string {
		return trim($_POST[self::$password]);
	}

	public function getRequestPwdRepeat() : string {
		return trim($_POST[self::$passwordRepeat]);
	}

	public function userWantsToRegister() : bool {
		return isset($_POST[self::$register]);
	}

	private function getFilteredName() : string {
		if ($this->userWantsToRegister()) {
			return strip_tags($this->getRequestName());
		} else {
			 return "";
		 }
	}

	public function isFieldMissing() : bool {
		return empty($this->getRequestName()) && empty($this->getRequestPwd());
	}

	public function doesPasswordsMatch() : bool {
		return $this->getRequestPwd() == $this->getRequestPwdRepeat();
	}

	public function getUser() : \Model\User {
		return new \Model\User($this->getRequestName(), $this->getRequestPwd());
	}

	public function setCredentialsMissingMsg() {
		if ($this->isFieldMissing()) {
			$userNameMsg = 'Username has too few characters, at least 3 characters.';
			$PwdMsg = 'Password has too few characters, at least 6 characters.';
			$this->message = "$userNameMsg<br>$PwdMsg";
		} 
	}

	public function setToShortUsernameMessage() {
		$this->message = 'Username has too few characters, at least 3 characters.';
	}

	public function setToShortPwdMessage() {
		$this->message = 'Password has too few characters, at least 6 characters.';
	}

	public function setInvalidCharactersMessage() {
		$this->message = 'Username contains invalid characters';
	}

	public function setUserExistsMessage() {
		$this->message = 'User exists, pick another username.';
	}

	public function setPwdsDontMatchMessage() {
		$this->message = 'Passwords do not match.';
	}


	public function response() {
		return $this->generateRegisterFormHTML($this->message);
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