<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
$cvwl = false;
include 'includes/phpimports.php';
$job_id;
if(isset($_REQUEST['id'])) {
    $job_id = $_REQUEST['id'];
    if(!vacature::excist($mysqli, $job_id)) {
        header("Location: home.php");
        exit;
    }
} else {
    header("Location: home.php");
    exit;
}
$naam;
$reden;
$email;
$skype;
$kennis;
$about;
$extra;
if(isset($_POST['submit'])) {
    $naam = $_POST['name'];
    $naam = trim($naam);
    $naam = strip_tags($naam);
    $naam = mysqli_real_escape_string($mysqli, $naam);

    $reden = $_POST['reden'];
    $reden = trim($reden);
    $reden = preg_replace("/\r\n|\r/", "<br />", $reden);
    $reden = strip_tags($reden, '<strong>, <i>, <br>');
    $reden = mysqli_real_escape_string($mysqli, $reden);

    $email = $_POST['email'];
    $email = trim($email);
    $email = strip_tags($email);
    $email = mysqli_real_escape_string($mysqli, $email);

    $about = $_POST['about'];
    $about = trim($about);
    $about = preg_replace("/\r\n|\r/", "<br />", $about);
    $about = strip_tags($about, '<strong>, <i>, <br>');
    $about = mysqli_real_escape_string($mysqli, $about);

    $skype = $_POST['skype'];
    $skype = trim($skype);
    $skype = strip_tags($skype);
    $skype = mysqli_real_escape_string($mysqli, $skype);

    $kennis = $_POST['kennis'];
    $kennis = trim($kennis);
    $kennis = preg_replace("/\r\n|\r/", "<br />", $kennis);
    $kennis = strip_tags($kennis, '<strong>, <i>, <br>');
    $kennis = mysqli_real_escape_string($mysqli, $kennis);

    $extra = $_POST['extra'];
    $extra = trim($extra);
    $extra = preg_replace("/\r\n|\r/", "<br />", $extra);
    $extra = strip_tags($kennis, '<strong>, <i>, <br>');
    $extra = mysqli_real_escape_string($mysqli, $extra);

    if (!empty($naam) && !empty($reden) && !empty($email) && !empty($skype) && !empty($kennis) && !empty($extra) && !empty($about)) {
        vacature::apply($mysqli, $naam, $about, $reden, $email, $skype, $kennis, $extra, $job_id);
        $info= "Bedankt voor het solliciteren, uw sollicitatie wordt zo snel mogelijk behandelt.";
        $naam = '';
        $reden = '';
        $email = '';
        $about = '';
        $skype = '';
        $kennis = '';
        $extra = '';
    } else {
        $warning = "Niet alle velden zijn correct ingevuld.";
    }
}
$active = '';
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
                    <ol class="breadcrumb">
                        <li><a href="applications.php">Vacatures</a></li>
                        <li class="active">Vacature: <?php echo vacature::getName($mysqli, $job_id);?></li>
                    </ol>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">Sollicitatie formulier</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <form name="register" id="register" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off" class="form-horizontal">
                                        <div class="form-group">
                                            <label for="name" class="col-md-2 control-label"><span class="text-info">Naam</span></label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="name" id="name" placeholder="Jouw naam" value="<?php echo $naam;?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="extra" class="col-md-2 control-label"><span class="text-info">Vertel wat over jezelf</span></label>
                                            <div class="col-md-10">
                                                <textarea class="form-control area" name="about" id="about" placeholder="Leeftijd, woonplaats etc."required><?php echo $about;?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="email" class="col-md-2 control-label"><span class="text-info">Email</span></label>
                                            <div class="col-md-10">
                                                <input type="email" class="form-control" name="email" id="email" placeholder="Jouw email" value="<?php echo $email;?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="skype" class="col-md-2 control-label"><span class="text-info">Skype</span></label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="skype" id="skype" placeholder="Jouw skypenaam" value="<?php echo $skype;?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="kennis" class="col-md-2 control-label"><span class="text-info">Kennis</span></label>
                                            <div class="col-md-10">
                                                <textarea class="form-control area" name="kennis" id="kennis" placeholder="Heb je al eerder z'n functie gehad als deze? Zoja, waar en wanneer?"required><?php echo $kennis;?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="reden" class="col-md-2 control-label"><span class="text-info">Motivatie</span></label>
                                            <div class="col-md-10">
                                                <textarea class="form-control area" name="reden" id="reden" placeholder="Wat is de motivatie om hier te gaan werken? Waarom zou je hier willen werken?"required><?php echo $reden;?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="extra" class="col-md-2 control-label"><span class="text-info">Extra informatie</span></label>
                                            <div class="col-md-10">
                                                <textarea class="form-control area" name="extra" id="extra" placeholder="Nog iets anders te vertellen?"required><?php echo $extra;?></textarea>
                                            </div>
                                        </div>
                                        <input type="hidden" class="form-control" name="id" id="id" value="<?php echo $job_id;?>" required>
                                        <script>
                                            $(".area").keypress(function(event) {
                                                if (event.which == 13) {
                                                    event.preventDefault();
                                                }
                                            });
                                        </script>
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