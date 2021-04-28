<?php 
require "database.php";
$loggedIn = False;
$Admin = False;
if(isset($_COOKIE['Auth'])) {
    try {
        $token = $_COOKIE['Auth'];
        $query = 'SELECT * FROM Cookies WHERE Token = :token AND Expiration > NOW();';
        $dbquery = $myDBconnection -> prepare($query);
        $dbquery -> bindValue(':token', $token); 
        $dbquery -> execute();
        $result = $dbquery -> fetch();
        if($result != "") {
            $loggedIn = True;
			$loggedInUser= $result['user_name'];
            if($result['admin'] == "Y") {
                $admin = True;
            }
        }
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
    }
}
$pwdrec = False;
if(isset($_COOKIE['Pwdcookie'])) {
    try {
        $token = $_COOKIE['Pwdcookie'];
        $query = 'SELECT * FROM Cookies WHERE Token = :token AND Expiration > NOW();';
        $dbquery = $myDBconnection -> prepare($query);
        $dbquery -> bindValue(':token', $token); 
        $dbquery -> execute();
        $result = $dbquery -> fetch();
        if($result != "") {
			$pwdrecUser = $result["user_name"];
            $pwdrec = True;
        }
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
    }
}
?>