<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
<!--
    Admin Page
    Author: Sabrina Hill

    Filename: index.php
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
		<?php
			require_once 'database.php'; 
			try {
				$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
			} catch (PDOException $e) {
				$error_message = $e->getMessage();
				print $error_message . "<br>";
			}
			
			try {
				$query = "SELECT user_name FROM items FROM users" ; 
				$dbquery = $myDBconnection -> prepare($query);
				$dbquery -> execute();						
				$results = $dbquery -> fetchAll();
			}
			catch (PDOException $e) {
				$error_message = $e -> getMessage(); 
				echo $error_message . "<br>"; 
			}
			foreach ($results as $value){
		?>
			<ul>
				<li><?php echo $value['user_name'];?>
				<form>
					<input type="submit" name="delete"<?php echo $value['user_name']; ?> value="Delete User"/>
				</form>
				</li>
		</ul>
		<?php
			$d = 'delete' . $value['user_name'];
			if(isset($_POST[$d])) {
				try {
					$query = 'DELETE FROM users WHERE user_name = :username;';
					$dbquery = $myDBconnection -> prepare($query);
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