<?php require "cookie.php"; ?>
<!doctype html>
<html lang="en">
<head>
<!--
    Topic Page
    Author: Sabrina Hill

    Filename: topic.php
   -->
   <meta charset="utf-8"/>
   <title>Hill: Web Security</title>
</head>

<body>
	<header>
		<h1>Welcome to the Topic page!</h1>
		<nav>
			<?php
				require "nav.php";
			?>
		</nav>
		<main>
			<?php
			$t = $GET_['t'];
			require_once 'database.php';
			try {
				$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
			} catch (PDOException $e) {
				$error_message = $e->getMessage();
				print $error_message . "<br>";
			}
			try{
				echo "t is: " . $t;
				$query = "SELECT ID, title, user_name, body, image FROM discussion_topics WHERE ID = :id;";
				$dbquery = $myDBconnection -> prepare($query);
				$dbquery -> bindvalue(':id', $t);
				$dbquery -> execute();
				$results = $dbquery -> fetchAll();
				echo "here are the results: " . $results["ID"];
				echo $results["title"];
				echo $results["user_name"];
				echo $results["body"];
				echo $results["image"];
			} catch (PDOException $e) {
						$error_message = $e -> getMessage();
						echo $error_message . "<br>";
						echo "The database had an error";
				}
					if($results != ""){
						foreach ($results as &$arr) {
						?>
							<h1><?php echo $arr['title']; ?></h1>
							<img src="images/<?php echo $arr['image']; ?>" alt="uploaded image">
							<h3><?php echo $arr['body'];?></h3>
							<p><?php echo "This post was brought to you by " . $arr['user_name'];?> </p> 
						<?php }
					}else {
						echo "<p>The topic you are looking for does not exist.</p>";
					}
			?>
		</main>
	</header>	
</body>
</html>