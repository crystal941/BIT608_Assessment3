<!DOCTYPE HTML>
<html>

<head>
    <title>Delete Food Item</title>
</head>
<?php
include "header.php";
include "menu.php";
?>

<body>
    <div id="body">
        <div class="header">
            <div>
                <h1>Food item details preview before deletion</h1>
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

                //retrieve the itemid from the URL
                if ($_SERVER["REQUEST_METHOD"] == "GET") {
                    $id = $_GET['id'];
                    if (empty($id) or !is_numeric($id)) {
                        echo "<h2>Invalid Food Item ID</h2>"; //simple error feedback
                        exit;
                    }
                }

                //the data was sent using a formtherefore we use the $_POST instead of $_GET
                //check if we are saving data first by checking if the submit button exists in the array
                if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {
                    $error = 0; //clear our error flag
                    $msg = 'Error: ';
                    //itemID (sent via a form it is a string not a number so we try a type conversion!)    
                    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
                        $id = cleanInput($_POST['id']);
                    } else {
                        $error++; //bump the error flag
                        $msg .= 'Invalid Food Item ID '; //append error message
                        $id = 0;
                    }

                    //save the food item data if the error flag is still clear and food item id is > 0
                    if ($error == 0 and $id > 0) {
                        $query = "DELETE FROM fooditems WHERE itemID=?";
                        $stmt = mysqli_prepare($DBC, $query); //prepare the query
                        mysqli_stmt_bind_param($stmt, 'i', $id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                        echo "<h2>Food item details deleted.</h2>";
                    } else {
                        echo "<h2>$msg</h2>" . PHP_EOL;
                    }
                }

                //prepare a query and send it to the server
                //NOTE for simplicity purposes ONLY we are not using prepared queries
                //make sure you ALWAYS use prepared queries when creating custom SQL like below
                $query = 'SELECT * FROM fooditems WHERE itemid=' . $id;
                $result = mysqli_query($DBC, $query);
                $rowcount = mysqli_num_rows($result);
                ?>

                <h2><a href='listitems.php'>[Return to the Food item listing]</a><a href='/pizza/'>[Return to the main page]</a></h2>
                <?php
                //makes sure we have the food item
                if ($rowcount > 0) {
                    echo "<fieldset><legend>Food Item details #$id</legend><dl>";
                    $row = mysqli_fetch_assoc($result);
                    echo "<dt>Pizza name:</dt><dd>" . $row['pizza'] . "</dd>" . PHP_EOL;
                    echo "<dt>Description:</dt><dd>" . $row['description'] . "</dd>" . PHP_EOL;
                    $pt = $row['pizzatype'] == 'S' ? 'Standard' : 'Vegeterian';
                    echo "<dt>Pizza type:</dt><dd>" . $pt . "</dd>" . PHP_EOL;
                    echo "<dt>Price:</dt><dd>" . $row['price'] . "</dd>" . PHP_EOL;
                    echo '</dl></fieldset>' . PHP_EOL;
                ?><form method="POST" action="deleteitem.php">
                        <h2>Are you sure you want to delete this Food item?</h2>
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="submit" name="submit" value="Delete">
                        <a href="listitems.php">[Cancel]</a>
                    </form>
                <?php
                } else echo "<h2>No Food Item found, possbily deleted!</h2>"; //suitable feedback
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