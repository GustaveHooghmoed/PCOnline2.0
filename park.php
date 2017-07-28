<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
$cvwl = false;
include 'includes/phpimports.php';
$parkid = $_REQUEST['id'];
$active = '';
if(!park::exist($mysqli, $parkid) || (park::isDeleted($mysqli, $parkid))) {
    header("Location: home.php");
    exit;
}
if(isset($_GET['follow'])) {
    $parkid = $_GET['id'];
    if(!user::IsFollowingPark($mysqli, $parkid, $_SESSION['UUID'])) {
        park::follow($mysqli, $parkid, $_SESSION['UUID']);
        if(isset($_GET['bts'])) {
            header("Location: settings.php?followed");
            exit;
        }
        header("Location: park.php?id=$parkid");
        exit;
    }
}
if(isset($_GET['unfollow'])) {
    $parkid = $_GET['id'];
    if(user::IsFollowingPark($mysqli, $parkid, $_SESSION['UUID'])) {
        park::unfollow($mysqli, $parkid, $_SESSION['UUID']);
        if(isset($_GET['bts'])) {
            header("Location: settings.php?followed");
            exit;
        }
        header("Location: park.php?id=$parkid");
        exit;
    }
}
    ?>
<?php system::copyRightSign();?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ParkCraft Online | <?php echo strip_tags(park::getName($mysqli, $parkid));?></title>
        <script src="assets/js/bootstrap.min.js"></script>
        <?php include 'includes/imports.php'; ?>
        <style>
            body {
                background: url(<?php echo park::getBackrgound($mysqli, $parkid);?>);
                transition: background 0.5s linear;
                background-position: center;
                background-size: 100% 100%;
                background-size: cover !important;
                background-repeat: no-repeat;
                background-attachment: fixed !important;
                color: black;
            }
        </style>
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
            <div style=" height: 300px; background: url('<?php echo park::getHeader($mysqli, $parkid);?>'); background-position: center;
                    background-size: cover;
                    background-repeat: no-repeat;" class="panel panel-success">
            </div>
            <div class="col-md-12">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="navbar-inner">
                            <div class="container-fluid">
                                <div class="navbar-header">
                                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                        <span class="sr-only">Toggle navigation</span>
                                        <span class="icon-bar hamburger"></span>
                                        <span class="icon-bar hamburger"></span>
                                        <span class="icon-bar hamburger"></span>
                                    </button>
                                </div>

                                <!-- Collect the nav links, forms, and other content for toggling -->
                                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                    <ul class="nav navbar-nav">
                                        <?php if(isset($_GET['rides'])) {?>
                                            <li><a href="?id=<?php echo $parkid;?>" class="btn btn-primary btn-sm">Park pagina</a></li>
                                        <?php } else {?>
                                            <li><a href="?id=<?php echo $parkid;?>&rides" class="text-muted btn btn-primary btn-sm">Statussen <span class="badge"><?php echo rides::countRidesOfPark($mysqli, $parkid);?></span></a></li>
                                        <?php } ?>
                                        <?php if(isset($_GET['vacatures'])) {?>
                                            <li><a href="?id=<?php echo $parkid;?>" class="btn btn-primary btn-sm">Park pagina</a></li>
                                        <?php } else {?>
                                            <li><a href="?id=<?php echo $parkid;?>&vacatures" class="text-muted btn btn-primary btn-sm">Vacatures <span class="badge"><?php echo vacature::vacaturesCount($mysqli, $parkid);?></span></a></li>
                                        <?php } ?>
                                        <?php if(isset($_GET['volgers'])) {?>
                                            <li><a href="?id=<?php echo $parkid;?>" class="btn btn-primary btn-sm">Park pagina</a></li>
                                        <?php } else {?>
                                            <li><a href="?id=<?php echo $parkid;?>&volgers" class="btn btn-primary btn-sm">Volgers <span class="badge"><?php echo park::getFollowers($mysqli, $parkid);?></span></a></li>
                                        <?php } ?>
                                            <li><a class="btn btn-primary btn-sm">Spelers online<span class="badge" id="playersonline"></span></a></li>
                                            <script>playersOnline("playersonline", "<?php echo park::getIp($mysqli, $parkid);?>");</script>
                                        <?php if(staff::canManageParks($mysqli, $_SESSION['UUID'])) {?>
                                            <li><a href="parksettings.php?id=<?php echo $parkid;?>" class="btn btn-success btn-sm">Beheren</a></li>
                                        <?php } ?>
                                        <?php if(user::IsFollowingPark($mysqli, $parkid, $_SESSION['UUID'])) {?>
                                            <li><a href="?id=<?php echo $parkid;?>&unfollow" class="btn btn-danger btn-sm" role="button">Ontvolgen</a></li>
                                        <?php } else {?>
                                            <li><a href="?id=<?php echo $parkid;?>&follow" class="btn btn-primary btn-sm" role="button">Volgen</a></li>
                                        <?php } ?>
                                    </ul>
                                </div><!-- /.navbar-collapse -->
                            </div><!-- /.container-fluid -->
                        </div>
                    </div>
                </div>
            </div>
            <?php
            if(isset($_GET['rides'])) {
                ?>
                <div class="col-md-12">
                    <div class="col-md-9">
                        <div class="panel panel-info">
                            <?php rides::loadForParkPage($mysqli, $parkid);?>
                        </div>
                    </div>
                    <div class="col-md-3 right-container well">
                        <h4 class="text-danger">Wie te volgen</h4>
                        <?php park::loadWhoToFollow($mysqli, $_SESSION['UUID']);?>
                        <?php ads::skycraper($mysqli, basename(__FILE__));?>
                    </div>
                </div>
                <?php
            } else if(isset($_GET['vacatures'])) {
                ?>
                <div class="col-md-12">
                    <div class="col-md-9">
                        <div class="panel panel-info">
                            <?php vacature::loadVacatures($mysqli, $parkid);?>
                        </div>
                    </div>
                    <div class="col-md-3 right-container well">
                        <h4 class="text-danger">Wie te volgen</h4>
                        <?php park::loadWhoToFollow($mysqli, $_SESSION['UUID']);?>
                        <?php ads::skycraper($mysqli, basename(__FILE__));?>
                    </div>
                </div>
                <?php
            } else if(isset($_GET['volgers'])) {
                ?>
                <div class="col-md-12">
                    <div class="col-md-9">
                        <div class="panel panel-info">
                            <?php park::LoadFollowers($mysqli, $parkid);?>
                        </div>
                    </div>
                    <div class="col-md-3 right-container well">
                        <h4 class="text-danger">Wie te volgen</h4>
                        <?php park::loadWhoToFollow($mysqli, $_SESSION['UUID']);?>
                        <?php ads::skycraper($mysqli, basename(__FILE__));?>
                    </div>
                </div>
                <?php
            } else {?>
                <div class="col-md-12">
                    <div class="col-md-3 left-container well">
                        <div>
                            <img src="<?php echo park::getLogo($mysqli, $parkid);?>" alt="logo" class="img-responsive center-block" style="max-height: 300px;"/>
                        </div>
                        <p><strong><?php echo park::getName($mysqli, $parkid);?></strong></p>
                        <br />
                        <p style="word-wrap: break-word;"><?php echo park::getDescription($mysqli, $parkid);?></p>
                        <?php
                        if(!empty(park::getIp($mysqli, $parkid))) {
                        ?>
                            <br />
                            <p>IP: <?php echo park::getIp($mysqli, $parkid);?></p>
                        <?php }?>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-info">
                            <?php
                                article::loadArticlesPark($mysqli, $parkid);
                            ?>
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