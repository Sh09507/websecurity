<!--
    Logging Page
    Author: Sabrina Hill

    Filename: logging.php
   -->
<?php
function auditlog($myDBconnection, $event, $serverity, $username, $password, $question, $answer) {
	try {
		$query = 'INSERT INTO auditLog (event,serverity,IP,time,user_name,password,security_question,answer) VALUES (:event, :serverity,:ip,NOW(),:user_name,:password,:security_question,:answer);';
		$statement = $myDBconnection -> prepare($query);
		$statement -> bindValue(":event", $event);
		$statement -> bindValue(":serverity", $serverity);
		$statement -> bindValue(":ip", $_SERVER['REMOTE_ADDR']);
		$statement -> bindValue(":user_name", $username);
		$statement -> bindValue(":password", $password);
		$statement -> bindValue(":security_question", $question);
		$statement -> bindValue(":answer", $answer);
		$statement -> execute();
	/// lines 21-24 was code referenced from Hawkins Web programming Lab 14 index.php 
	} catch (PDOException $e) {
		$error_message = $e->getMessage();
		echo "An error occurred while selecting data from the table: $error_message";
	}
}
?>