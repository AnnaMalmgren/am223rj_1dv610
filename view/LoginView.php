<?php
namespace View;

require_once(__DIR__ . '/../model/LoginUser.php');
require_once(__DIR__ . '/../model/User.php');

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

        if ($this->isLoggedIn()) {
			return $this->generateLogoutButtonHTML($this->message);
		} else {
			return $this->generateLoginFormHTML($this->message);
		}
	}

	public function userWantsToAuthenticate() : bool {
		return !empty($_COOKIE[self::$cookieName]) && !empty($_COOKIE[self::$cookiePassword]);
	  }

	public function getRequestName() : string {
		return trim($_POST[self::$name]);
	}

	public function getRequestPwd() : string {
		return trim($_POST[self::$password]);
	}

	public function isLoggedIn() {
		return \Model\LoginUser::isUserLoggedIn();
	}

	/**
	 * Checks if user clicked "keep me logged in".
	 */
	public function rememberMe() : bool {
		return isset($_POST[self::$keep]);
	}

	public function userWantsToLogin() : bool {
		return isset($_POST[self::$login]);
	}


	public function userWantsToLogout() : bool {
		return isset($_POST[self::$logout]);
	}
	
	
	public function getUserCredentials() : \Model\User {
		if (empty($this->getRequestName())) {
			throw new \Model\LoginUserException('Username is missing');
		} else if (empty($this->getRequestPwd())) {
			throw new \Model\LoginUserException('Password is missing');
		}
		return new \Model\User($this->getRequestName(), $this->getRequestPwd());
	}

	public function getAuthCredentials() : \Model\User {
		return new \Model\User($_COOKIE[self::$cookieName], $_COOKIE[self::$cookiePassword]);
	}

	
	public function setMessage($message) : string {
		return $this->message = $message;
	}

	public function setWelcomeMessage() {
		if ($this->rememberMe() && !$this->isLoggedIn()) {
			$this->setMessage("Welcome and you will be remembered");
		} else if ($this->userWantsToAuthenticate() && !$this->isLoggedIn()) {
			$this->setMessage("Welcome back with cookie");
		} else if (!$this->isLoggedIn()){
			$this->setMessage("Welcome");
		}
	}
	
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

	public function setCookies(User $user, $expiresIn) {
		setcookie(self::$cookieName, $this->getRequestName(),  $expiresIn);
		setcookie(self::$cookiePassword, $user->tempPassword,  $expiresIn);
	}

	public function removeCookies() {
		setcookie(self::$cookieName, "", time() - 3600);
		setcookie(self::$cookiePassword, "", time() - 3600);
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