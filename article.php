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
        if (!article::exist($mysqli, $_POST['articleid'])) {
            header("Location: home.php");
            exit;
        }
        $reaction = $_POST['reaction'];
        $id = $_POST['articleid'];
        $reaction = trim($reaction);
        $reaction = strip_tags($reaction);
        $reaction = mysqli_real_escape_string($mysqli, $reaction);
        $id = trim($id);
        $id = strip_tags($id);
        $id = mysqli_real_escape_string($mysqli, $id);
        article::PlaceReaction($mysqli, $id, $reaction);
        article::sendReactionEmail($mysqli, $id, $_SESSION['UUID'], $reaction);
        header("Location: article.php?id=$id");
    }
} else {
   $id = $_GET['id'];
}
if(isset($_GET['remove']) && isset($_SESSION['UUID'])) {
    if(isset($_SESSION['UUID'])) {
        article::RemoveReaction($mysqli, $_GET['remove']);
        header("Location: article.php?id=" . $id);
        exit;
    }
}
if(!article::exist($mysqli, $id)) {
    header("Location: home.php");
    exit;
}
if(isset($_REQUEST['like'])) {
    if(isset($_SESSION['UUID'])) {
        article::like($mysqli, $id, $_SESSION['UUID']);
        header("Location: article.php?id=$id");
        exit;
    }
}
if(isset($_REQUEST['unlike'])) {
    if(isset($_SESSION['UUID'])) {
        article::unlike($mysqli, $id, $_SESSION['UUID']);
        header("Location: article.php?id=$id");
        exit;
    }
}
statistics::articleVisit($mysqli, $_SESSION['UUID'], $id);
?>
<?php system::copyRightSign();?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ParkCraft Online | <?php echo strip_tags(article::getTitle($mysqli, $id));?></title>
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
                    <?php if(isset($_REQUEST['likes'])) {?>
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3 class="panel-title">Gebruikers die dit artikel leuk vonden</h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php article::LoadLikes($mysqli, $id); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } else {?>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">Artikel</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <style>
                                        .carousel-control {
                                            color: black;
                                        }
                                        .carousel{
                                            background: white;
                                        }
                                        .carousel-control.left {
                                            background-image: none;
                                        }
                                        .carousel-control.right {
                                            background-image: none;
                                        }
                                    </style>
                                    <?php article::loadArticle($mysqli, $id);?>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3 class="panel-title">Reacties</h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="text-danger">Reacties</h4>
                                        <form name="register" id="register" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off" class="form-horizontal">
                                            <div class="form-group">
                                                <label for="title" class="col-md-1 control-label"><span class="text-info">Reactie</span></label>
                                                <div class="col-md-12" id="titlediv">
                                                    <input type="hidden" name="articleid" value="<?php echo $id;?>">
                                                    <textarea class="form-control" name="reaction" id="reaction" placeholder="Typ hier je reactie" value="" required <?php if(!isset($_SESSION['UUID'])) { echo 'readonly';}?>><?php if(!isset($_SESSION['UUID'])) { echo 'Je moet inloggen om een reactie te plaatsen';}?></textarea>
                                                    <button type="submit" class="btn btn-raised btn-success" name="submit" id="postbutton">Plaats</button>
                                                </div>
                                            </div>
                                        </form>
                                        <h4>Reacties:</h4>
                                        <?php article::loadReactions($mysqli, $id);?>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php }?>
                </div>

                <div class="col-md-3 right-container well">
                    <?php ads::skycraper(); ?>
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