<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
$cvwl = false;
include 'includes/phpimports.php';
$keywords = $_GET['keywords'];
$keywords = trim($keywords);
$keywords = strip_tags($keywords);
$keywords = mysqli_real_escape_string($mysqli, $keywords);
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
                            <ul class="nav navbar-info nav-tabs">
                                <li <?php if(isset($_GET['articles'])) { echo 'class="searchactive"';}?>><a href="?articles=&keywords=<?php echo $_GET['keywords'];?>"><h3 class="panel-title">Artikelen</h3></a></li>
                                <li <?php if(isset($_GET['parks'])) { echo 'class="searchactive"';}?>><a href="?parks=&keywords=<?php echo $_GET['keywords'];?>"><h3 class="panel-title">Parken</h3></a></li>
                                <li <?php if(isset($_GET['users'])) { echo 'class="searchactive"';}?>><a href="?users=&keywords=<?php echo $_GET['keywords'];?>"><h3 class="panel-title">Personen</h3></a></li>
                            </ul>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div>
                                        <?php if(isset($_GET['parks'])) {
                                            echo '<h3 class="panel-title">Parken op de woorden: '.$keywords.'</h3></a>';
                                            echo search::loadParks($mysqli, $keywords);
                                        } else if(isset($_GET['users'])) {
                                            echo '<h3 class="panel-title">Gebruikers op de woorden: '.$keywords.'</h3></a>';
                                            echo search::users($mysqli, $keywords);
                                        } else if(isset($_GET['articles'])) {
                                            echo '<h3 class="panel-title">Artikelen op de woorden: '.$keywords.'</h3></a>';
                                            search::load($mysqli, $keywords);
                                        }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 right-container well">
                    <h4 class="text-danger">Wie te volgen</h4>
                    <?php park::loadWhoToFollow($mysqli,$_SESSION['UUID']);?>
                    <?php ads::skycraper($mysqli, basename(__FILE__));?>
                </div>
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