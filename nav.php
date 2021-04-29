<!--
    Navigation Page
    Author: Sabrina Hill
	Spring 2021
	
    Filename: nav.php
   -->
<?php
	//Lines 10-28 refernece Hawkins Lab 17 nav.php
	require "database.php";
	if ($admin == True){ ?>
		<ul>
			<li><a href="index.php">Home</a></li>
			<li><a href="logout.php">Logout</a></li>
			<li><a href="admin.php">Admin</a></li>
		</ul>
	<?php } else if ($loggedIn == True){ ?>
		<ul>
			<li><a href="index.php">Home</a></li>
			<li><a href="logout.php">Logout</a></li>
		</ul>
	<?php }else{ ?>
		<ul>
			<li><a href="index.php">Home</a></li>
			<li><a href="login.php">Log in</a></li>
			<li><a href="registration.php">Registration</a></li>
		</ul>
	<?php }
?>