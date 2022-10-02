<!DOCTYPE HTML>
<html>

<head>
  <title>Register new customer</title>
</head>

<body>
  <?php
  include "header.php";
  include "menu.php";
  ?>

  <div id="body">
    <div class="header">
      <div>
        <h1>New Customer Registration</h1>
      </div>
    </div>
    <div class="body">
      <div>
        <?php
        include_once "checksession.php";
        checkUser(); //check if user logged in
        loginStatus(); //show the current login status

        //function to clean input but not validate type and content
        function cleanInput($data)
        {
          return htmlspecialchars(stripslashes(trim($data)));
        }

        //the data was sent using a formtherefore we use the $_POST instead of $_GET
        //check if we are saving data first by checking if the submit button exists in the array
        if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Register')) {
          //if ($_SERVER["REQUEST_METHOD"] == "POST") { //alternative simpler POST test    
          include "config.php"; //load in any variables
          $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

          if (mysqli_connect_errno()) {
            echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
            exit; //stop processing the page further
          };

          //validate incoming data - only the first field is done for you in this example - rest is up to you do
          //firstname
          $error = 0; //clear our error flag
          $msg = 'Error: ';
          if (isset($_POST['firstname']) and !empty($_POST['firstname']) and is_string($_POST['firstname'])) {
            $fn = cleanInput($_POST['firstname']);
            $firstname = (strlen($fn) > 50) ? substr($fn, 1, 50) : $fn; //check length and clip if too big
            //we would also do context checking here for contents, etc       
          } else {
            $error++; //bump the error flag
            $msg .= 'Invalid firstname '; //append eror message
            $firstname = '';
          }
          //lastname
          if (isset($_POST['lastname']) and !empty($_POST['lastname']) and is_string($_POST['lastname'])) {
            $fn = cleanInput($_POST['lastname']);
            $lastname = (strlen($fn) > 50) ? substr($fn, 1, 50) : $fn; //check length and clip if too big
          } else {
            $error++;
            $msg .= 'Invalid last name; '; //append error message
            $lastname = '';
          }
          //email
          if (isset($_POST['email']) and !empty($_POST['email']) and is_string($_POST['email'])) {
            $fn = cleanInput($_POST['email']);
            $email = (strlen($fn) > 100) ? substr($fn, 1, 100) : $fn; //check length and clip if too big
          } else {
            $error++;
            $msg .= 'Invalid email; '; //append error message
            $email = '';
          }

          //password    
          if (isset($_POST['password']) and !empty($_POST['password']) and is_string($_POST['password'])) {
            $fn = cleanInput($_POST['password']);
            $password = (strlen($fn) > 32) ? substr($fn, 1, 32) : $fn; //check length and clip if too big
          } else {
            $error++;
            $msg .= 'Invalid password; '; //append error message
            $password = '';
          }

          //save the customer data if the error flag is still clear
          if ($error == 0) {
            $query = "INSERT INTO customer (firstname,lastname,email,password) VALUES (?,?,?,?)";
            $stmt = mysqli_prepare($DBC, $query); //prepare the query
            mysqli_stmt_bind_param($stmt, 'ssss', $firstname, $lastname, $email, $password);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo "<h2>Customer saved!</h2>";
          } else {
            echo "<h2>$msg</h2>" . PHP_EOL;
          }
          mysqli_close($DBC); //close the connection once done
        }
        ?>

        <h2><a href='listcustomers.php'>[Return to the Customer listing]</a><a href='/pizza/'>[Return to the main page]</a></h2>

        <form method="POST" action="addcustomer.php">
          <p>
            <label for="firstname">First Name: </label>
            <input type="text" id="firstname" name="firstname" minlength="1" maxlength="50" required>
          </p>
          <p>
            <label for="lastname">Last Name: </label>
            <input type="text" id="lastname" name="lastname" minlength="1" maxlength="50" required>
          </p>
          <p>
            <label for="email">Email: </label>
            <input type="email" id="email" name="email" maxlength="100" size="50" required>
          </p>
          <p>
            <label for="password">Password: </label>
            <input type="password" id="password" name="password" minlength="8" maxlength="32" required>
          </p>

          <input type="submit" name="submit" value="Register">
          <a href="listcustomers.php">[Cancel]</a>
        </form>
      </div>
    </div>
  </div>
</body>
<?php
include "footer.php"
?>

</html>