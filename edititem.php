<!DOCTYPE HTML>
<html>

<head>
  <title>Edit a Food Item</title>
</head>

<body>
  <?php
  include "header.php";
  include "menu.php";
  ?>
  <div id="body">
    <div class="header">
      <div>
        <h1>Food item Details Update</h1>
      </div>
    </div>
    <div class="body">
      <div>
        <?php
        include_once "checksession.php";
        checkUser(); //check if user logged in
        loginStatus(); //show the current login status
        include "config.php"; //load in any variables
        $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

        if (mysqli_connect_errno()) {
          echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
          exit; //stop processing the page further
        };

        //function to clean input but not validate type and content
        function cleanInput($data)
        {
          return htmlspecialchars(stripslashes(trim($data)));
        }

        //retrieve the itemid from the URL
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
          $id = $_GET['id'];
          if (empty($id) or !is_numeric($id)) {
            echo "<h2>Invalid food item ID</h2>"; //simple error feedback
            exit;
          }
        }
        //the data was sent using a formtherefore we use the $_POST instead of $_GET
        //check if we are saving data first by checking if the submit button exists in the array
        if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {
          //validate incoming data - only the first field is done for you in this example - rest is up to you do
          $error = 0; //clear our error flag
          $msg = 'Error: ';
          //refer to additems for extend validation examples
          //itemID (sent via a form it is a string not a number so we try a type conversion!)    
          if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
            $id = cleanInput($_POST['id']);
          } else {
            $error++; //bump the error flag
            $msg .= 'Invalid food item ID '; //append error message
            $id = 0;
          }
          //pizza
          if (isset($_POST['pizza']) and !empty($_POST['pizza']) and is_string($_POST['pizza'])) {
            $fn = cleanInput($_POST['pizza']);
            $pizza = (strlen($fn) > 15) ? substr($fn, 1, 15) : $fn; //check length and clip if too big
            //we would also do context checking here for contents, etc       
          } else {
            $error++; //bump the error flag
            $msg .= 'Invalid pizza  '; //append eror message
            $pizza = '';
          }
          //description
          if (isset($_POST['description']) and !empty($_POST['description']) and is_string($_POST['description'])) {
            $fn = cleanInput($_POST['description']);
            $description = (strlen($fn) > 200) ? substr($fn, 1, 200) : $fn; //check length and clip if too big   
            //we would also do context checking here for contents, etc  
          } else {
            $error++; //bump the error flag
            $msg .= 'Invalid description  '; //append eror message
            $description = '';
          }
          //pizzatype
          if (isset($_POST['pizzatype']) and !empty($_POST['pizzatype']) and is_string($_POST['pizzatype'])) {
            $fn = strtoupper(cleanInput($_POST['pizzatype']));
            $pizzatype = (strlen($fn) > 1) ? substr($fn, 1, 1) : $fn; //check length and clip if too big   
            if ($pizzatype != 'V') $pizzatype = 'S'; //can only be V or S
          } else {
            $error++; //bump the error flag
            $msg .= 'Invalid pizza type  '; //append eror message
            $pizzatype = '';
          }
          //price
          if (isset($_POST['price']) and !empty($_POST['price']) and is_float($_POST['price'] + 0)) { //must have decimal
            $price = cleanInput($_POST['price']);
            if ($price < 5 or $price > 50) $price = 5;
          } else {
            $error++; //bump the error flag
            $msg .= 'Invalid pizza price '; //append eror message
            $price = '';
          }

          //save the item data if the error flag is still clear and item id is > 0
          if ($error == 0 and $id > 0) {
            $query = "UPDATE fooditems SET pizza=?,description=?,pizzatype=?,price=? WHERE itemID=?";
            $stmt = mysqli_prepare($DBC, $query); //prepare the query
            mysqli_stmt_bind_param($stmt, 'ssssi', $pizza, $description, $pizzatype, $price, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo "<h2>Food item details updated.</h2>";
          } else {
            echo "<h2>$msg</h2>" . PHP_EOL;
          }
        }
        //locate the food item to edit by using the itemID
        //we also include the item ID in our form for sending it back for saving the data
        $query = 'SELECT itemID, pizza, description, pizzatype, price FROM fooditems WHERE itemid=' . $id;
        $result = mysqli_query($DBC, $query);
        $rowcount = mysqli_num_rows($result);
        if ($rowcount > 0) {
          $row = mysqli_fetch_assoc($result);
        ?>

          <h2><a href='listitems.php'>[Return to the food item listing]</a><a href='/pizza/'>[Return to the main page]</a></h2>

          <form method="POST" action="edititem.php">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <p>
              <label for="pizza">Pizza name: </label>
              <input type="text" id="pizza" name="pizza" minlength="5" maxlength="50" value="<?php echo $row['pizza']; ?>" required>
            </p>
            <p>
              <label for="description">Description: </label>
              <input type="text" id="description" name="description" size="100" minlength="5" maxlength="200" value="<?php echo $row['description']; ?>" required>
            </p>
            <p>
              <label for="pizzatype">Pizza type: </label>
              <input type="radio" id="pizzatype" name="pizzatype" value="S" <?php echo $row['pizzatype'] == 'S' ? 'Checked' : ''; ?>> Standard
              <input type="radio" id="pizzatype" name="pizzatype" value="V" <?php echo $row['pizzatype'] == 'V' ? 'Checked' : ''; ?>> Vegeterian
            </p>
            <p>
              <label for="price">Price $(5.0 to 50.0): </label>
              <input type="number" id="price" name="price" min="5" max="50" value="<?php echo $row['price']; ?>" step="0.10" required>
            </p>
            <input type="submit" name="submit" value="Update">
            <a href="listitems.php">[Cancel]</a>
          </form>
        <?php
        } else {
          echo "<h2>Food item not found with that ID</h2>"; //simple error feedback
        }
        mysqli_close($DBC); //close the connection once done
        ?>
      </div>
    </div>
  </div>
</body>
<?php
include "footer.php"
?>
</html>