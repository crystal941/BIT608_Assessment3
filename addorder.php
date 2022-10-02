<!DOCTYPE HTML>
<html>

<head>
    <title>Place an order</title>

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
                <h1>Place an order</h1>
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

                //function to get select options from the database
                function getSelect()
                {
                    //prepare a query to get pizza items
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

                //function to clean input but not validate type and content
                function cleanInput($data)
                {
                    return htmlspecialchars(stripslashes(trim($data)));
                }

                //the data was sent using a form, therefore we use the $_POST instead of $_GET
                //check if we are saving data first by checking if the submit button exists in the array
                if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Place Order')) {

                    //validate incoming data 
                    $error = 0; //clear our error flag
                    $msg = 'Error: ';

                    //ordertime
                    if (isset($_POST['ordertime']) and !empty($_POST['ordertime'])) {
                        $ordertime = $_POST['ordertime']; //the datetime format and content will be valid as a date picker is used
                        //validate the date to see if the order is prior to the current date
                        date_default_timezone_set('Pacific/Auckland'); //set server timezone
                        $time = date('Y-m-d H:i:s'); //get current time
                        if ($ordertime < $time) {
                            $error++; //bump the error flag
                            $msg .= 'Invalid order time selected;  '; //append error message
                            $ordertime = '';
                        }
                    } else {
                        $error++; //bump the error flag
                        $msg .= 'Please select an order time;  '; //append eror message
                        $ordertime = '';
                    }

                    //extras
                    if (isset($_POST['extras']) and !empty($_POST['extras']) and is_string($_POST['extras'])) {
                        $fn = cleanInput($_POST['extras']);
                        $extras = (strlen($fn) > 500) ? substr($fn, 1, 500) : $fn; //check length and clip if too big   
                    } else {
                        $extras = ''; //extras is default null  
                    }

                    //orderitems
                    if (!isset($_POST['orderitems']) or empty($_POST['orderitems']) or array_unique($_POST['orderitems']) === array('None')) { //make sure there is at least one item selected for the order
                        $error++; //bump the error flag
                        $msg .= 'Please select a pizza for this order;  '; //append eror message
                    }

                    //quantity    
                    if (!isset($_POST['quantity']) or empty($_POST['quantity']) or array_unique($_POST['quantity']) === array('0')) {
                        $error++; //bump the error flag
                        $msg .= 'Invalid quantity selected for this order; ';
                    } //append eror message


                    //save the item data if the error flag is still clear
                    if ($error == 0) {

                        $customerid = $_SESSION['customerid']; //get customerID
                        $query = "SELECT itemID, pizza FROM fooditems"; //prepare the query
                        $result = mysqli_query($DBC, $query);
                        while ($row = mysqli_fetch_array($result)) {
                            $pizza[] = $row['pizza']; //get pizza list from database
                            $pizzaid[] = $row['itemID']; //get itemid of the pizza from database
                        }
                        mysqli_free_result($result); //free any memory used by the query

                        //insert to the orders table
                        $query1 = "INSERT INTO orders (customerID, ordertime, extras) VALUES (?,?,?)"; //prepare the query
                        $stmt1 = mysqli_prepare($DBC, $query1); //prepare the query
                        mysqli_stmt_bind_param($stmt1, 'iss', $customerid, $ordertime, $extras);
                        mysqli_stmt_execute($stmt1);
                        mysqli_stmt_close($stmt1);
                        $orderid = mysqli_insert_id($DBC);

                        //insert to the orderlines table
                        //use a for loop to iterate the array ordertimes and quantity
                        for ($a = 0; $a < count($_POST['orderitems']); $a++) {
                            if ($_POST['orderitems'][$a] !== "None" && $_POST['quantity'][$a] > 0) {
                                $i = array_search($_POST['orderitems'][$a], $pizza);
                                $itemid = $pizzaid[$i]; //find the itemid of the selected pizza
                                $query2 = "INSERT INTO orderlines (orderID, itemID, quantity) VALUES (?,?,?)"; //prepare a query
                                $stmt2 = mysqli_prepare($DBC, $query2);
                                mysqli_stmt_bind_param($stmt2, 'iii', $orderid, $itemid, $_POST['quantity'][$a]);
                                mysqli_stmt_execute($stmt2);
                                mysqli_stmt_close($stmt2);
                            }
                        }
                        echo "<h2>New order #$orderid is placed!</h2>";
                    } else {
                        echo "<h2>$msg</h2>" . PHP_EOL;
                    }
                }
                ?>

                <h2><a href="listorders.php">[Return to the Orders listing]</a><a href="/pizza/">[Return to the main page]</a></h2>

                <form action="addorder.php" method="POST">

                    <lable for="ODATE">Order for (date & time):</lable>
                    <input type="datetime" name="ordertime" id="ordertime" placeholder="yyyy-mm-dd HH:MM"><br><br>
                    <Label for="EXTRAS">Extras:</Label>
                    <input type="text" name="extras" id="extras" maxlength="500" size="50" placeholder="Any extra you would like to add to your order"><br><br>
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
                            mysqli_close($DBC); //close the database connection once done
                            ?>
                        </tbody>
                    </table>
                    <p><input type="submit" name="submit" value="Place Order"> <a href="listorders.php">[Cancel]</a></p>
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
        flatpickr("input[id=ordertime]", config);
    </script>
</body>
<?php
include "footer.php"
?>

</html>