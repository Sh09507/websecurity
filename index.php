<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
<!--
    Home Page
    Author: Sabrina Hill
    Due Date:   2/6/2020

    Filename: index.php
   -->
   <meta charset="utf-8"/>
   <title>Hill: Web Security</title>
</head>

<body>
	<header>
		<nav>
			<ul>
				<li><a href="index.php">Home</a></li>
				<li><a href="login.php">Log in</a></li>
				<li><a href="logout.php">Logout</a></li>
				<li><a href="registration.php">Registration</a></li>
				<li><a href="admin.php">Admin</a></li>
			</ul>
		</nav>
	</header>
	<main>
		<article>
			<h2>Welcome to the Home page!</h2>
			<?php
				if ($login == 1) {
					echo '<p> You are logged in!</p>';
				} else {
					echo '<P>Please sign up or log in.</p>';
				}
			?>
</body>

</html> 