<!-- menu start -->
<div id="header">
	<div>
		<a href="index.php" class="logo"><img src="images/logo.png" alt=""></a>
		<ul id="navigation">
			<li class="menu selected">
				<a href="index.php">Home</a>
			</li>
			<li class="menu">
				<a href="">Pizzas</a>
				<ul class="primary">
					<li><a href="product.php">Pizza Menu</a></li>
					<li><a href="addorder.php">Place Order</a></li>
				</ul>

			</li>
			<li class="menu">
				<a href="">About</a>
				<ul class="primary">
					<li><a href="about.php">About Us</a></li>
					<li><a href="contact.php">Contact Us</a></li>
					<li><a href="privacy.php">Privacy Policy</a></li>
				</ul>
			</li>
			<li class="menu">
				<a href="">Login</a>
				<ul class="secondary">
					<li>
						<a href="login.php">Sign in to Order</a>
						<a href="register.php">Register</a>
					</li>
				</ul>
			</li>

			<!-- only showing the admin menu to logged in users -->
			<?php
			include_once "checksession.php";
			if (!empty($_SESSION['loggedin']) and $_SESSION['loggedin'] == 1) {
				echo '<li class="menu"><a href="">Admin</a><ul class="secondary">
						<li><a href="listbookings.php">Reservations</a></li>
						<li><a href="listorders.php">Orders</a></li>';
				//only showing the fooditem and customer details to admin users
				if ($_SESSION['role'] == "admin") {
					echo '<li><a href="listitems.php">Products</a></li>                            
							<li><a href="listcustomers.php">Customers</a></li>';
				}
				echo '</ul></li>' . PHP_EOL;
			}
			?>
		</ul>
	</div>
</div>
<!-- menu end -->