<!DOCTYPE HTML>
<html>

<head>
    <title>Update Order</title>
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
                <h1>Order details update</h1>
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
                        echo "<h2>Invalid Order ID</h2>"; //simple error feedback
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

                //function to get select options from the database
                function getSelect()
                {
                    $query = "SELECT pizza FROM fooditems";
                    global $DBC;
                    $result = mysqli_query($DBC, $query);
                    $code = '<select name = "orderitems[]"><option>None</option>\n';
                    while ($row = mysqli_fetch_array($result)) {
                        $code .= "<option>" . $row['pizza'] . "</option>\n";
                    }
                    $code .= '</select>';
                    return $code;
                }

                //the data was sent using a form, therefore we use the $_POST instead of $_GET
                //check if we are saving data first by checking if the submit button exists in the array
                if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {
                    $error = 0; //clear our error flag
                    $msg = 'Error: ';

                    //orderID (sent via a form it is a string not a number so we try a type conversion!)    
                    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
                        $id = cleanInput($_POST['id']);
                    } else {
                        $error++; //bump the error flag
                        $msg .= 'Invalid Order ID '; //append error message
                        $id = 0;
                    }

                    //validate the date to see if the order is prior to the current date
                    date_default_timezone_set('Pacific/Auckland'); //set server timezone
                    $time = date('Y-m-d H:i:s'); //get current time
                    $query = 'SELECT ordertime FROM orders WHERE orderID=' . $id;
                    $result = mysqli_query($DBC, $query);
                    $row = mysqli_fetch_assoc($result);
                    $ordertime = $row['ordertime']; //get order time for selected order

                    //provide error message if the order time is prior the current time
                    if ($time > $ordertime) {
                        $error++; //bump the error flag
                        $msg .= 'The order is not available for update!';
                    } else {

                        //validate incoming data if the order time is after the current time
                        //ordertime
                        if (isset($_POST['ordertime']) and !empty($_POST['ordertime'])) {
                            $newordertime = $_POST['ordertime']; //the datetime format and content will be valid as a date picker is used
                            //validate the date to see if the order is prior to the current date
                            if ($newordertime < $time) {
                                $error++; //bump the error flag
                                $msg .= 'Invalid order time selected;  '; //append error message
                                $newordertime = '';
                            }
                        }

                        //extras
                        if (isset($_POST['extras']) and !empty($_POST['extras']) and is_string($_POST['extras'])) {
                            $fn = cleanInput($_POST['extras']);
                            $newextras = (strlen($fn) > 500) ? substr($fn, 1, 500) : $fn; //check length and clip if too big   
                        }

                        //give an error message if none of fields was filled for update 
                        if (empty($_POST['ordertime']) and array_unique($_POST['orderitems']) === array('None') and empty($_POST['extras'])) {
                            $error++; //bump the error flag
                            $msg .= 'Nothing has been selected to update this order!'; //append eror message
                        }
                    }

                    //save the order data if the error flag is still clear and order id is > 0
                    if ($error == 0 and $id > 0) {
                        //process the orderitems and quantity for update
                        if (!(array_unique($_POST['orderitems']) === array('None'))) {
                            $customerid = $_SESSION['customerid']; //get customerID
                            $sql = "SELECT itemID, pizza FROM fooditems"; //prepare the query
                            $result = mysqli_query($DBC, $sql);
                            while ($row = mysqli_fetch_array($result)) {
                                $pizza[] = $row['pizza']; //get pizza list from database
                                $pizzaid[] = $row['itemID']; //get itemid of the pizza from database
                            }

                            //delete the old rows in orderlines table
                            $query1 = "DELETE FROM orderlines WHERE orderID=?";
                            $stmt1 = mysqli_prepare($DBC, $query1); //prepare the query
                            mysqli_stmt_bind_param($stmt1, 'i', $id);
                            mysqli_stmt_execute($stmt1);
                            mysqli_stmt_close($stmt1);

                            //insert new rows into orderlines table
                            //use a for loop to iterate the array ordertimes and quantity
                            for ($a = 0; $a < count($_POST['orderitems']); $a++) {
                                if ($_POST['orderitems'][$a] !== "None" && $_POST['quantity'][$a] > 0) {
                                    $i = array_search($_POST['orderitems'][$a], $pizza);
                                    $itemid = $pizzaid[$i]; //find the itemid of the selected pizza
                                    $query2 = "INSERT INTO orderlines (orderID, itemID, quantity) VALUES (?,?,?)"; //prepare a query
                                    $stmt2 = mysqli_prepare($DBC, $query2);
                                    mysqli_stmt_bind_param($stmt2, 'iii', $id, $itemid, $_POST['quantity'][$a]);
                                    mysqli_stmt_execute($stmt2);
                                    mysqli_stmt_close($stmt2);
                                }
                            }
                        }

                        //process the ordertime for update
                        if (isset($newordertime) and !empty($newordertime)) {
                            $query3 = "UPDATE orders SET ordertime=? WHERE orderID = $id";
                            $stmt3 = mysqli_prepare($DBC, $query3);
                            mysqli_stmt_bind_param($stmt3, 's', $newordertime);
                            mysqli_stmt_execute($stmt3);
                            mysqli_stmt_close($stmt3);
                        }

                        //process the extras message for update
                        if (isset($newextras) and !empty($newextras) and is_string($newextras)) {
                            $query4 = "UPDATE orders SET extras=? WHERE orderID = $id";
                            $stmt4 = mysqli_prepare($DBC, $query4);
                            mysqli_stmt_bind_param($stmt4, 's', $newextras);
                            mysqli_stmt_execute($stmt4);
                            mysqli_stmt_close($stmt4);
                        }
                        echo "<h2>Order #$id has been updated!</h2>";
                    } else {
                        echo "<h2>$msg</h2>" . PHP_EOL;
                    }
                }
                //locate the order to edite by using the orderID
                $query = 'SELECT ordertime, extras FROM orders WHERE orderID =' . $id;
                $result = mysqli_query($DBC, $query);
                $rowcount = mysqli_num_rows($result);
                if ($rowcount > 0) {
                    $row = mysqli_fetch_assoc($result);
                ?>

                    <h2><a href="listorders.php">[Return to the Orders listing]</a><a href="/pizza/">[Return to the main page]</a></h2>

                    <form action="editorder.php" method="POST">
                        <lable for="ODATE">Order for (date & time):</lable>
                        <input type="datetime" name="ordertime" id="ordertime" placeholder="yyyy-mm-dd HH:MM" value="<?php echo $row['ordertime']; ?>" required><br><br>
                        <Label for="EXTRAS">Extras:</Label>
                        <input type="text" name="extras" id="extras" maxlength="500" size="50" placeholder="Any extra you would like to add to your order" value="<?php echo $row['extras']; ?>"><br><br>
                        <hr>
                        <p style="font-weight: bold;">Pizzas for this order:</p>
                        <table id="tblpizzas" border="1">
                            <tr>
                                <th>#</th>
                                <th>Pizza</th>
                                <th>Quantity</th>
                            </tr>
                            <tbody id="tblitems">
                                <?php
                                //a for loop to generate 10 lines for the order
                                for ($a = 0; $a < 10; $a++) {
                                    $lineid = $a + 1;
                                    $code = '<tr><td>' . $lineid . '</td><td>';
                                    $code .= getSelect();
                                    $code .= '</select><td>';
                                    $code .= "<input type='number' name='quantity[]' value='1' step='1' min='1' max='100' required></td></tr>";
                                    echo $code;
                                }
                                ?>
                            </tbody>
                        </table>
                        <p>
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <input type="submit" name="submit" value="Update"> <a href="listorders.php">[Cancel]</a>
                        </p>
                    </form>
                <?php
                } else {
                    echo "<h2>Order is not found with that ID!</h2>"; //simple error feedback
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
        flatpickr("input[id=ordertime]", config);
    </script>

</body>
<?php
include "footer.php"
?>

</html>