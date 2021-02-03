<?php session_start(); ?>
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
			<ul>
				<li><a href="index.php">Home</a></li>
				<li><a href="login.php">Log in</a></li>
				<li><a href="registration.php">Registration</a></li>
				<li><a href="admin.php">Admin</a></li>
			</ul>
		</nav>
	</header>
		<main>
		<article>  
				<form method="post">
					<fieldset id="reginfo">
						<legend>Please enter your details to register.</legend>

						<label id="user"><b>User Name: </b></label>
						<input type="text" placeholder="Enter User Name" name="user" required>
						<br>
						<label id="psw"><b>Password: </b></label>
						<input type="password" placeholder="Enter Password" name="psw" required>
						<br>
						<label id="question"><b>Security Question: </b></label>
						<input type="text" list="questionOptions" id="q" name="question" class="question"required>
						<datalist id="questionOptions">                 
							<option value="What is the name of the town where you were born?"></option>       
							<option value="Who was your childhood hero?"></option>    
							<option value="Where was your best family vacation as a kid?"></option>    
							<option value="What is the name of your first pet?"></option>    
							<option value="What was your first car?"></option>       
						</datalist> 
						<br>
						<label id="answer"><b>Security Answer: </b></label>
						<input type="text" name="answer" required>

					</fieldset>
					<input type="submit" value="Register" name="submit">
				</form>
				<?php
				echo "test";
					//connection info
					require_once 'database.php'; 				
					try {
						$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
					} catch (PDOException $e) {
						$error_message = $e->getMessage();					
						print $error_message . "<br>";
					}
					echo "test";
					function sani($bad){
						$good =  htmlentities( strip_tags( stripslashes( $bad ) ) );
						return $good;
					}
					echo "test";
					//Check if form has not submited
					if(isset($_POST["submit"])){
						echo "test";
						//are all the fields filled out? If not, empty do the following
						if( !(empty($_POST["user"])) && !(empty($_POST["psw"])) && !(empty($_POST["question"])) && !(empty($_POST["answer"]))) {
							echo "test";
							//Put all POST values into variables from form
							$username = $_POST["user"];
							$password = $_POST["psw"];
							$question = $_POST["question"];
							$answer = $_POST["answer"];
							
							$username = sani($username);
							$password = sani($password);
							$question = sani($question);
							$answer = sani($answer);
							
							//If username is not blank, test if it's in the database
							if( $username != "" && $password != "" && $question != "" && $answer != "") {
								echo "test";
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
									echo "test";
									try {
										//Check if table has the same fields & spelled the same way
										$query = 'INSERT INTO users (user_name,password,security_question,answer) VALUES (:user_name,:password,:security_question,:answer);';
										$statement = $myDBconnection -> prepare($query);
										$statement -> bindValue(":user_name", $username);
										$statement -> bindValue(":password", $password);
										$statement -> bindValue(":security_question", $question);
										$statement -> bindValue(":answer", $answer);
										$statement -> execute();
										echo "You have been successfully registered!";
										require_once "logging.php";
										auditlog($myDBconnection,"New account registered", 0, $username, $password, $question, $answer);
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
						} else {
							echo "Not all fields have been filled in.";
						}
					}
				?>
		</article>
	</main>
</body>
</html>