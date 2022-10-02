<!DOCTYPE HTML>
<html>

<head>
    <title>Delete Booking</title>
</head>

<body>
    <?php
    include "header.php";
    include "menu.php";
    ?>
    <div id="body">
        <div class="header">
            <div>
                <h1>Booking preview before deletion</h1>
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

                //function to clean input but not validate type and content
                function cleanInput($data)
                {
                    return htmlspecialchars(stripslashes(trim($data)));
                }

                //retrieve the booking id from the URL
                if ($_SERVER["REQUEST_METHOD"] == "GET") {
                    if (empty($_GET['id']) or !is_numeric($_GET['id'])) {
                        echo "<h2>Invalid Order ID</h2>"; //simple error feedback
                        exit;
                    } else {
                        $id = $_GET['id'];
                    }
                }

                //the data was sent using a form, therefore we use the $_POST instead of $_GET
                //check if we are saving data first by checking if the submit button exists in the array
                if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {
                    $error = 0; //clear our error flag
                    $msg = 'Error: ';
                    //BookingID (sent via a form it is a string not a number so we try a type conversion!)    
                    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
                        $id = cleanInput($_POST['id']);
                    } else {
                        $error++; //bump the error flag
                        $msg .= 'Invalid Booking ID '; //append error message
                        $id = 0;
                    }

                    //validate the date to see if the booking date is prior to the current date
                    date_default_timezone_set('Pacific/Auckland'); //set server timezone
                    $time = date('Y-m-d H:i:s'); //get current time
                    $query = 'SELECT bookingdate FROM booking WHERE bookingID=' . $id;
                    $result = mysqli_query($DBC, $query);
                    $row = mysqli_fetch_assoc($result);
                    $booktime = $row['bookingdate']; //get order time
                    //provide error message if the order time is prior the current time
                    if ($time > $booktime) {
                        $error++; //bump the error flag
                        $msg .= 'The booking is not available for deletion!';
                    }

                    //save the booking data if the error flag is still clear and order id is > 0
                    if ($error == 0 and $id > 0) {
                        //delete the row in the booking table
                        $query = "DELETE FROM booking WHERE bookingID=?";
                        $stmt = mysqli_prepare($DBC, $query); //prepare the query
                        mysqli_stmt_bind_param($stmt, 'i', $id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                        echo "<h2>The booking was deleted!</h2>";
                    } else {
                        echo "<h2>$msg</h2>" . PHP_EOL;
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
                    echo "<dt>Party size:</dt><dd>" . $row['people'] . "</dd>" . PHP_EOL;
                    echo "<dt>Customer name:</dt><dd>" . $row['lastname'] . ", " . $row['firstname'] . " (T: " . $row['telephone'] . ")</dd>" . PHP_EOL;
                    echo '</dl></fieldsey>' . PHP_EOL; ?>
                    <form method="POST" action="deletebooking.php">
                        <h2>Are you sure you want to delete this Booking?</h2>
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="submit" name="submit" value="Delete">
                        <a href="listbookings.php">[Cancel]</a>
                    </form>

                <?php
                } else echo "<h2>No Booking found, possibly deleted!</h2>"; //suitable feedback
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