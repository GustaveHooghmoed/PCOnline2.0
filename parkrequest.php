<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
$cvwl = false;
include 'includes/phpimports.php';
$parkname;
$parkip;
$parktwitter;
$parkemail;
if(isset($_POST['submit'])) {
    $parkname = $_POST['name'];
    $parkname = trim($parkname);
    $parkname = strip_tags($parkname);
    $parkname = mysqli_real_escape_string($mysqli, $parkname);
    $parkip = $_POST['ip'];
    $parkip = trim($parkip);
    $parkip = strip_tags($parkip);
    $parkip = mysqli_real_escape_string($mysqli, $parkip);
    $parktwitter = $_POST['twitter'];
    $parktwitter = trim($parktwitter);
    $parktwitter = strip_tags($parktwitter);
    $parktwitter = mysqli_real_escape_string($mysqli, $parktwitter);
    $parkemail = $_POST['email'];
    $parkemail = trim($parkemail);
    $parkemail = strip_tags($parkemail);
    $parkemail = mysqli_real_escape_string($mysqli, $parkemail);
    if (!empty($parkname) && !empty($parkip) && !empty($parktwitter) && !empty($parkemail)) {
        park::request($mysqli, $parkname, $parkip, $parktwitter, $parkemail);
        $info= "Bedankt voor het aanmelden van uw park, uw aanvraag wordt zo snel mogelijk behandelt.";
        $parkname = '';
        $parkip = '';
        $parktwitter = '';
        $parkemail = '';
    } else {
        $warning = "Niet alle velden zijn correct ingevuld.";
    }
}
$active = 'parkrequest';
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
                            <h3 class="panel-title">Park Aanvragen</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <form name="register" id="register" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off" class="form-horizontal">
                                        <div class="form-group">
                                            <label for="title" class="col-md-2 control-label"><span class="text-info">Naam</span></label>
                                            <div class="col-md-10" id="namediv">
                                                <input type="text" class="form-control" name="name" id="name" placeholder="Typ hier de naam van het park" value="<?php echo $parkname;?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="article" class="col-md-2 control-label"><span class="text-info">IP</span></label>
                                            <div class="col-md-10" id="ipdiv">
                                                <input type="text" class="form-control" name="ip" id="ip" placeholder="Typ hier het ip van het park" value="<?php echo $parkip;?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="article" class="col-md-2 control-label"><span class="text-info">Twitter</span></label>
                                            <div class="col-md-10" id="ipdiv">
                                                <input type="text" class="form-control" name="twitter" id="twitter" placeholder="Typ hier de twitternaam van het park" value="<?php echo $parktwitter;?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="article" class="col-md-2 control-label"><span class="text-info">Email</span></label>
                                            <div class="col-md-10" id="emaildiv">
                                                <input type="email" class="form-control" name="email" id="email" placeholder="Typ hier de email van het park" value="<?php echo $parkemail;?>" required>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-raised btn-success" name="submit" id="postbutton">Verzenden</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 right-container well">
                    <h4 class="text-danger">Wie te volgen</h4>
                    <?php park::loadWhoToFollow($mysqli,$_SESSION['UUID']);?>
                    <?php ads::skycraper();?>
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