<?php
session_start();

//function to check if the user is logged else send to the login page 
function checkUser()
{
  $_SESSION['URI'] = '';
  if ($_SESSION['loggedin'] == 1) {
    return TRUE;
  } else {
    $_SESSION['URI'] = 'http://localhost' . $_SERVER['REQUEST_URI']; //save current url for redirect     
    header('Location: http://localhost/pizza/login.php', true, 303);
  }
}

//to show if we are logged in
function loginStatus()
{
  if (empty($_SESSION['loggedin']) or $_SESSION['loggedin'] !== 1) {
    echo "<h2>You haven't logged in.</h2>";
  } else {
    $un = $_SESSION['email'];
    echo "<h2>Logged in as $un</h2>";
  }
}

//log a user in
function login($id, $email, $role)
{
  //simple redirect if a user tries to access a page they have not logged in to
  if ($_SESSION['loggedin'] == 0 and !empty($_SESSION['URI']))
    $uri = $_SESSION['URI'];
  else {
    $_SESSION['URI'] =  'http://localhost/pizza/listorders.php';
    $uri = $_SESSION['URI'];
  }

  $_SESSION['role'] = $role;
  $_SESSION['loggedin'] = 1;
  $_SESSION['customerid'] = $id;
  $_SESSION['email'] = $email;
  $_SESSION['URI'] = '';
  header('Location: ' . $uri, true, 303);
}

//simple logout function
function logout()
{
  $_SESSION['role'] = 0;
  $_SESSION['loggedin'] = 0;
  $_SESSION['customerid'] = -1;
  $_SESSION['email'] = '';
  $_SESSION['URI'] = '';
  header('Location: http://localhost/pizza/login.php', true, 303);
}
?>