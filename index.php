<?php require "cookie.php"; ?>
<!doctype html>
<html lang="en">
<head>
<!--
    Home Page
    Author: Sabrina Hill
    Due Date:   2/6/2020

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
			<li><a href="topic.php?t=1">How to not break the bank playing gacha games.</a></li>
			<li><a href="topic.php?t=2">Why do I love Nagito so much?</a></li>
			<li><a href="topic.php?t=3">Anime Recommendations</a></li>
			<li><a href="topic.php?t=4">Why sonic is the best</a></li>
			<li><a href="topic.php?t=5">9s appreciation post</a></li>
		</ul>
	</main>
</body>

</html> 