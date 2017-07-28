<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
$cvwl = false;
include 'includes/phpimports.php';
$userid = $_REQUEST['id'];
$active = '';
if(!userpage::exist($mysqli, $userid)) {
    header("Location: home.php");
    exit;
}
?>
<?php system::copyRightSign();?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ParkCraft Online | <?php echo strip_tags(userpage::getName($mysqli, $userid));?></title>
        <script src="assets/js/bootstrap.min.js"></script>
        <?php include 'includes/imports.php'; ?>
        <!--<style>
            body {
                background: url(/*php echo park::getBackrgound($mysqli, $parkid);*/);
                transition: background 0.5s linear;
                background-position: center;
                background-size: 100% 100%;
                background-size: cover !important;
                background-repeat: no-repeat;
                background-attachment: fixed !important;
                color: black;
            }
        </style>!-->
    </head>
    <body>
    <?php include 'includes/nav.php'; ?>
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
            <!--<div style=" height: 300px; background: url('/*echo park::getHeader($mysqli, $parkid);*/'); background-position: center;
                background-size: cover;
                background-repeat: no-repeat;" class="panel panel-success">
            </div>!-->
            <?php
            if(isset($_GET['volgend'])) {
                ?>
                <!--<div class="col-md-12">
                    <div class="col-md-9">
                        <div class="panel panel-info">
                            /*park::LoadFollowers($mysqli, $parkid);*/
                        </div>
                    </div>
                    <div class="col-md-3 right-container well">
                        <h4 class="text-danger">Wie te volgen</h4>
                        park::loadWhoToFollow($mysqli, $_SESSION['UUID']);
                        ads::skycraper($mysqli, basename(__FILE__));*/
                    </div>
                </div>!-->
                <?php
            } else {?>
                <div class="col-md-12">
                    <div class="col-md-9">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3 class="panel-title">Profiel</h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <div class="col-md-12 panel panel-default">
                                                <div class="col-md-12">
                                                    <img src="<?php echo userpage::getProfilePicture($mysqli, $userid);?>" alt="logo" class="img-responsive avatar center-block"/>
                                                </div>
                                                <div class="col-md-12 text-center">
                                                    <h5><span class="text-muted">Minecraft naam:</span> <?php echo userpage::getMC($mysqli, $userid);?></h5>
                                                    <img src="https://crafatar.com/renders/body/<?php echo userpage::getMC($mysqli, $userid);?>?overlay" alt="logo" class="img-responsive center-block"/>
                                                </div>
                                                <div class="col-md-12 text-center">
                                                    <?php if($userid == $_SESSION['user']) {
                                                        echo '<a href="settings.php?profile" class="btn btn-info">Beheren</a>';
                                                    }else if(user::getRankByUUID($mysqli, $_SESSION['UUID']) > 0){
                                                        echo '<a href="messenger.php?startchat='.user::getUUIDFromID($mysqli, $userid).'" class="btn btn-info">Gesprek</a>';
                                                    }?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-9 panel panel-default">
                                            <p><span class="text-muted">Naam:</span></p>
                                            <p><?php echo userpage::getName($mysqli, $userid);?></p>
                                            <br />
                                            <p><span class="text-muted">Over:</span></p>
                                            <p><?php echo userpage::getAbout($mysqli, $userid);?></p>
                                            <br />
                                            <p><span class="text-muted">Werkzaam bij:</span></p>
                                            <p><?php echo userpage::getActiveJobsOnParkCraft($mysqli, $userid);?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Laatste reacties</h3>
                                </div>
                                <div class="panel-body">
                                    <?php userpage::loadReactionsOfUser($mysqli, $userid);?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 right-container well">
                        <h4 class="text-danger">Wie te volgen</h4>
                        <?php park::loadWhoToFollow($mysqli, $_SESSION['UUID']);?>
                        <?php ads::skycraper($mysqli, basename(__FILE__));?>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    </body>
    </html>
<?php
mysqli_close($mysqli);
?>