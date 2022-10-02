<?php
include "header.php";
include "checksession.php";

include "menu.php";
//----------- page content starts here

?>
<div id="body" class="home">
	<div class="header">
		<img src="images/bg-home.jpg" alt="">
		<div>
			<a href="product.php">Seafood Delight</a>
		</div>
	</div>
	<div class="body">
		<div>
			<div>
				<h1>NEW PRODUCT</h1>
				<h2>The Tasty Calzone Pizza</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean eget lacinia sem. Quisque a libero semper, efficitur ante quis, molestie erat. Phasellus ut turpis libero. Nulla ex est, tristique et nunc id, interdum dignissim nunc. Proin eget ipsum ipsum. Nunc a lectus et neque scelerisque consectetur. Nulla facilisi. </p>
			</div>
			<img src="images/calzone.jpg" alt="">
		</div>
	</div>
	<div class="footer">
		<div>
			<ul>
				<li>
					<a href="product.php" class="product"></a>
					<h1>Pizzas</h1>
				</li>
				<li>
					<a href="about.php" class="about"></a>
					<h1>ABOUT US</h1>
				</li>
				<li>
					<a href="login.php" class="flavor"></a>
					<h1>RESERVATIONS</h1>
				</li>
				<li>
					<a href="contact.php" class="contact"></a>
					<h1>CONTACT</h1>
				</li>
			</ul>
		</div>
	</div>
</div>
<?php
//----------- page content ends here
include "footer.php";
?>