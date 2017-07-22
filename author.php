<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
$cvwl = false;
include 'includes/phpimports.php';
if(!parkcraft::IsAuthor($mysqli, $_SESSION['UUID'])) {
    header("Location: home.php");
    exit;
}
if(isset($_POST['tutpost'])) {
    $title = $_POST['title'];
    $title = trim($title);
    $title = strip_tags($title);
    $title = mysqli_real_escape_string($mysqli, $title);
    $body = preg_replace("/\r\n|\r/", "<br />", $_POST["tutorial"]);
    $body = strip_tags($body, '<strong>, <i>, <br>');
    $body = trim($body);
    $body = mysqli_real_escape_string($mysqli, $body);
    $bodyimg;
    $imgb = $_FILES['tutorialimage'];
    $bodyimg = common::uploadimage($imgb);
    if(parkcraft::canWriteTutorials($mysqli, $_SESSION['UUID'])) {
        $body = parkcraft::postTutorial($mysqli, $title, $body, $bodyimg);
        header("Location: parkcraft.php?tutorials");
        exit;
    } else {
        header("Location: home.php");
        exit;
    }
    exit;
}
$active = 'staff';
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
                            <?php if(isset($_GET['tutorial'])) {?>
                                <h3 class="panel-title">Tutorials</h3>
                            <?php } else if(isset($_GET['pvdw'])) {?>
                                <h3 class="panel-title">Plugin van de week</h3>
                            <?php } else if(isset($_GET['pvdm'])) {?>
                                <h3 class="panel-title">Park van de maand</h3>
                            <?php } else {?>
                                <h3 class="panel-title">Home</h3>
                            <?php }?>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php if(isset($_GET['tutorial']) && parkcraft::canWriteTutorials($mysqli, $_SESSION['UUID'])) {
                                        ?>
                                        <form name="tutorial" id="tutorial" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data" method="post" autocomplete="off" class="form-horizontal">
                                            <div class="form-group">
                                                <label for="title" class="col-md-2 control-label"><span class="text-info">Titel</span></label>
                                                <div class="col-md-10" id="titlediv">
                                                    <input type="hidden" name="id" value="<?php echo $parkid;?>">
                                                    <input type="text" class="form-control" name="title" id="title" placeholder="Typ hier de titel van de tutorial" value="" required>
                                                </div>
                                            </div>
                                            <div class="form-group">

                                                <label for="article" class="col-md-2 control-label"><span class="text-info">Artikel</span></label>
                                                <div class="col-md-10" id="titlediv">
                                                    <textarea type="text" class="form-control" name="tutorial" id="tutorial" placeholder="Typ hier de tutorial" value="" rows="10" required></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="articleimage" class="col-md-2 control-label"><span class="text-info">Tutorial afbeelding<br /><small><span class="text-danger">Beste afmeting is 500x300 pixels</span></small></span></label>
                                                <div class="col-md-10" id="headdiv">
                                                    <input type="file" id="tutorialimage" multiple="" name="tutorialimage" accept="image/*" onchange="loadBodyPreview(this)">
                                                    <input type="text" readonly="" class="form-control" placeholder="Kies een afbeelding" id="tutorialtext">
                                                    <script>
                                                        $("#tutorialimage").change(function(){
                                                            if($("#tutorialimage").val() == '') {
                                                                document.getElementById("tutorialtext").placeholder = 'Kies een afbeelding';
                                                            } else {
                                                                document.getElementById("tutorialtext").placeholder = $("#articleimage").val().replace(/C:\\fakepath\\/i, '');
                                                            }
                                                        });
                                                    </script>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-raised btn-success" name="tutpost" id="postbutton">Post</button>
                                            </div>
                                        </form>
                                        <?php
                                    } else if(isset($_GET['pvdw']) && parkcraft::canWritePluginVanDeWeek($mysqli, $_SESSION['UUID'])) {
                                        ?>

                                        <?php
                                    } else if(isset($_GET['pvdm']) && parkcraft::canWriteParkVanDeMaand($mysqli, $_SESSION['UUID'])) {
                                        ?>

                                        <?php
                                    } else if(isset($_GET['home'])) {
                                        ?>

                                        <?php
                                    } else {
                                        header("Location: author.php?home=&warning=Geen toegang tot dit gedeelte.");
                                        exit;
                                    }?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 right-container well">
                    <h4 class="text-danger">Snelkoppelingen</h4>
                    <p><a href="?home" class="shortcut"><i class="material-icons">home</i><span>Home</span></a></p>
                    <?php if(parkcraft::canWriteTutorials($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?tutorial" class="shortcut"><i class="material-icons">info</i><span>Tutorial</span></a></p>
                    <?php }
                    if(parkcraft::canWritePluginVanDeWeek($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?pvdw" class="shortcut"><i class="material-icons">view_week</i><span>Plugin van de week</span></a></p>
                    <?php }
                    if(parkcraft::canWriteParkVanDeMaand($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?pvdm" class="shortcut"><i class="material-icons">account_balance</i><span>Park van de maand</span></a></p>
                    <?php }?>
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