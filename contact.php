<?php
include "header.php";
include "checksession.php";
include "menu.php";
?>
<div id="body" class="contact">
	<div class="header">
		<div>
			<h1>Contact</h1>
		</div>
	</div>
	<div class="body">
		<div>
			<div>
				<img src="images/check-in.png" alt="">
				<h1>Waipukurau Corner Pizzeria, 1 Main Street,Waipukurau, 4000</h1>
				<p>If you're having problems editing this website template, then don't hesitate to ask for help on the Forums.</p>
			</div>
		</div>
	</div>
	<div class="footer">
		<div class="contact">
			<h1>INQUIRY FORM</h1>
			<form action="contact.php">
				<input type="text" name="Name" value="Name" onblur="this.value=!this.value?'Name':this.value;" onfocus="this.select()" onclick="this.value='';">
				<input type="text" name="Email" value="Email" onblur="this.value=!this.value?'Email':this.value;" onfocus="this.select()" onclick="this.value='';">
				<input type="text" name="Subject" value="Subject" onblur="this.value=!this.value?'Subject':this.value;" onfocus="this.select()" onclick="this.value='';">
				<textarea name="meassage" cols="50" rows="7">Share your thoughts</textarea>
				<input type="submit" value="Send" id="submit">
			</form>
		</div>
		<div class="section">
			<h1>WE’D LOVE TO HEAR FROM YOU.</h1>
			<p>If you're having problems finding us, then don't hesitate to ask for help on Facebook.</p>
		</div>
	</div>
</div>
<?php
include "footer.php";
?>