<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 26-1-2017
 * Time: 21:41
 */
ob_start();
session_start();
include 'includes/connectdb.php';
include 'includes/PCO_API.php';
include 'includes/CHAT_API.php';
include 'includes/Mobile_Detect.php';

$mobile = false;
$detect = new Mobile_Detect();
if ($detect->isMobile()) {
    $mobile = true;
} else {
    $mobile = false;
}
if(!$cvwl) {
    if (!isset($_SESSION['user'])) {
        header("Location: index.php?redirect=".urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
    if (!user::hasAccess($mysqli, $_SESSION['UUID'])) {
        header("Location: index.php");
        exit;
    }
    if (!user::isActivated($mysqli, $_SESSION['UUID'])) {
        header("Location: index.php");
        exit;
    }
}
system::addPageVisit($mysqli);
if(system::isMaintenanceModeOn($mysqli)) {
    user::logout();
    header("Location: index.php?warning=Onderhoudsmode is ingeschakeld. Meer info? Volg ons op <a href=\"https://www.facebook.com/ParkCraft-370915049752819/\" class=\"soc-btn fb\">Facebook</a>, <a href=\"https://twitter.com/ParkenCraft\" class=\"soc-btn tw\">Twitter</a> en <a href=\"https://www.youtube.com/ParkCraft\" class=\"soc-btn gp\">YouTube</a>");
    exit;
}
user::setLastExcecution($mysqli);

foreach($_REQUEST as $key => $value) {
    $_REQUEST[$key] = your_filter($value, $mysqli);
}
function your_filter($value, $mysqli) {
    $newVal = trim($value);
    $newVal = mysqli_real_escape_string($mysqli, $newVal);
    return $newVal;
}
?>