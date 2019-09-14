<?php

class LayoutView {

  private $href;
  private $linkText;

  public function setLinkRegister() {
    $this->href = "?register";
    $this->linkText = "Register a new user";
  }

  public function setLinkGoBack() {
    $this->href = "?";
    $this->linkText = "Back to login";
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

          <a href= ' . $this->href . '>' . $this->linkText . '</a>

          ' . $this->renderIsLoggedIn($isLoggedIn) . '
          
          <div class="container">
              ' . $v->response() . '
              
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
