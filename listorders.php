<!DOCTYPE HTML>
<html>

<head>
    <title>Browse Orders</title>
</head>

<body>
    <?php
    include "header.php";
    include "menu.php";
    ?>
    <div id="body">
        <div class="header">
            <div>
                <h1>Current Orders</h1>
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

                //check if the user role is admin or customer
                $role = $_SESSION['role'];
                $id = $_SESSION['customerid'];
                if ($role == "admin") {
                    //prepare a query with all orders for admin
                    $query = 'SELECT orderID, ordertime, firstname, lastname 
                    FROM orders, customer
                    WHERE orders.customerID = customer.customerID
                    ORDER BY ordertime';
                } else {
                    //prepare a query with only customer's orders
                    $query = "SELECT orderID, ordertime, firstname, lastname FROM orders
                    INNER JOIN customer ON orders.customerID = customer.customerID
                    WHERE customer.customerID = $id  
                    ORDER BY ordertime";
                }
                $result = mysqli_query($DBC, $query);
                $rowcount = mysqli_num_rows($result);
                ?>

                <h2><a href='addorder.php'>[Place an Order]</a><a href='/pizza/'>[Return to main page]</a></h2>
                <table border="1">
                    <thread>
                        <tr>
                            <th>Orders (Date of order, Order number)</th>
                            <th>Customer (Lastname, Firstname)</th>
                            <th>Action</th>
                        </tr>
                    </thread>

                    <?php
                    //make sure there is any order
                    if ($rowcount > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['orderID'];
                            echo '<tr><td>' . $row['ordertime'] . ' (' . $id . ')</td>';
                            echo '<td>' . $row['lastname'] . ', ' . $row['firstname'] . '</td>';
                            echo '<td><a href="vieworder.php?id=' . $id . '">[View]</a>';
                            echo '<a href ="editorder.php?id=' . $id . '">[Edit]</a>';
                            echo '<a href ="deleteorder.php?id=' . $id . '">[Delete]</a></td>';
                            echo '</tr>' . PHP_EOL;
                        }
                    } else echo '<h2>No orders found!</h2>'; //suitable feedback

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