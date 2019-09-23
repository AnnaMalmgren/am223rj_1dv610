<?php
require_once(__DIR__ . '/../model/LoginUser.php');
require_once(__DIR__ . '/../model/Auth.php');

class LoginView {
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';
	private $message = "";
	private $username = "";

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response() {
		$message = $this->message;

		$response = $this->generateLoginFormHTML($message);

        if ($this->isLoggedIn()) {
			$response = $this->generateLogoutButtonHTML($message);
		} else {
			$response = $this->generateLoginFormHTML($message);
		}
		
		return $response;
	}

	public function getRequestName() : string {
		return trim($_POST[self::$name]);
	}

	public function getRequestPwd() : string {
		return trim($_POST[self::$password]);
	}
	
	/**
	 * Checks if SESSION variable username is set.
	 * @return bool
	 */
	public function isLoggedIn () {
		if (isset($_SESSION['username'])) {
			return TRUE;
		} else if (!empty($_COOKIE[self::$cookieName]) && !empty($_COOKIE[self::$cookiePassword])) {
			$auth = new Auth($_COOKIE[self::$cookieName], $_COOKIE[self::$cookiePassword]);
			return $auth->AuthUser();
		}
	}

	/**
	 * Checks if user clicked "remember me".
	 * @return bool
	 */
	public function rememberMe() : bool {
		return isset($_POST[self::$keep]);
	}

	/**
	 * Checks if user has clicked login.
	 * @return bool 
	 */
	public function userWantsToLogin() : bool {
		return isset($_POST[self::$login]);
	}

    /**
	 * Checks if user has clicked logout.
	 * @return bool 
	 */
	public function userWantsToLogout() : bool {
		return isset($_POST[self::$logout]);
	}
	
	
	public function getLoginUser() : LoginUser {
		return new LoginUser($this->getRequestName(), $this->getRequestPwd());
	}
	
	/**
	 * Sets message to be written to the user.
	 * @return string
	 */
	public function setMessage($message) : string {
		return $this->message = $message;
	}
	
	/**
	 * Sets the username
	 * @return string
	 */
	public function setUserName($username) {
		$this->username = $username;
	}

	private function getUserName() : string {
		if ($this->userWantsToLogin()) {
			return $this->getRequestName();
		} else {
			return $this->username;
		}
	}

	public function getCookieName() {
		return self::$cookieName;
	}

	public function getCookiePassword() {
		return self::$cookiePassword;
	}


	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	private function generateLogoutButtonHTML($message) {
		return '
			<form  method="post" >
				<p id="' . self::$messageId . '">' . $message .'</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
	}
	
	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	private function generateLoginFormHTML($message) {
		return '
			<form method="post" action="?" > 
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getUserName() . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					
					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}
}