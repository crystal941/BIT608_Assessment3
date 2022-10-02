 <!DOCTYPE HTML>
 <html>

 <head>
   <title>Add a New Food Item</title>
 </head>

 <body>
   <?php
    include "header.php";
    include "menu.php";
    ?>
   <div id="body">
     <div class="header">
       <div>
         <h1>Add a new food item</h1>
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

          //the data was sent using a formtherefore we use the $_POST instead of $_GET
          //check if we are saving data first by checking if the submit button exists in the array
          if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {
            //if ($_SERVER["REQUEST_METHOD"] == "POST") { //alternative simpler POST test    
            include "config.php"; //load in any variables
            $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

            if (mysqli_connect_errno()) {
              echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
              exit; //stop processing the page further
            };

            //validate incoming data - only the first field is done for you in this example - rest is up to you do
            //food item name
            $error = 0; //clear our error flag
            $msg = 'Error: ';
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

            //save the item data if the error flag is still clear
            if ($error == 0) {
              $query = "INSERT INTO fooditems (pizza,description,pizzatype,price) VALUES (?,?,?,?)";
              $stmt = mysqli_prepare($DBC, $query); //prepare the query
              mysqli_stmt_bind_param($stmt, 'sssd', $pizza, $description, $pizzatype, $price);
              mysqli_stmt_execute($stmt);
              mysqli_stmt_close($stmt);
              echo "<h2>New food item added to the list</h2>";
            } else {
              echo "<h2>$msg</h2>" . PHP_EOL;
            }
            mysqli_close($DBC); //close the connection once done
          }
          ?>

         <h2><a href='listitems.php'>[Return to the food item listing]</a><a href='/pizza/'>[Return to the main page]</a></h2>

         <form method="POST" action="additem.php">
           <p>
             <label for="pizza">Pizza name: </label>
             <input type="text" id="pizza" name="pizza" minlength="5" maxlength="15" required>
           </p>
           <p>
             <label for="description">Description: </label>
             <input type="text" id="description" size="100" name="description" minlength="5" maxlength="200" required>
           </p>
           <p>
             <label for="pizzatype">Pizza type: </label>
             <input type="radio" id="pizzatype" name="pizzatype" value="S" checked> Standard
             <input type="radio" id="pizzatype" name="pizzatype" value="V"> Vegitarian
           </p>
           <p>
             <label for="price">Price $(5.0 to 50.0): </label>
             <input type="number" id="price" name="price" min="5" max="50" value="5.0" step="0.10" required>
           </p>

           <input type="submit" name="submit" value="Add">
           <a href="listitems.php">[Cancel]</a>
         </form>
       </div>
     </div>
   </div>
 </body>
 <?php
  include "footer.php"
  ?>

 </html>