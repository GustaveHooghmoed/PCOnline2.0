<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 19-1-2017
 * Time: 12:02
 */
$cvwl = false;
include 'includes/connectdb.php';
include 'includes/PCO_API.php';
unset($_SESSION['remoteuuid']);
unset($_SESSION['remoteuser']);
user::logout();
mysqli_close($mysqli);
header("Location: index.php");
exit;
?>