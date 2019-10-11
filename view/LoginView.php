<?php
namespace View;

require_once(__DIR__ . '/../model/UserStorage.php');
require_once(__DIR__ . '/../model/UserCredentials.php');

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
	private $userStorage;
	private $cookieExpiresIn;

	public function __construct() {
		$this->userStorage = new \Model\UserStorage();
		$this->cookieExpiresIn = time() + (7 * 24 * 60 * 60);
	}

	public function userWantsToAuthenticate() : bool {
		return !empty($_COOKIE[self::$cookieName]) && !empty($_COOKIE[self::$cookiePassword]);
	  }

	public function userWantsToLogin() : bool {
		return isset($_POST[self::$login]);
	}

	public function userWantsToLogout() : bool {
		return isset($_POST[self::$logout]);
	}

	public function rememberMe() : bool {
		return isset($_POST[self::$keep]);
	}

	public function getRequestName() : string {
		return trim($_POST[self::$name]);
	}

	public function getRequestPwd() : string {
		return trim($_POST[self::$password]);
	}
	
	private function getLoginCredentials() : \Model\UserCredentials {
		return new \Model\UserCredentials($this->getRequestName(), $this->getRequestPwd());
	}

	private function getCookieCredentials() : \Model\UserCredentials {
		return new \Model\UserCredentials($_COOKIE[self::$cookieName], $_COOKIE[self::$cookiePassword]);
	}

	public function getUserCredentials() : \Model\UserCredentials {
		if ($this->userWantsToLogin()) {
			return $this->getLoginCredentials();
		} else if ($this->userWantsToAuthenticate()) {
			return $this->getCookieCredentials();
		}
	}

	public function setNoUsernamegMsg() {
		$this->message = "Username is missing";
	}

	public function setNoPasswordMsg() {
		$this->message = "Password is missing";
	}

	public function setWrongNameOrPwdMsg() {
		$this->message = "Wrong name or password";
	}

	public function setWrongAuthCredentialsMsg() {
		$this->message = "Wrong information in cookies";
	}

	public function setUserRegisteredMsg() {
		$this->message = "Registered new user.";
	}

	public function setWelcomeBackMsg() {
		$this->message = "Welcome back with cookie";
	}

	public function setByeMessage() {
		$this->message = "Bye bye!";
	}

	public function isLoggedIn() {
		return $this->userStorage->isUserLoggedIn();
	}

	public function setUsername($username) {
		$this->username = $username;
	}

	private function getUsername() : string {
		if ($this->userWantsToLogin()) {
			return $this->getRequestName();
		} else {
			return $this->username;
		}
	}

	public function setLoggedInView(\Model\User $user) {
		if ($this->rememberMe()) {
			$this->setCookies($user);
			$this->message = "Welcome and you will be remembered";
		} else {
			$this->message = "Welcome";
		}
	}

    public function setRememberMeUI(\Model\User $user) {
		$this->setCookies($user);
		$this->setRememberMeWelcomeMsg();
	}

	private function setCookies(\Model\User $user) {
		setcookie(self::$cookieName, $this->getRequestName(), $this->cookieExpiresIn);
		setcookie(self::$cookiePassword, $user->getTempPassword(), $this->cookieExpiresIn);
	}

	public function setLogoutUI() {
		$this->removeCookies();
		$this->setByeMessage();
	}

	public function removeCookies() {
		setcookie(self::$cookieName, "", time() - 3600);
		setcookie(self::$cookiePassword, "", time() - 3600);
	}

	public function response() {

        if ($this->isLoggedIn()) {
			return $this->generateLogoutButtonHTML($this->message);
		} else {
			return $this->generateLoginFormHTML($this->message);
		}
	}

	private function generateLogoutButtonHTML($message) {
		return '
			<form  method="post" >
				<p id="' . self::$messageId . '">' . $message .'</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
	}

	private function generateLoginFormHTML($message) {
		return '
			<form method="post" action="?" > 
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getUsername() . '" />

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