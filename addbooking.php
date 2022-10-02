<!DOCTYPE HTML>
<html>
<?php
include "header.php";
include "menu.php";
?>

<head>
    <title>Make a booking</title>
    <!-- flatpickr-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body>
    <div id="body">
        <div class="header">
            <div>
                <h1>Make a booking</h1>
            </div>
        </div>
        <div class="body">
            <div>
                <?php
                include_once "checksession.php";
                checkUser(); //check if user logged in
                loginStatus(); //show the current login status

                //function to clean input but not validate type and content
                function cleanInput($data)
                {
                    return htmlspecialchars(stripslashes(trim($data)));
                }

                //the data was sent using a form, therefore we use the $_POST instead of $_GET
                //check if we are saving data first by checking if the submit button exists in the array
                if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {
                    include "config.php";
                    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

                    //check if the connection was good
                    if (mysqli_connect_errno()) {
                        echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
                        exit; //stop processing the page further
                    }

                    //validate incoming data 
                    $error = 0; //clear our error flag
                    $msg = 'Error: ';

                    //booking time
                    if (isset($_POST['booktime']) and !empty($_POST['booktime'])) {
                        $booktime = $_POST['booktime']; //the datetime format and content will be valid as a date picker is used
                        //validate the date to see if the order is prior to the current date
                        date_default_timezone_set('Pacific/Auckland'); //set server timezone
                        $time = date('Y-m-d H:i:s'); //get current time
                        if ($booktime < $time) {
                            $error++; //bump the error flag
                            $msg .= 'Invalid booking time selected;  '; //append error message
                            $booktime = '';
                        }
                    } else {
                        $error++; //bump the error flag
                        $msg .= 'Please select a booking time;  '; //append eror message
                        $booktime = '';
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

                    //save the item data if the error flag is still clear
                    if ($error == 0) {
                        $customerid = $_SESSION['customerid']; //get customerID
                        $query = "INSERT INTO booking (customerID, bookingdate, telephone, people) VALUES (?,?,?,?)"; //prepare the query
                        $stmt = mysqli_prepare($DBC, $query);
                        mysqli_stmt_bind_param($stmt, 'issi', $customerid, $booktime, $phone, $size);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                        echo "<h2>You have successfully made a booking on $booktime for $size people!</h2>";
                    } else {
                        echo "<h2>$msg</h2>" . PHP_EOL;
                    }
                    mysqli_close($DBC); //close the database connection once done
                }
                ?>

                <h2><a href="listbookings.php">[Return to the Bookings listing]</a><a href="/pizza/">[Return to the main page]</a></h2>

                <form action="addbooking.php" method="POST">
                    <lable for="BDATE">Booking date & time:</lable>
                    <input type="datetime" name="booktime" id="booktime" placeholder="yyyy-mm-dd HH:MM"><br><br>
                    <label for="SIZE">Party size (# people, 1-10):</label>
                    <input type="number" name="psize" id="psize" min="1" max="10" value="1" step="1"><br><br>
                    <label for="phone">Contact number:</label>
                    <input type="text" name="phone" id="phone" placeholder="###-###-####" pattern="[0-9]{3}[- ]*[0-9]{3}[- ]*[0-9]{4}" required>
                    <p><input type="submit" name="submit" value="Add"> <a href="listbookings.php">[Cancel]</a></p>
                </form>
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
include "footer.php";
?>

</html>