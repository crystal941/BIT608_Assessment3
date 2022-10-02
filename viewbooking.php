<!DOCTYPE HTML>
<html>

<head>
    <title>View Booking</title>
</head>

<body>
    <?php
    include "header.php";
    include "menu.php";
    ?>
    <div id="body">
        <div class="header">
            <div>
                <h1>Booking Details View</h1>
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
                //retrieve the booking id from the URL
                if ($_SERVER["REQUEST_METHOD"] == "GET") {
                    if (empty($_GET['id']) or !is_numeric($_GET['id'])) {
                        echo "<h2>Invalid Booking ID</h2>"; //simple error feedback
                        exit;
                    } else {
                        $id = $_GET['id'];
                    }
                }

                //prepare a query and send it to the server
                $query = 'SELECT bookingID, bookingdate, people, telephone, firstname, lastname
                FROM booking
                INNER JOIN customer ON customer.customerID = booking.customerID
                WHERE bookingID =' . $id;
                $result = mysqli_query($DBC, $query);
                $rowcount = mysqli_num_rows($result);
                ?>

                <h2><a href='listbookings.php'>[Return to the Bookings listing]</a><a href='/pizza/'>[Return to the main page]</a></h2>

                <?php
                //make sure there is any booking item
                if ($rowcount > 0) {
                    echo "<fieldset><legend>Booking detail #$id</legend><dl>";
                    $row = mysqli_fetch_assoc($result);
                    echo "<dt>Booking date & time</dt><dd>" . $row['bookingdate'] . "</dd>" . PHP_EOL;
                    echo "<dt>Customer name:</dt><dd>" . $row['lastname'] . ", " . $row['firstname'] . "</dd>" . PHP_EOL;
                    echo "<dt>Party size:</dt><dd>" . $row['people'] . "</dd>" . PHP_EOL;
                    echo "<dt>Contact number:</dt><dd>" . $row['telephone'] . "</dd>" . PHP_EOL;
                    echo '</dl></fieldsey>' . PHP_EOL;
                } else echo "<h2>No bookings found!</h2>"; //suitable feedback
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