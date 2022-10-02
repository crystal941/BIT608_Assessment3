<!DOCTYPE HTML>
<html>

<head>
    <title>Browse Bookings</title>
</head>

<body>
    <?php
    include "header.php";
    include "menu.php";
    ?>
    <div id="body">
        <div class="header">
            <div>
                <h1>Current Bookings</h1>
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
                    //prepare a query with all bookings for admin
                    $query = 'SELECT bookingID, bookingdate, people, telephone, firstname, lastname 
                    FROM booking, customer
                    WHERE booking.customerID = customer.customerID
                    ORDER BY lastname';
                } else {
                    //prepare a query with only customer's orders
                    $query = "SELECT bookingID, bookingdate, people, telephone, firstname, lastname
                    FROM booking
                    INNER JOIN customer ON booking.customerID = customer.customerID
                    WHERE customer.customerID = $id  
                    ORDER BY lastname";
                }
                $result = mysqli_query($DBC, $query);
                $rowcount = mysqli_num_rows($result);
                ?>

                <h2><a href='addbooking.php'>[Make a booking]</a><a href='/pizza/'>[Return to main page]</a></h2>
                <table border="1">
                    <thread>
                        <tr>
                            <th>Booking (date & time, people)</th>
                            <th>Customer (Telephone)</th>
                            <th>Action</th>
                        </tr>
                    </thread>

                    <?php
                    //make sure there is any order
                    if ($rowcount > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['bookingID'];
                            echo '<td>' . $row['bookingdate'] . ' (' . $row['people'] . ')</td>';
                            echo '<td>' . $row['lastname'] . ', ' . $row['firstname'] . ' (T: ' . $row['telephone'] . ')</td>';
                            echo '<td><a href="viewbooking.php?id=' . $id . '">[View]</a>';
                            echo '<a href ="editbooking.php?id=' . $id . '">[Edit]</a>';
                            echo '<a href ="deletebooking.php?id=' . $id . '">[Delete]</a></td>';
                            echo '</tr>' . PHP_EOL;
                        }
                    } else echo '<h2>No bookings found!</h2>'; //suitable feedback
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