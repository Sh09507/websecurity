<!--
    login-handler Page
    Author: Sabrina Hill

    Filename: login-handler.php
   -->
	
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
				
				//makes sure character length stays within the limit and if there is an atempt to go past the limit it will prevent the user from doing so and logs it
				if(strlen($_POST['username']) > 30 || strlen($_POST['password']) > 50) {
							echo "<p>Maximum character limit has been reached!</p>";
							require_once "logging.php";
							auditlog($myDBconnection, "Login Attempt Exceeded Character Limit", 2, $username, $password, "NULL", "NULL");
						} else {
							//Makes sure the password has at least the minimum required characters
							if(strlen($_POST['password']) < 10) {
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