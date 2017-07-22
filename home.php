<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
$cvwl = false;
include 'includes/phpimports.php';
include 'includes/FEED_API.php';
$active = 'home';
?>
<?php system::copyRightSign();?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ParkCraft Online</title>
        <script src="assets/js/bootstrap.min.js"></script>
        <?php include 'includes/imports.php';?>
    </head>
    <body>
    <?php include 'includes/nav.php';?>
    <div class="container">
        <?php if (isset($warning)) { ?>
            <div class="alert alert-dismissible alert-warning">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?php echo $warning; ?></span>
            </div>
        <?php } ?>
        <?php if (isset($danger)) { ?>
            <div class="alert alert-dismissible alert-danger">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?php echo $danger; ?></span>
            </div>
        <?php } ?>
        <?php if (isset($info)) { ?>
            <div class="alert alert-dismissible alert-info">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?php echo $info; ?></span>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-9">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">Jouw feed</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12" id="feed">
                                    <?php
                                    /*if($mobile) {
                                        article::loadArticles($mysqli);
                                    } else {*/
                                        feed::loadArticles($mysqli, 0, $_SESSION['UUID']);
                                    //}?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 right-container well">
                    <h4 class="text-danger">Wie te volgen</h4>
                    <?php park::loadWhoToFollow($mysqli,$_SESSION['UUID']);?>
                    <?php ads::skycraper();?></div>
            </div>
        </div>
    </div>
    <?php
    if(!$mobile) {
        ?>
        <script>
            var loadid = 1;
            $(document).ready(function () {
                $(document).scroll(function () {
                    if (($(window).scrollTop() + $(window).height() > ($(document).height() - 2))) {
                        $.get("https://parkcraft.nl/feed-api.php?loadfeed=" + loadid, function (data) {
                            $("#feed").append(data);
                        });
                        loadid++;
                    }
                });
            });
        </script>
        <?php
    } else {
        ?>
        <script>
            var loadid = 1;
            $(document).ready(function () {
                $(document).scroll(function () {
                    console.log("window scrolltop: " + $(window).scrollTop());
                    console.log("window height: " + $(window).height());
                    console.log("document height: " + $(document).height());
                    console.log(Math.trunc($(window).scrollTop() + $(window).height()) + " = " + $(document).height())
                    if ((Math.trunc($(window).scrollTop() + $(window).height()) == ($(document).height() - 1)) || (Math.trunc($(window).scrollTop() + $(window).height()) == ($(document).height()))) {
                        $.get("https://parkcraft.nl/feed-api.php?loadfeed=" + loadid, function (data) {
                            $("#feed").append(data);
                        });
                        loadid++;
                    }
                });
            });
        </script>
        <?php
    }?>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    </body>
    </html>
<?php
mysqli_close($mysqli);
?>