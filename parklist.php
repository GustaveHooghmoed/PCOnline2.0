<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
$cvwl = true;
include 'includes/phpimports.php';
$active = 'parklist';
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
        <script type="text/javascript" src="includes/js/tablesorter.js"></script>
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
                            <h3 class="panel-title">Parklijst</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div>
                                        <?php park::LoadParksList($mysqli);?>
                                        <i>Klopt er iets niet? Mail het naar <a href="mailto:dani@parkcraft.nl">dani@parkcraft.nl</a>.</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(isset($_SESSION['UUID'])) {?>
                    <div class="col-md-3 right-container well">
                        <h4 class="text-danger">Wie te volgen</h4>
                        <?php park::loadWhoToFollow($mysqli,$_SESSION['UUID']);?>
                        <?php ads::skycraper($mysqli, basename(__FILE__));?>
                    </div>
                <?php }?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    </body>
    </html>
<?php
mysqli_close($mysqli);
?>