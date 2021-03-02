<!--
    Navigation Page
    Author: Sabrina Hill
	Spring 2021
	
    Filename: nav.php
   -->
<?php
	require "database.php";
	if ($Admin == True){
		echo '<ul><li><a href="index.php">Home</a></li><li><a href="logout.php">Logout</a></li><li><a href="admin.php">Admin</a></li></ul>';
	} else if ($loggedIn == True){
		echo '<ul><li><a href="index.php">Home</a></li><li><a href="logout.php">Logout</a></li></ul>';
	}else{
		echo '<ul><li><a href="index.php">Home</a></li><li><a href="login.php">Log in</a></li><li><a href="registration.php">Registration</a></li></ul>';
	}
?>