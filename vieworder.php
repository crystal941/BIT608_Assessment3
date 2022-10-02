<!DOCTYPE HTML>
<html>

<head>
    <title>View Order</title>
</head>

<body>
    <?php
    include "header.php";
    include "menu.php";
    ?>
    <div id="body">
        <div class="header">
            <div>
                <h1>Pizza Order Details View</h1>
            </div>
        </div>
        <div class="body">
            <div>
                <?php
                include_once "checksession.php";
                checkUser(); //check if user logged in
                loginStatus(); //show the current login status
                include "config.php";
                $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

                //check if the connection was good
                if (mysqli_connect_errno()) {
                    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
                    exit; //stop processing the page further
                }

                //simple validation to check if id exists
                //retrieve the order id from the URL
                if ($_SERVER["REQUEST_METHOD"] == "GET") {
                    if (empty($_GET['id']) or !is_numeric($_GET['id'])) {
                        echo "<h2>Invalid Order ID</h2>"; //simple error feedback
                        exit;
                    } else {
                        $id = $_GET['id'];
                    }
                }

                //prepare a query and send it to the server
                $query = 'SELECT orders.orderID, ordertime, firstname, lastname, extras, pizza, quantity
                FROM customer
                INNER JOIN orders ON customer.customerID = orders.customerID
                INNER JOIN orderlines ON orders.orderID = orderlines.orderID
                INNER JOIN fooditems ON  orderlines.itemID = fooditems.itemID
                WHERE orders.orderID=' . $id;
                $result = mysqli_query($DBC, $query);
                $rowcount = mysqli_num_rows($result);
                ?>

                <h2><a href='listorders.php'>[Return to the orders listing]</a><a href='/pizza/'>[Return to the main page]</a></h2>

                <?php
                //make sure there is any order item
                if ($rowcount > 0) {
                    echo "<fieldset><legend>Pizza order detail for order #$id</legend><dl>";

                    //prepare an array for the order items
                    $order = array();
                    while ($row = mysqli_fetch_assoc($result)) {
                        $order[] = $row;
                    }

                    //show once for the order time, customer name, and pizza extras
                    echo "<dt>Date & time ordered for:</dt><dd>" . $order[0]['ordertime'] . "</dd>" . PHP_EOL;
                    echo "<dt>Customer name:</dt><dd>" . $order[0]['lastname'] . ", " . $order[0]['firstname'] . "</dd>" . PHP_EOL;
                    echo "<dt>Extras:</dt><dd>" . $order[0]['extras'] . "</dd>" . PHP_EOL;
                    echo "<dt>Pizzas:</dt>";

                    //iterate of each row of pizza and quantity
                    foreach ($order as $row) {
                        echo "<dd>" . $row['pizza'] . " X " . $row['quantity'] . "</dd>";
                    }
                    echo '</dl></fieldsey>' . PHP_EOL;
                } else echo "<h2>No order items found!</h2>"; //suitable feedback

                mysqli_free_result($result); //free any memory used by the query
                mysqli_close($DBC); //close the connecton once done
                ?>
            </div>
        </div>
    </div>
</body>
<?php
include "footer.php";
?>

</html>