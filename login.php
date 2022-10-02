<!DOCTYPE HTML>
<html>

<head>
    <title>Login</title>
</head>

<body>
    <?php
    include "header.php";
    include "menu.php";
    ?>
    <div id="body">
        <div class="header">
            <div>
                <h1>Login</h1>
            </div>
        </div>
        <div class="body">
            <div>
                <?php
                include_once "checksession.php";
                loginStatus(); //show the current login status

                //simple logout
                if (isset($_POST['logout'])) {
                    logout();
                }

                if (isset($_POST['login']) and !empty($_POST['login']) and ($_POST['login'] == 'Login')) {
                    include "config.php";
                    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE) or die();

                    //validate incoming data
                    $error = 0; //clear our error flag
                    $msg = 'Error: ';

                    //email
                    if (isset($_POST['email']) and !empty($_POST['email']) and is_string($_POST['email'])) {
                        $un = htmlspecialchars(stripslashes(trim($_POST['email'])));
                        $email = (strlen($un) > 50) ? substr($un, 1, 50) : $un;
                    } else {
                        $error++; //bump the error flag
                        $msg .= 'Invalid email! '; //append error message
                        $email = '';
                    }

                    //password
                    if (isset($_POST['password']) and !empty($_POST['password']) and is_string($_POST['password'])) {
                        $password = trim($_POST['password']);
                    } else {
                        $error++;
                        $msg .= 'Invalid password! ';
                        $password = '';
                    }

                    //prepare a query
                    if ($error == 0) {
                        $query = "SELECT customerID, password, role FROM customer WHERE email = '$email'";
                        $result = mysqli_query($DBC, $query);
                        if (mysqli_num_rows($result) == 1) { //found the user
                            $row = mysqli_fetch_assoc($result);

                            if ($password === $row['password']) //plaintext password as it is not hashed before store 
                                login($row['customerID'], $email, $row['role']);
                            mysqli_free_result($result); //free any memory used by the query
                            mysqli_close($DBC); //close the connection once done
                        }
                        echo "<h2>Login fail</h2>" . PHP_EOL;
                    } else {
                        echo "<h2>$msg</h2>" . PHP_EOL;
                    }
                }
                ?>
                <form method="POST" action="login.php">
                    <p>
                        <label for="email">Email: </label>
                        <input type="text" id="email" name="email" maxlength="50">
                    </p>
                    <p>
                        <label for="password">Password: </label>
                        <input type="password" id="password" name="password" maxlength="32">
                    </p>

                    <input type="submit" name="login" value="Login">
                    <input type="submit" name="logout" value="Logout">
                </form>
            </div>
        </div>
    </div>
</body>
<?php
include "footer.php";
?>

</html>