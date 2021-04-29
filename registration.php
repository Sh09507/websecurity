<?php require "cookie.php"; ?>
<!doctype html>
<html lang="en">
<head>
<!--
    Registration Page
    Author: Sabrina Hill

    Filename: registration.php
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
					<fieldset id="reginfo">
						<legend>Please enter your details to register.</legend>

						<label id="user"><b>User Name: </b></label>
						<input type="text" placeholder="Enter User Name" name="user" maxlength="30" required>
						<br>
						<label id="psw"><b>Password: </b></label>
						<input type="password" placeholder="Enter Password" name="psw" maxlength="50" pattern=".{10,}" required>
						<br>
						<!--
							Lines 38-45 are code from Web programming assignment 5 profile.php values just changes from numbers to questions
						-->
						<label id="question"><b>Security Question: </b></label>
						<input type="text" list="questionOptions" id="q" name="question" required>
						<datalist id="questionOptions">                 
							<option value="What is the name of the town where you were born?"></option>       
							<option value="Who was your childhood hero?"></option>    
							<option value="Where was your best family vacation as a kid?"></option>    
							<option value="What is the name of your first pet?"></option>    
							<option value="What was your first car?"></option>       
						</datalist> 
						<br>
						<label id="answer"><b>Security Answer: </b></label>
						<input type="text" name="answer" maxlength="50" required>

					</fieldset>
					<input type="submit" value="Register" name="submit">
				</form>
				<?php
				//echo "test1";
					//connection info
					// lines 44-60 was code referenced from Hawkins Web programming Lab 14 index.php 
					require_once 'database.php'; 				
					try {
						$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
					} catch (PDOException $e) {
						$error_message = $e->getMessage();					
						print $error_message . "<br>";
					}
					//echo "test2";
					//sanatization is referenced from my web programming Assignment 5 that Hawkins helped with, and the this assignment took reference code from web programming lab 16 
					function sani($bad){
						//line 65 the strip tags were pulled from hawkins web programming Lab 16
						$good =  htmlentities( strip_tags( stripslashes( $bad ) ) );
						return $good;
					}
					//echo "test3";
					//Check if form has not submited
					if(isset($_POST["submit"])){
						//echo "test4";
						//are all the fields filled out? If not, empty do the following
						if( !(empty($_POST["user"])) && !(empty($_POST["psw"])) && !(empty($_POST["question"])) && !(empty($_POST["answer"]))) {
							//echo "test5";
							//Put all POST values into variables from form
							$username = $_POST["user"];
							$password = $_POST["psw"];
							$question = $_POST["question"];
							$answer = $_POST["answer"];
							
							$username = sani($username);
							$password = sani($password);
							$question = sani($question);
							$answer = sani($answer);
							
							if(strlen($_POST['user']) > 30 || strlen($_POST['psw']) > 50 || strlen($_POST['answer']) > 50) {
								echo "<p>Maximum character limit has been reached!</p>";
								//  Line 89 Was taught this semester by Thackston
								$password = password_hash($password, PASSWORD_DEFAULT);
								require_once "logging.php";
								auditlog($myDBconnection, "Register Attempt Exceeded Character Limit", 2, $username, $password, $question, $answer);
							} else {
								if(strlen($_POST['psw']) < 10) {
									echo "<p>Password is too short!</p>";
									require_once "logging.php";
									auditlog($myDBconnection, "Register Attempt had too short of a password", 2, $username, $password, $question, $answer);
								} else {
									//If username is not blank, test if it's in the database
									if( $username != "" && $password != "" && $question != "" && $answer != "") {
										//echo "test6";
										try {
											//See if username is in database
											$query = 'SELECT user_name FROM users WHERE user_name = :user_name;';
											$dbquery = $myDBconnection -> prepare($query);
											$dbquery -> bindValue(":user_name", $username);
											$dbquery -> execute();
											$results = $dbquery -> fetch();	
										} catch (PDOException $e) {
											$error_message = $e->getMessage();
											echo "An error occurred while selecting data from the table: $error_message";
										} 
										//If username is not in database, insert it
										if (empty($results)) {
											//echo "test7";
											try {
												//  Line 117 Was taught this semester by Thackston
												$password = password_hash($password, PASSWORD_DEFAULT);
												//Check if table has the same fields & spelled the same way
												$query = 'INSERT INTO users (user_name, password, security_question, answer, admin) VALUES (:user_name,:password,:security_question,:answer, "N");';
												$statement = $myDBconnection -> prepare($query);
												$statement -> bindValue(":user_name", $username);
												$statement -> bindValue(":password", $password);
												$statement -> bindValue(":security_question", $question);
												$statement -> bindValue(":answer", $answer);
												$statement -> execute();
												echo "You have been successfully registered!";
												require_once "logging.php";
												auditlog($myDBconnection,"New Account Registered", 0, $username, $password, $question, $answer);
											} catch (PDOException $e) {
												$error_message = $e->getMessage();
												echo "An error occurred while selecting data from the table: $error_message";
											}
										} else {
											echo "This username is already taken.";
										}
									} else {
										echo "Not all fields have been sanitized.";
									}
								}
							}
						} else {
							echo "Not all fields have been filled in.";
						}
					}
				?>
		</article>
	</main>
</body>
</html>