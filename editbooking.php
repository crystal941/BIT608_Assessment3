<!DOCTYPE HTML>
<html>

<head>
    <title>Update Booking</title>
    <!-- flatpickr-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body>
    <?php
    include "header.php";
    include "menu.php";
    ?>
    <div id="body">
        <div class="header">
            <div>
                <h1>Edit a Booking</h1>
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
                };

                //simple validation to check if id exists
                //retrieve the order id from the URL
                if ($_SERVER["REQUEST_METHOD"] == "GET") {
                    if (empty($_GET['id']) or !is_numeric($_GET['id'])) {
                        echo "<h2>Invalid Booking ID</h2>"; //simple error feedback
                        exit;
                    } else {
                        $id = $_GET['id'];
                    }
                }

                //function to clean input but not validate type and content
                function cleanInput($data)
                {
                    return htmlspecialchars(stripslashes(trim($data)));
                }

                //the data was sent using a form, therefore we use the $_POST instead of $_GET
                //check if we are saving data first by checking if the submit button exists in the array
                if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {
                    //validate incoming data
                    $error = 0; //clear our error flag
                    $msg = 'Error: ';

                    //orderID (sent via a form it is a string not a number so we try a type conversion!)    
                    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
                        $id = cleanInput($_POST['id']);
                    } else {
                        $error++; //bump the error flag
                        $msg .= 'Invalid Booking ID '; //append error message
                        $id = 0;
                    }

                    //validate the date to see if the order is prior to the current date
                    date_default_timezone_set('Pacific/Auckland'); //set server timezone
                    $time = date('Y-m-d H:i:s'); //get current time
                    $query = 'SELECT bookingdate FROM booking WHERE bookingID=' . $id;
                    $result = mysqli_query($DBC, $query);
                    $row = mysqli_fetch_assoc($result);
                    $booktime = $row['bookingdate']; //get order time for selected order

                    //provide error message if the order time is prior the current time
                    if ($time > $booktime) {
                        $error++; //bump the error flag
                        $msg .= 'The booking is not available for edit!';
                    } else {

                        //validate incoming data if the order time is after the current time
                        //ordertime
                        if (isset($_POST['booktime']) and !empty($_POST['booktime'])) {
                            $newbooktime = $_POST['booktime']; //the datetime format and content will be valid as a date picker is used
                            //validate the date to see if the order is prior to the current date
                            if ($newbooktime < $time) {
                                $error++; //bump the error flag
                                $msg .= 'Invalid booking time selected;  '; //append error message
                                $newbooktime = '';
                            }
                        }

                        //contact number
                        if (isset($_POST['phone']) and !empty($_POST['phone']) and is_string($_POST['phone'])) {
                            $phone = cleanInput($_POST['phone']);
                        } else {
                            $error++; //bump the error flag
                            $msg .= 'Invalid contact number;  ';
                            $phone = '';
                        }

                        //party size
                        if (isset($_POST['psize']) and !empty($_POST['psize']) and is_int($_POST['psize'] + 0)) {
                            $size = cleanInput($_POST['psize']) + 0;
                            if ($size < 1 or $size > 10) $size = 1;
                        } else {
                            $error++; //bump the error flag
                            $msg .= 'Invalid party size selected; ';
                            $size = '';
                        } //append eror message

                        //give an error message if none of fields was filled for edit 
                        if (empty($_POST['booktime']) and empty($_POST['phone']) and empty($_POST['psize'])) {
                            $error++; //bump the error flag
                            $msg .= 'Nothing for editing this order!'; //append eror message
                        }
                    }

                    //save the order data if the error flag is still clear and booking id is > 0
                    if ($error == 0 and $id > 0) {
                        $query = "UPDATE booking SET bookingdate=?, people=?, telephone=? WHERE bookingID=?";
                        $stmt = mysqli_prepare($DBC, $query); //prepare the query
                        mysqli_stmt_bind_param($stmt, 'sisi', $newbooktime, $size, $phone, $id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                        echo "<h2>Booking #$id has been edited!</h2>";
                    } else {
                        echo "<h2>$msg</h2>" . PHP_EOL;
                    }
                }
                //locate the booking to edit by using the bookingID
                //we also include the bookingID in our form for sending it back for saving the data
                $query = 'SELECT bookingdate, telephone, people FROM booking WHERE bookingID=' . $id;
                $result = mysqli_query($DBC, $query);
                $rowcount = mysqli_num_rows($result);
                if ($rowcount > 0) {
                    $row = mysqli_fetch_assoc($result);
                ?>

                    <h2><a href="listbookings.php">[Return to the Bookings listing]</a><a href="/pizza/">[Return to the main page]</a></h2>
                    <form action="editbooking.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <lable for="BDATE">Booking date & time:</lable>
                        <input type="datetime" name="booktime" id="booktime" placeholder="yyyy-mm-dd HH:MM" value="<?php echo $row['bookingdate']; ?>" required><br><br>
                        <label for="SIZE">Party size (# people, 1-10):</label>
                        <input type="number" name="psize" id="psize" min="1" max="10" step="1" value="<?php echo $row['people']; ?>" required><br><br>
                        <label for="phone">Contact number:</label>
                        <input type="text" name="phone" id="phone" placeholder="###-###-####" pattern="[0-9]{3}[- ]*[0-9]{3}[- ]*[0-9]{4}" value="<?php echo $row['telephone']; ?>" required>
                        <p>
                            <input type="submit" name="submit" value="Update"> <a href="listbookings.php">[Cancel]</a>
                        </p>
                    </form>
                <?php
                } else {
                    echo "<h2>Booking is not found with that ID!</h2>"; //simple error feedback
                }
                mysqli_free_result($result); //free any memory used by the query
                mysqli_close($DBC); //close the connection once done
                ?>
            </div>
        </div>
    </div>
    <!-- flatpickr -->
    <script>
        config = {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            minDate: "today", //ordertime cannot be prior to current date
            maxDate: new Date().fp_incr(365) //ordertime cannot be made further than a year
        }
        flatpickr("input[id=booktime]", config);
    </script>
</body>
<?php
include "footer.php"
?>

</html>