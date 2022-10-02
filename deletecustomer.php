<!DOCTYPE HTML>
<html>

<head>
    <title>View Customer</title>
</head>

<body>
    <?php
    include "header.php";
    include "menu.php";
    ?>
    <div id="body">
        <div class="header">
            <div>
                <h1>Customer details preview before deletion</h1>
            </div>
        </div>
        <div class="body">
            <div>
                <?php
                include_once "checksession.php";
                checkUser(); //check if user logged in
                loginStatus(); //show the current login status
                include "config.php"; //load in any variables
                $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

                //insert DB code from here onwards
                //check if the connection was good
                if (mysqli_connect_errno()) {
                    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
                    exit; //stop processing the page further
                }

                //function to clean input but not validate type and content
                function cleanInput($data)
                {
                    return htmlspecialchars(stripslashes(trim($data)));
                }

                //retrieve the customerid from the URL
                if ($_SERVER["REQUEST_METHOD"] == "GET") {
                    $id = $_GET['id'];
                    if (empty($id) or !is_numeric($id)) {
                        echo "<h2>Invalid Customer ID</h2>"; //simple error feedback
                        exit;
                    }
                }

                //the data was sent using a formtherefore we use the $_POST instead of $_GET
                //check if we are saving data first by checking if the submit button exists in the array
                if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {
                    $error = 0; //clear our error flag
                    $msg = 'Error: ';
                    //customerID (sent via a form it is a string not a number so we try a type conversion!)    
                    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
                        $id = cleanInput($_POST['id']);
                    } else {
                        $error++; //bump the error flag
                        $msg .= 'Invalid Customer ID '; //append error message
                        $id = 0;
                    }

                    //save the customer data if the error flag is still clear and customer id is > 0
                    if ($error == 0 and $id > 0) {
                        $query = "DELETE FROM customer WHERE customerID=?";
                        $stmt = mysqli_prepare($DBC, $query); //prepare the query
                        mysqli_stmt_bind_param($stmt, 'i', $id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                        echo "<h2>Customer details deleted.</h2>";
                    } else {
                        echo "<h2>$msg</h2>" . PHP_EOL;
                    }
                }

                //prepare a query and send it to the server
                //NOTE for simplicity purposes ONLY we are not using prepared queries
                //make sure you ALWAYS use prepared queries when creating custom SQL like below
                $query = 'SELECT * FROM customer WHERE customerid=' . $id;
                $result = mysqli_query($DBC, $query);
                $rowcount = mysqli_num_rows($result);
                ?>

                <h2><a href='listcustomers.php'>[Return to the Customer listing]</a><a href='/pizza/'>[Return to the main page]</a></h2>
                <?php
                //makes sure we have the customer
                if ($rowcount > 0) {
                    echo "<fieldset><legend>Customer detail #$id</legend><dl>";
                    $row = mysqli_fetch_assoc($result);
                    echo "<dt>Name:</dt><dd>" . $row['firstname'] . "</dd>" . PHP_EOL;
                    echo "<dt>Lastname:</dt><dd>" . $row['lastname'] . "</dd>" . PHP_EOL;
                    echo "<dt>Email:</dt><dd>" . $row['email'] . "</dd>" . PHP_EOL;
                    echo "<dt>Password:</dt><dd>" . $row['password'] . "</dd>" . PHP_EOL;
                    echo '</dl></fieldset>' . PHP_EOL;
                ?><form method="POST" action="deletecustomer.php">
                        <h2>Are you sure you want to delete this customer?</h2>
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="submit" name="submit" value="Delete">
                        <a href="listcustomers.php">[Cancel]</a>
                    </form>
                <?php
                } else echo "<h2>No Customer found, possbily deleted!</h2>"; //suitable feedback
                mysqli_free_result($result); //free any memory used by the query
                mysqli_close($DBC); //close the connection once done
                ?>
                </table>
            </div>
        </div>
    </div>
</body>
<?php
include "footer.php"
?>

</html>