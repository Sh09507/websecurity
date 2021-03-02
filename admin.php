<?php 
require "cookie.php"; 
if ($admin == True){
} else {
	header('Location:index.php');
}
?>
<!doctype html>
<html lang="en">
<head>
<!--
    Admin Page
    Author: Sabrina Hill

    Filename: admin.php
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
		<?php
			require_once 'database.php'; 
			try {
				$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
			} catch (PDOException $e) {
				$error_message = $e->getMessage();
				print $error_message . "<br>";
			}
			
			try {
				$query = "SELECT user_name FROM users WHERE admin = 'N';"; 
				$dbquery = $myDBconnection -> prepare($query);
				$dbquery -> execute();						
				$results = $dbquery -> fetchAll();
			}
			catch (PDOException $e) {
				$error_message = $e -> getMessage(); 
				echo $error_message . "<br>"; 
			}
			foreach ($results as &$value){
		?>
			<ul>
				<li><?php echo $value['user_name'];?>
				<form method="post">
					<input type="submit" name="delete<?php echo $value['user_name']; ?>" value="Delete User"/>
				</form>
				</li>
		</ul>
		<?php
			$d = 'delete' . $value['user_name'];
			if(isset($_POST[$d])) {
				try {
					$query = 'DELETE FROM users WHERE user_name = :username;';
					$dbquery = $myDBconnection -> prepare($query);
					//echo "test";
					$dbquery -> bindValue(':username', $value['user_name']); 
					$dbquery -> execute();
					header('Location:admin.php');
				} catch (PDOException $e) {
					$error_message = $e->getMessage();
					echo "<p>An error occurred while trying to delete data from the table: $error_message </p>";
				}
			}
			}
		?>
	</main>
</body>
</html>