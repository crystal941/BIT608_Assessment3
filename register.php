<!DOCTYPE HTML>
<html>

<head>
    <title>Register an Account</title>
</head>

<body>
    <?php
    include "header.php";
    include "menu.php";
    ?>

    <div id="body">
        <div class="header">
            <div>
                <h1>Register</h1>
            </div>
        </div>
        <div class="body">
            <div>
                <?php
                //function to clean input but not validate type and content
                function cleanInput($data)
                {
                    return htmlspecialchars(stripslashes(trim($data)));
                }

                //the data was sent using a form, therefore we use the $_POST instead of $_GET
                //check if we are saving data first by checking if the submit button exists in the array
                if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Register')) {
                    include "config.php";
                    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

                    //check if the connection was good
                    if (mysqli_connect_errno()) {
                        echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
                        exit; //stop processing the page further
                    }

                    //validate incoming data 
                    $error = 0; //clear our error flag
                    $msg = 'Error: ';

                    //email
                    if (isset($_POST['email']) and !empty($_POST['email']) and is_string($_POST['email'])) {
                        $fn = cleanInput($_POST['email']);
                        $email = (strlen($fn) > 50) ? substr($fn, 1, 50) : $fn; //check length and clip if too big
                    } else {
                        $error++;
                        $msg .= 'Invalid email; '; //append error message
                        $email = '';
                    }

                    //firstname
                    if (isset($_POST['firstname']) and !empty($_POST['firstname']) and is_string($_POST['firstname'])) {
                        $fn = cleanInput($_POST['firstname']);
                        $firstname = (strlen($fn) > 25) ? substr($fn, 1, 25) : $fn; //check length and clip if too big
                    } else {
                        $error++;
                        $msg .= 'Invalid first name; '; //append error message
                        $firstname = '';
                    }

                    //lastname
                    if (isset($_POST['lastname']) and !empty($_POST['lastname']) and is_string($_POST['lastname'])) {
                        $fn = cleanInput($_POST['lastname']);
                        $lastname = (strlen($fn) > 25) ? substr($fn, 1, 25) : $fn; //check length and clip if too big
                    } else {
                        $error++;
                        $msg .= 'Invalid last name; '; //append error message
                        $lastname = '';
                    }

                    //password
                    if (isset($_POST['password']) and !empty($_POST['password']) and is_string($_POST['password'])) {
                        $fn = cleanInput($_POST['password']);
                        $password = (strlen($fn) > 50) ? substr($fn, 1, 50) : $fn; //check length and clip if too big
                    } else {
                        $error++;
                        $msg .= 'Invalid password; '; //append error message
                        $password = '';
                    }

                    //save the data if the error flag is still clear
                    if ($error == 0) {
                        $query = "INSERT INTO customer (firstname, lastname, email, password) VALUES (?,?,?,?)"; //prepare the query
                        $stmt = mysqli_prepare($DBC, $query);
                        mysqli_stmt_bind_param($stmt, 'ssss', $firstname, $lastname, $email, $password);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                        echo "<h2>You have successfully registered an account! <br> Please use your email $email to log in. </h2>";
                    } else {
                        echo "<h2>$msg</h2>" . PHP_EOL;
                    }
                    mysqli_close($DBC); //close the database connection once done
                }
                ?>

                <h2>Register a New Account</h2>
                <p>
                <form action="register.php" method="POST">
                    <lable for="email">Enter your email:</lable>
                    <input type="text" name="email" id="email" pattern="\b[\w\.-]+@[\w\.-]+\.\w{2,4}\b" size="50" required><br><br>
                    <label for="firstname">Enter your first name:</label>
                    <input type="text" name="firstname" id="firstname" required><br><br>
                    <label for="lastname">Enter your last name:</label>
                    <input type="text" name="lastname" id="lastname" required><br><br>
                    <label for="password">Enter your password:</label>
                    <input type="text" name="password" id="password" required>
                    <p><input type="submit" name="submit" value="Register"> <a href="/pizza/">[Cancel]</a></p>
                </form>
                </p>
            </div>
        </div>
    </div>
</body>
<?php
include "footer.php"
?>

</html>