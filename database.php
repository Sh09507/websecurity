<?php 
// lines 3-8 was code from Hawkins Web programming Lab 14 database.php
	$HOST_NAME = 'localhost';  
	$DATABASE_NAME = 'websecurity';  
	$USERNAME = 'phpmyadmin';  
	$PASSWORD = 'sab95978';
	
	$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD); 
?>