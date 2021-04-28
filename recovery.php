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
<form method="post">
	<fieldset>   
	   <label id ="psw"><b>New Password: </b></label>
		<input type="password" name="password" placeholder="Enter New Password" maxlength="50" pattern=".{10,}" required>
	</fieldset>
</form>

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
						$password = password_hash($password, PASSWORD_DEFAULT);
						$query = 'UPDATE users SET password = :password WHERE user_name = :username;';
						$statement = $myDBconnection -> prepare($query);
						$statement -> bindValue(':username', $pwdrecUser); 
						$statement -> bindValue(':password', $password);
						$statement -> execute();
						echo "Password has been updated. Please try Logging in.";
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