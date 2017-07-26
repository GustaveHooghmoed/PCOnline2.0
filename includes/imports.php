<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 17-1-2017
 * Time: 22:28
 */
if(isset($_REQUEST['warning'])) {
    $warning= $_REQUEST['warning'];
}
if(isset($_REQUEST['info'])) {
    $info= $_REQUEST['info'];
}
if(isset($_REQUEST['danger'])) {
    $danger= $_REQUEST['danger'];
}
?>
<?php include_once("includes/analyticstracking.php") ?>
<!-- Material Design fonts -->
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
<link rel="stylesheet" type="text/css" href="assets/fonts/materialicons/Material-Icons.css">

<!-- Bootstrap -->
<link href="assets/css/bootstrap.min.css" rel="stylesheet">
<link href="includes/css/core.css" rel="stylesheet">
<link href="includes/css/animate.min.css" rel="stylesheet">
<?php system::getBackground($mysqli);?>
<!-- Bootstrap Material Design -->
<link href="assets/material/css/bootstrap-material-design.min.css" rel="stylesheet">
<link href="assets/material/css/ripples.min.css" rel="stylesheet">

<!-- Javascript -->
<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<?php if($mobile) {
   ?>
    <script type="text/javascript" src="https://rawgithub.com/expandtheroom/jquery-infinite-scroll-helper/master/jquery.infinite-scroll-helper.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/mobile/1.4.2/jquery.mobile.structure-1.4.2.css">
    <?php
} ?>
<script src="assets/material/js/material.js"></script>
<script src="includes/core.js"></script>
<!-- Favivon -->
<link rel="icon" type="image/png" href="/resources/favicon.png" sizes="16x16">
<link rel="icon" type="image/png" href="/resources/favicon.png" sizes="32x32">
<link rel="icon" type="image/x-icon" href="/resources/favicon.ico" >
<link rel="shortcut icon" type="image/x-icon" href="/resources/favicon.ico"/>