<?php 
require "database.php";
$loggedIn = False;
$Admin = False;
if(isset($_COOKIE['Authentication'])) {
    try {
        $token = $_COOKIE['Authentication'];
        $query = 'SELECT * FROM Cookies WHERE Token = :token AND Expiration > NOW();';
        $dbquery = $myDBconnection -> prepare($query);
        $dbquery -> bindValue(':token', $token); 
        $dbquery -> execute();
        $result = $dbquery -> fetch();
        if($result != "") {
            $loggedIn = True;
            if($result['admin'] == "Y") {
                $admin = True;
            }
        }
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
    }
}
?>