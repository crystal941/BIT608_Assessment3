<!DOCTYPE HTML>
<html>
<?php
include "header.php";
include "menu.php";
?>

<head>
  <title>Browse Customers</title>
</head>
<div id="body">
  <div class="header">
    <div>
      <h1>Current Customers</h1>
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
      //prepare a query and send it to the server
      $query = "SELECT customerID, firstname, lastname, email FROM customer ORDER BY lastname";
      $result = mysqli_query($DBC, $query);
      $rowcount = mysqli_num_rows($result);
      ?>
      <h2><a href='addcustomer.php'>[Create new Customer]</a><a href="/pizza/">[Return to main page]</a></h2>

      <table id="tblcustomers" border="1">
        <thead>
          <tr>
            <th>Lastname</th>
            <th>Firstname</th>
            <th>Email</th>
            <th>Actions</th>
          </tr>
        </thead>
        <?php
        //makes sure we have customers
        if ($rowcount > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['customerID'];
            echo '<tr><td>' . $row['lastname'] . '</td><td>' . $row['firstname'] . '</td>';
            echo '<td>' . $row['email'] . '</td>';
            echo '<td><a href="viewcustomer.php?id=' . $id . '">[View]</a>';
            echo '<a href="editcustomer.php?id=' . $id . '">[Edit]</a>';
            echo '<a href="deletecustomer.php?id=' . $id . '">[Delete]</a><td>';
            echo '</tr>' . PHP_EOL;
          }
        } else echo "<h2>No customer found!</h2>";
        mysqli_free_result($result);  //free any memory used by the query
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