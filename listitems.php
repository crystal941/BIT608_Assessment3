<!DOCTYPE HTML>
<html>

<head>
    <title>Food Item List</title>
</head>

<body>
    <?php
    include "header.php";
    include "menu.php";
    ?>
    <div id="body">
        <div class="header">
            <div>
                <h1>Food Item List</h1>
            </div>
        </div>
        <div class="body">
            <div>
                <?php
                include_once "checksession.php";
                loginStatus(); //show the current login status

                include "config.php"; //load in any variables
                $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

                //insert DB code from here onwards
                //check if the connection was good
                if (mysqli_connect_errno()) {
                    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
                    exit; //stop processing the page further
                }

                //prepare a query and send it to the server
                $query = 'SELECT itemID, pizza, pizzatype FROM fooditems ORDER BY pizzatype';
                $result = mysqli_query($DBC, $query);
                $rowcount = mysqli_num_rows($result);
                ?>
                <h2><a href='additem.php'>[Add a food item]</a><a href="index.php">[Return to main page]</a></h2>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Food Item Name</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <?php
                    //makes sure we have food items
                    if ($rowcount > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['itemID'];
                            $pt = $row['pizzatype'] == 'S' ? 'Standard' : 'Vegeterian';
                            echo '<tr><td>' . $row['pizza'] . '</td><td>' . $pt . '</td>';
                            echo '<td><a href="viewitem.php?id=' . $id . '">[view]</a>';
                            echo '<a href="edititem.php?id=' . $id . '">[edit]</a>';
                            echo '<a href="deleteitem.php?id=' . $id . '">[delete]</a></td>';
                            echo '</tr>' . PHP_EOL;
                        }
                    } else echo "<h2>No food items found!</h2>"; //suitable feedback
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