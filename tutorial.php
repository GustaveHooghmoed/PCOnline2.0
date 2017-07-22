<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
$cvwl = true;
include 'includes/phpimports.php';
$id;
$active = '';
if(isset($_POST['submit']) && isset($_SESSION['UUID'])) {
    if(isset($_SESSION['UUID'])) {
        if (!parkcraft::existTut($mysqli, $_POST['tutorialid'])) {
            header("Location: home.php");
            exit;
        }
        $reaction = $_POST['reaction'];
        $id = $_POST['tutorialid'];
        parkcraft::PlaceReactionTut($mysqli, $id, $reaction);
        header("Location: tutorial.php?id=$id");
        exit;
    }
} else {
   $id = $_GET['id'];
}
if(isset($_GET['remove']) && isset($_SESSION['UUID'])) {
    if(isset($_SESSION['UUID'])) {
        parkcraft::RemoveReactionTut($mysqli, $_GET['remove']);
        header("Location: tutorial.php?id=" . $id);
        exit;
    }
}
if(!parkcraft::existTut($mysqli, $id)) {
    header("Location: home.php");
    exit;
}
if(isset($_REQUEST['like'])) {
    if(isset($_SESSION['UUID'])) {
        parkcraft::likeTut($mysqli, $id, $_SESSION['UUID']);
        header("Location: tutorial.php?id=$id");
        exit;
    }
}
if(isset($_REQUEST['unlike'])) {
    if(isset($_SESSION['UUID'])) {
        parkcraft::unlikeTut($mysqli, $id, $_SESSION['UUID']);
        header("Location: tutorial.php?id=$id");
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
        <title>ParkCraft Online | <?php echo strip_tags(parkcraft::getTitleTut($mysqli, $id));?></title>
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
                            <h3 class="panel-title">Tutorial</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                        parkcraft::loadTutorial($mysqli, $id);
                                        ads::vierkant();
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 right-container well">
                    <h4 class="text-danger">Reacties</h4>
                    <form name="register" id="register" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off" class="form-horizontal">
                        <div class="form-group">
                            <label for="title" class="col-md-2 control-label"><span class="text-info">Reactie</span></label>
                            <div class="col-md-10" id="titlediv">
                                <input type="hidden" name="tutorialid" value="<?php echo $id;?>">
                                <textarea class="form-control" name="reaction" id="reaction" placeholder="Typ hier je reactie" value="" required <?php if(!isset($_SESSION['UUID'])) { echo 'readonly';}?>><?php if(!isset($_SESSION['UUID'])) { echo 'Je moet inloggen om een reactie te plaatsen';}?></textarea>
                                <button type="submit" class="btn btn-raised btn-success" name="submit" id="postbutton">Plaats</button>
                            </div>
                        </div>
                    </form>
                    <h4>Reacties:</h4>
                    <?php parkcraft::loadReactionsTut($mysqli, $id);?>
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