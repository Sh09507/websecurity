<?php require "cookie.php"; ?>
<!doctype html>
<html lang="en">
<head>
<!--
    login Page
    Author: Sabrina Hill

    Filename: login.php
   -->
   <meta charset="utf-8"/>
    <title>Hill: Web Security</title>
</head>
<body>
	<header>
		<nav>
			<?php
				require "nav.php";
			?>
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
						<input type="password" name="password" placeholder="Enter Password" maxlength="50" pattern=".{10,}" required> <a href="forgotpass.php">Forgot Password?</a>
					</fieldset>
					<input type="submit" value="Login" name="submit">
				</form>
		</article>
	</main>
	
	<?php
		//Connect to DB
			require_once 'database.php'; 
			// lines 43-47 was code referenced from Hawkins Web programming Lab 14 index.php
			try {
				$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
			} catch (PDOException $e) {
				$error_message = $e->getMessage();
				print $error_message . "<br>";
			}
			//sanatization is referenced from my web programming Assignment 5 that Hawkins helped with, and the this assignment took reference code from web programming lab 16 
			//sanitize function (to clean up malicious data)
			function sani($bad){
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
					$username = sani($username);
					$password = sani($password);
	
					if(strlen($_POST['username']) > 30 || strlen($_POST['password']) > 50) {
								echo "<p>Maximum character limit has been reached!</p>";
								$password = password_hash($password, PASSWORD_DEFAULT);
								require_once "logging.php";
								auditlog($myDBconnection, "Login Attempt Exceeded Character Limit", 2, $username, $password, "NULL", "NULL");
							} else {
								if(strlen($_POST['password']) < 10) {
									echo "<p>Password is too short!</p>";
									//  Line 87 Was taught this semester by Thackston
									$password = password_hash($password, PASSWORD_DEFAULT);
									require_once "logging.php";
									auditlog($myDBconnection, "Login Attempt had too short of a password", 2, $username, $password, "NULL", "NULL");
								} else {
									//do all the sanitized variables still have a value?
									if( $username != "" && $password != "" ) {
										try {
											//check to see if your table has the same fields & is spelled the same way
											$query = 'SELECT user_name, password, admin FROM users WHERE user_name = :username;';
											$statement = $myDBconnection -> prepare($query);
											$statement -> bindValue(':username', $username); 
											$statement -> execute();
											$result = $statement -> fetch();
										} catch (PDOException $e) {
											$error_message = $e->getMessage();
											echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
										}
										//Does the username match the data in the table? 
										if (!empty($result) && password_verify($password, $result['password'])) {
											echo "Welcome back";
											//  Line 101 Was shown in class this semester by Thackston
											$token = bin2hex(random_bytes(15));
											$query = 'INSERT INTO Cookies (user_name, Token, Expiration, admin) VALUES (:username, :token, DATE_ADD(NOW(), INTERVAL 7 DAY), :admin);';
											$statement = $myDBconnection -> prepare($query);
											$statement -> bindValue(':username', $username); 
											$statement -> bindValue(':token', $token);
											$statement -> bindValue(':admin', $result['admin']);
											$statement -> execute();
											//  Line 109 Was shown in class this semester by Thackston
											setcookie('Auth', $token, time() + (86400 * 7), "/");
											$password = password_hash($password, PASSWORD_DEFAULT);
											require_once "logging.php";
											auditlog($myDBconnection,"User Login", 0, $username, $password, "NULL", "NULL");
											header('Location: index.php');
										}else {
											echo "User not found, please try again.";
											$password = password_hash($password, PASSWORD_DEFAULT);
											require_once "logging.php";
											auditlog($myDBconnection,"Login Attempt Failed", 1, $username, $password, "NULL", "NULL");
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