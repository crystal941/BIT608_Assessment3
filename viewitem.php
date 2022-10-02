<!DOCTYPE HTML>
<html>

<head>
    <title>View Food items</title>
</head>

<body>
    <?php
    include "header.php";
    include "menu.php";
    ?>
    <div id="body">
        <div class="header">
            <div>
                <h1>Food item Details View</h1>
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

                //do some simple validation to check if id exists
                $id = $_GET['id'];
                if (empty($id) or !is_numeric($id)) {
                    echo "<h2>Invalid Food item ID</h2>"; //simple error feedback
                    exit;
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

                //makes sure we have the Food Item
                if ($rowcount > 0) {
                    echo "<fieldset><legend>Food item detail #$id</legend><dl>";
                    $row = mysqli_fetch_assoc($result);
                    echo "<dt>Pizza name:</dt><dd>" . $row['pizza'] . "</dd>" . PHP_EOL;
                    echo "<dt>Description:</dt><dd>" . $row['description'] . "</dd>" . PHP_EOL;
                    $pt = $row['pizzatype'] == 'S' ? 'Standard' : 'Vegeterian';
                    echo "<dt>Pizza type:</dt><dd>" . $pt . "</dd>" . PHP_EOL;
                    echo "<dt>Price:</dt><dd>" . $row['price'] . "</dd>" . PHP_EOL;
                    echo '</dl></fieldset>' . PHP_EOL;
                } else echo "<h2>No Food Item found!</h2>"; //suitable feedback

                mysqli_free_result($result); //free any memory used by the query
                mysqli_close($DBC); //close the connection once done
                ?>
                </table>
            </div>
        </div>
    </div>
</body>
<?php
include "footer.php";
?>

</html>