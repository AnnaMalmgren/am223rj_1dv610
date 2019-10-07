<?php
namespace View;

class LayoutView {
  
  private static $registerLink = "register";
  private $link = "";

  public function setLink($link) {
    $this->link = $link;
  }


  /**
	 * Checks if user has clicked register a new user.
	 * @return bool 
	 */
	public function userClicksRegisterLink() : bool {
		return isset($_GET[self::$registerLink]);
	}
  
  public function render($isLoggedIn, LoginView $v, DateTimeView $dtv) {
    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Login Example</title>
        </head>
        <body>
          <h1>Assignment 2</h1>

          ' . $this->link . '

          ' . $this->renderIsLoggedIn($isLoggedIn) . '
          
          <div class="container">
              ' . $v->response($isLoggedIn) . '
              
              ' . $dtv->show() . '
          </div>
         </body>
      </html>
    ';
  }

  private function renderIsLoggedIn($isLoggedIn) {
    if ($isLoggedIn) {
      return '<h2>Logged in</h2>';
    }
    else {
      return '<h2>Not logged in</h2>';
    }
  }
}
