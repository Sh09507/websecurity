<?php require "cookie.php"; ?>
<html lang="en">
<head>
<!--
    Registration Page
    Author: Sabrina Hill

    Filename: forgotpass.php
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
					<br>
					<label id="user"><b>User Name: </b></label>
					<input type="text" placeholder="Enter User Name" name="user" maxlength="30" required>
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
					<input type="text" name="answer" maxlength="50" required>
					<br>
					<label id ="psw"><b>New Password: </b></label>
					<input type="password" name="password" placeholder="Enter New Password" maxlength="50" pattern=".{10,}" required>

				</fieldset>
				<input type="submit" value="Change Password" name="recover">
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
			function sani($bad){
				$bad = stripslashes($bad);
				$bad = strip_tags($bad);
				$good = htmlentities($bad);
				return $good;
			}
				
			//has the form been submitted?
			if(isset($_POST["recover"])){ 
				
				//are all the fields filled out?
				if( !(empty($_POST["user"])) && !(empty($_POST["question"])) && !(empty($_POST["answer"])) && !(empty($_POST["password"]))) {
						
					//put all POST values into variables
					$username = $_POST["user"];
					$question = $_POST["question"];
					$answer = $_POST["answer"];
					$password = $_POST["password"];
				
					$username = sani($username);
					$question = sani($question);
					$answer = sani($answer);
					$password = sani($password);
					
					if(strlen($_POST['user']) > 30 || strlen($_POST['answer']) > 50 || strlen($_POST['password']) > 50) {
						echo "<p>Maximum character limit has been reached!</p>";
						$password = password_hash($password, PASSWORD_DEFAULT);
						require_once "logging.php";
						auditlog($myDBconnection, "Recovery Attempt Exceeded Character Limit", 2, $username, $password, $question, $answer);
					} else {
						if(strlen($_POST['password']) < 10) {
							echo "<p>Password is too short!</p>";
							require_once "logging.php";
							auditlog($myDBconnection, "Changed password Attempt was too short", 2, $username, $password, $question, $answer);
						} else {				
							//do all the sanitized variables still have a value?
							if(  $username != "" && $question != "" && $answer != "" && $password != "" ) {
								try {
									//check to see if your table has the same fields & is spelled the same way
									$query = 'SELECT user_name, password, security_question, answer FROM users WHERE user_name = :username AND
									security_question = :question AND answer = :answer;';
									$statement = $myDBconnection -> prepare($query);
									$statement -> bindValue(':username', $username); 
									$statement -> bindValue(':question', $question);
									$statement -> bindValue(':answer', $answer);
									$statement -> execute();
									$result = $statement -> fetch();
								} catch (PDOException $e) {
									$error_message = $e->getMessage();
									echo "<p>An error occurred while trying to retrieve data from the table: $error_message </p>";
								}
								//Does the username match the data in the table? 
								if (!empty($result)) {
									$password = password_hash($password, PASSWORD_DEFAULT);
									$query = 'UPDATE users SET password = :password WHERE user_name = :username;';
									$statement = $myDBconnection -> prepare($query);
									$statement -> bindValue(':username', $username); 
									$statement -> bindValue(':password', $password);
									$statement -> execute();
									echo "Password has been updated. Please try Logging in.";
									require_once "logging.php";
									auditlog($myDBconnection,"User Password Changed", 1, $username,$password, $question, $answer);
								}else {
									echo "Invalid credentials. Try again.";
									$password = password_hash($password, PASSWORD_DEFAULT);
									require_once "logging.php";
									auditlog($myDBconnection,"Password Recovery Failed", 1, $username,$password, $question, $answer);
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