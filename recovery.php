<?php require "cookie.php"; 
if ($pwdrec == False) {
	header("Location:forgotpass.php");
}
?>
<html lang="en">
<head>
<!--
    Registration Page
    Author: Sabrina Hill

    Filename: forgotpass.php
   -->
<header>
	<nav>
		<?php
			require "nav.php";
		?>
	</nav>
</header>
<form method="post">
	<fieldset>   
	   <label id ="psw"><b>New Password: </b></label>
		<input type="password" name="password" placeholder="Enter New Password" maxlength="50" pattern=".{10,}" required>
	</fieldset>
	<input type="submit" value="Change Password" name="recover">
</form>

<?php
//Connect to DB
	// lines 32-38 was code referenced from Hawkins Web programming Lab 14 index.php
	require_once 'database.php'; 
	try {
		$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
	} catch (PDOException $e) {
		$error_message = $e->getMessage();
		print $error_message . "<br>";
	}
		
	//sanitize function (to clean up malicious data)
	//sanatization is referenced from my web programming Assignment 5 that Hawkins helped with, and the this assignment took reference code from web programming lab 16 with htmlentities and strip_tags/stripcslashes
	function sani($bad){
		$bad = stripslashes($bad);
		$bad = strip_tags($bad);
		$good = htmlentities($bad);
		return $good;
	}
		
	//has the form been submitted?
	if(isset($_POST["recover"])){ 
		
		//are all the fields filled out?
		if( !(empty($_POST["password"]))) {
				
			//put all POST values into variables
			$password = $_POST["password"];
		
			$password = sani($password);
			
			if(strlen($_POST['password']) > 50) {
				echo "<p>Maximum character limit has been reached!</p>";
				$password = password_hash($password, PASSWORD_DEFAULT);
				require_once "logging.php";
				auditlog($myDBconnection, "Recovery Attempt Exceeded Character Limit", 2, $pwdrecUser, $password, NULL, NULL);
			} else {
				if(strlen($_POST['password']) < 10) {
					echo "<p>Password is too short!</p>";
					require_once "logging.php";
					auditlog($myDBconnection, "Changed password Attempt was too short", 2, $pwdrecUser, $password, NULL, NULL);
				} else {				
					//do all the sanitized variables still have a value?
					if($password != "" ) {
					//Does the username match the data in the table? 
						//  Line 75 Was shown in class this semester by Thackston
						$password = password_hash($password, PASSWORD_DEFAULT);
						$query = 'UPDATE users SET password = :password WHERE user_name = :username;';
						$statement = $myDBconnection -> prepare($query);
						$statement -> bindValue(':username', $pwdrecUser); 
						$statement -> bindValue(':password', $password);
						$statement -> execute();
						echo "Password has been updated. Please try Logging in.";
						//  Line 82 Was shown in class this semester by Thackston
						setcookie('Pwdcookie', $token, time() - 3600, "/");
						require_once "logging.php";
						auditlog($myDBconnection,"User Password Changed", 1, $pwdrecUser, $password, NULL, NULL);
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