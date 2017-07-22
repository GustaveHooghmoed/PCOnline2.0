<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 27-1-2017
 * Time: 23:03
 */

ob_start();
session_start();
$cvwl = false;
include 'includes/connectdb.php';
include 'includes/PCO_API.php';
if(isset($_GET['code'])) {
    $code = $_GET['code'];
    $code = trim($code);
    $code = strip_tags($code);
    $code = mysqli_real_escape_string($mysqli, $code);
    if(user::IsActivationCodeValid($mysqli, $_GET['code'])) {
        user::activateAccount($mysqli, $code);
        header("Location: index.php?info=Jouw account is geactiveerd. Je kunt nu inloggen");
        exit;
    } else {
        header("Location: index.php?warning=Deze activatie code is ongeldig");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
mysqli_close($mysqli);
?>