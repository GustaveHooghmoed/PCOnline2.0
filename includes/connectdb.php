<?php
$host="localhost";
$username="root";
$password="";
$db_name="pc";

/*$host="localhost";
$username="root";
$password="---;
$db_name="parkcraft";*/


$mysqli = mysqli_connect($host, $username, $password) or die(mysqli_connect_error());
mysqli_select_db($mysqli, $db_name) or die(mysqli_connect_error());
?>
