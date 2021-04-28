<?php require "cookie.php"; ?>
<!doctype html>
<html lang="en">
<head>
<!--
    Home Page
    Author: Sabrina Hill

    Filename: index.php
   -->
   <meta charset="utf-8"/>
   <title>Hill: Web Security</title>
</head>

<body>
	<header>
	<h1>Welcome to the Home page!</h1>
		<nav>
			<?php
				require "nav.php";
			?>
		</nav>
	</header>
	<main>
		<h2>Discussion Topics</h2>
		<ul>
		<?php
		require_once 'database.php';
		try{
			$query = "SELECT ID, title, user_name FROM discussion_topics;";
			$dbquery = $myDBconnection -> prepare($query);
			$dbquery -> execute();
			$results = $dbquery -> fetchAll();
		} catch (PDOException $e) {
					$error_message = $e -> getMessage();
					echo $error_message . "<br>";
				}
				foreach ($results as &$arr) {
		?>
			<li>
				<a href="topic.php?t=<?php echo $arr['ID']; ?>"><?php echo $arr['title'] . " by " . $arr['user_name'];?></a>
			</li>
		<?php }?> 
		</ul>
		<?php if($loggedIn == True){
		?>
	<!--
		lines 37-46 code reference come from the description.php page of Web Programming assignment 5 lines 111-172
	-->
		<main>
		<!--
		line 53 code reference came from Hawkins Web Programming lab 12 attractions.php line 65
		-->
		<form method="post" action="index.php" enctype='multipart/form-data'>
			<fieldset>
			<legend>Create a post</legend>
				<input type="text" name="title" id="titlebox" placeholder="Title"> <br>
				<textarea type="text" name="body" rows = "5" cols="80" placeholder="What's on your mind?"></textarea><br>
				<input type="file" value="Choose File" name="image"><br>
			</fieldset>
			<input type="submit" name="submit" value="Post" />
		</form>
		<!--
		Lines 50-111 code from Web Programming Hawkins Assignment 5 profile.php 4/29/2020 Lines 82-143.
		-->
		<?php 
			require_once 'database.php'; 
			try {
				$myDBconnection = new PDO("mysql:host=$HOST_NAME;dbname=$DATABASE_NAME", $USERNAME, $PASSWORD);
			} catch (PDOException $e) {
				$error_message = $e->getMessage();
				print $error_message . "<br>";
			}
			function sani($bad){
				$good =  htmlentities( strip_tags( stripslashes( $bad ) ) );
				return $good;
			}
			if(isset($_POST['submit'])) {
			//is form submitted?
				if( !empty($_FILES['image']['name'])){
					$simg = sani( $_FILES['image']['name'] );    
					$file = "images/" . $_FILES['image']['name'];  
						switch($_FILES['image']['type'])    
						{    
							case 'image/jpeg': $ext = 'jpg'; break;      
							case 'image/gif':  $ext = 'gif'; break;      
							case 'image/png':  $ext = 'png'; break;      
							case 'image/tiff': $ext = 'tif'; break;      
							default:           $ext = '';    break;    
						}    
						if ($ext)    
						{           
							move_uploaded_file($_FILES['image']['tmp_name'], $file);   
						}    
						else{ 
							$simg = "";
						}
					if($simg != ""){
						echo "<br>$loggedInUser";
						$user = $loggedInUser;
						if(!empty($_POST['title']) && !empty($_POST['body'])){
							$stitle = sani($_POST['title']);
							$sbody = sani($_POST['body']);
							if($stitle != '' && $sbody != ''){
								try {
									$query = "INSERT INTO discussion_topics (user_name, title, body, image, date_added) VALUES (:user, :title, :body, :img, NOW());";
									$dbquery = $myDBconnection -> prepare($query);
									$dbquery -> bindValue(':user', $user);
									$dbquery -> bindValue(':title', $stitle);
									$dbquery -> bindValue(':body', $sbody);
									$dbquery -> bindValue(':img', $simg);
									$dbquery -> execute();
									header('Location:index.php');
								} catch (PDOException $e) {
									$error_message = $e -> getMessage();
									echo $error_message . "<br>";
								}	
							} else{
								echo "Text did not pass sanitization.";
							}
						} else{
							echo "Please fill in all text boxes.";
						}
					} else {
						echo "Sorry, image could not be uploaded. Please try again.";
					}
				} else{
					echo "No image was uploaded.";
				}
			} 
		}
		?>		
	</main>
</body>

</html> 