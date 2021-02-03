<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
<!--
    login Page
    Author: Sabrina Hill

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
				<li><a href="forgotpass.php">Forgot Password</a></li>
			</ul>
		</nav>
	</header>
	<main>
		<article>  
				<form method="post">
					<fieldset id="loginfo">
						<legend>Please enter your details to log in.</legend>
						
						<label id ="user"><b>User Name: </b></label>
						<input type="text" name="username" placeholder="Enter User Name" maxlength="30" required>
						<br>
						<label id ="psw"><b>Password: </b></label>
						<input type="password" name="password" placeholder="Enter Password" maxlength="50" pattern=".{10,}" required>
					</fieldset>
					<input type="submit" value="Login" name="submit">
				</form>
		</article>
	</main>
	
	<?php
		//Connect to DB
			require_once 'database.php'; 
			try {
				$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
			} catch (PDOException $e) {
				$error_message = $e->getMessage();
				print $error_message . "<br>";
			}
				
			//sanitize function (to clean up malicious data)
			function sanitize($bad){
				$bad = stripslashes($bad);
				$bad = strip_tags($bad);
				$good = htmlentities($bad);
				return $good;
			}
				
			//has the form been submitted?
			if(isset($_POST["submit"])){ 
				
				//are all the fields filled out?
				if( !(empty($_POST["username"])) && !(empty($_POST["password"]))) {
						
					//put all POST values into variables
					$username = $_POST["username"];
					$password = $_POST["password"];
						
					//sanitize each of the fields (send each field to the sanitize function)
					$username = sanitize($username);
					$password = sanitize($password);
					
					if(strlen($_POST['user']) > 30 || strlen($_POST['psw']) > 50) {
								echo "<p>Maximum character limit has been reached!</p>";
								require_once "logging.php";
								auditlog($myDBconnection, "Login Attempt Exceeded Character Limit", 2, $username, $password, "NULL", "NULL");
							} else {
								if(strlen($_POST['psw']) < 10) {
									echo "<p>Password is too short!</p>";
									require_once "logging.php";
									auditlog($myDBconnection, "Login Attempt had too short of a password", 2, $username, $password, "NULL", "NULL");
								} else {
									//do all the sanitized variables still have a value?
									if( $username != "" && $password != "" ) {
										try {
											//check to see if your table has the same fields & is spelled the same way
											$query = 'SELECT user_name, password FROM users WHERE user_name = :username AND
											password = :password;';
											$statement = $myDBconnection -> prepare($query);
											$statement -> bindValue(':username', $username); 
											$statement -> bindValue(':password', $password);
											$statement -> execute();
											$result = $statement -> fetch();
										} catch (PDOException $e) {
											$error_message = $e->getMessage();
											echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
										}
										//Does the username match the data in the table? 
										if (!empty($result)) {
											echo "Welcome back";
											$_SESSION["MySession"] = $username;
											require_once "logging.php";
											auditlog($myDBconnection,"User Login", 0, $username, $password, "NULL", "NULL");
											header('Location: index.php');
										}else {
											echo "User not found, please try again.";
											require_once "logging.php";
											auditlog($myDBconnection,"Login Attempt Failed", 1, $username, $password, "NULL", "NULL");
											session_unset($_SESSION["MySession"]);
											session_destroy();
										}
									} else { //not all sanitized variables have values
										echo "<p>Bad data was inserted into the fields.</p>";
									}
								}
							}
				} else { //not all fields were filled in
					echo "<p>Not all fields were filled in.</p>";
				}
			} else { //form not submitted
				echo "<p>Form has not been submitted yet.</p>";
			}
	?>
</body>
</html>