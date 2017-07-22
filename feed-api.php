<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-3-2017
 * Time: 16:19
 */
ob_start();
session_start();
include 'includes/connectdb.php';
include 'includes/PCO_API.php';
include 'includes/FEED_API.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
if(isset($_GET['loadfeed'])) {
    feed::loadArticles($mysqli, $_GET['loadfeed'], $_SESSION['UUID']);
}
exit;