<?php
setcookie('Auth', $token, time() - 3600, "/");
header('Location:index.php');
?>