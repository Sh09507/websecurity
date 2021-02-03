<?php session_start(); ?>
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
				<li><a href="logout.php">Logout</a></li>
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
						<br>
						<label id="user"><b>User Name: </b></label>
						<input type="text" placeholder="Enter User Name" name="user" required>
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
					<input type="submit" value="Recover Password" name="recover">
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
				if( !(empty($_POST["user"])) && !(empty($_POST["question"])) && !(empty($_POST["answer"]))) {
						
					//put all POST values into variables
					$username = $_POST["user"];
					$question = $_POST["question"];
					$answer = $_POST["answer"];
				
					$username = sani($username);
					$question = sani($question);
					$answer = sani($answer);
						
					//do all the sanitized variables still have a value?
					if(  $username != "" && $question != "" && $answer != "" ) {
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
							echo "Your password is " . $result["password"];
							require_once "logging.php";
							auditlog($myDBconnection,"User Password Recovered", 1, $username,"NULL" $question, $answer);
						}else {
							echo "Invalid credentials. Try again.";
							require_once "logging.php";
							auditlog($myDBconnection,"Password Recovery Failed", 1, $username,"NULL" $question, $answer);
						}
					} else { //not all sanitized variables have values
						echo "<p>Bad data was inserted into the fields.</p>";
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