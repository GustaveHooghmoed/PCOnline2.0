<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
$cvwl = false;
include 'includes/phpimports.php';
if(isset($_POST['submit']) && isset($_POST['oldpassword']) && isset($_POST['newpassword'])) {
    $old = $_POST['oldpassword'];
    $new = $_POST['newpassword'];
    if(user::changePassword($mysqli, $new, $old, $_SESSION['UUID'])) {
        header("Location: settings.php?info=Uw wachtwoord is gewijzigd");
        exit;
    } else {
        header("Location: settings.php?warning=Het wachtwoord dat u hebt ingevuld bij Oud Wachtwoord is onjuist.");
        exit;
    }
}
if(isset($_GET['email'])) {
    if(isset($_GET['value']) && strcmp($_GET['value'], "on") == 0) {
        user::setReceiveNewsEmails($mysqli, 1);
        header("Location: settings.php");
        exit;
    }
    if(isset($_GET['value']) && strcmp($_GET['value'], "off") == 0) {
        user::setReceiveNewsEmails($mysqli, 0);
        header("Location: settings.php");
        exit;
    }
}
if(isset($_GET['reaction'])) {
    if(isset($_GET['value']) && strcmp($_GET['value'], "on") == 0) {
        user::setReceiveReactionEmails($mysqli, 1);
        header("Location: settings.php");
        exit;
    }
    if(isset($_GET['value']) && strcmp($_GET['value'], "off") == 0) {
        user::setReceiveReactionEmails($mysqli, 0);
        header("Location: settings.php");
        exit;
    }
}
if(isset($_POST['profileedit'])) {
    $userid = $_SESSION['user'];
    $mcname = $_POST['mcname'];
    $mcname = trim($mcname);
    $mcname = strip_tags($mcname);
    $mcname = str_replace("\"", '', $mcname);
    $mcname = str_replace("\'", '', $mcname);
    $mcname = mysqli_real_escape_string($mysqli, $mcname);

    $about = $_POST["about"];
    $about = preg_replace("/\r\n|\r/", "<br />", $about);
    $about = strip_tags($about, '<strong>, <i>, <br>');
    $about = trim($about);
    $about = mysqli_real_escape_string($mysqli, $about);

    $userimg;
    if(!$_FILES['profileimg']['size'] == 0) {
        $imga = $_FILES['profileimg'];
        $userimg = common::uploadimagenotmp($imga);
    }
    userpage::update($mysqli, $userid, $userimg, $about, $mcname);
    header("Location: settings.php?profile=&info=Profiel bijgewerkt!");
    exit;
}
system::copyRightSign();?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ParkCraft Online</title>
        <script src="assets/js/bootstrap.min.js"></script>
        <?php include 'includes/imports.php'; ?>
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
            <div class="col-md-12">
                <div class="col-md-9">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <?php if(isset($_GET['followed'])) {
                                ?><h3 class="panel-title">Parken die je volgt</h3><?
                                $title = 'Parken die je volgt';
                            } else if(isset($_GET['password'])) {
                                ?><h3 class="panel-title">Wachtwoord veranderen</h3><?php
                            } else if(isset($_GET['profile'])) {
                                ?><h3 class="panel-title">Profiel instellingen</h3><?php
                            } else {
                                ?><h3 class="panel-title">Algemene instellingen</h3><?php
                            }?>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <?php if(isset($_GET['followed'])) {
                                            user::loadFollowedParks($mysqli, $_SESSION['UUID']);
                                        } else if(isset($_GET['profile'])) {?>
                                            <form name="profile" id="profile"
                                                  action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                                                  enctype="multipart/form-data" method="post" autocomplete="off"
                                                  class="form-horizontal">
                                                <div class="form-group">
                                                    <label for="profileimg" class="col-md-2 control-label"><span class="text-info">Profielfoto</span></label>

                                                    <div class="col-md-10" id="profilediv">
                                                        <img src="<?php echo userpage::getProfilePicture($mysqli, $_SESSION['user']); ?>" alt=""
                                                             class="logo" id="profileimgprv" style="width: 20%;"/>
                                                        <input type="file" id="profileimg" multiple="" name="profileimg"
                                                               accept="image/*" onchange="LoadPreview(this)">
                                                        <input type="text" readonly="" class="form-control"
                                                               placeholder="Kies een afbeelding" id="profiletext">
                                                        <script>
                                                            $("#profileimg").change(function () {
                                                                if ($("#profileimg").val() == '') {
                                                                    document.getElementById("profiletext").placeholder = 'Kies een afbeelding';
                                                                } else {
                                                                    document.getElementById("profiletext").placeholder = $("#profileimg").val().replace(/C:\\fakepath\\/i, '');
                                                                }
                                                            });
                                                            function LoadPreview(fileInput) {
                                                                var files = fileInput.files;
                                                                for (var i = 0; i < files.length; i++) {
                                                                    var file = files[i];
                                                                    var imageType = /image.*/;
                                                                    if (!file.type.match(imageType)) {
                                                                        continue;
                                                                    }
                                                                    var img = document.getElementById("profileimgprv");
                                                                    img.file = file;
                                                                    var reader = new FileReader();
                                                                    reader.onload = (function (aImg) {
                                                                        return function (e) {
                                                                            aImg.src = e.target.result;
                                                                        };
                                                                    })(img);
                                                                    reader.readAsDataURL(file);
                                                                }
                                                            }
                                                        </script>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="about" class="col-md-2 control-label"><span
                                                                class="text-info">Over jezelf</span></label>
                                                    <div class="col-md-10" id="aboutdiv">
                                                    <textarea type="text" class="form-control" name="about"
                                                              id="about"
                                                              placeholder="Vertel hier wat over jezelf" rows="10"
                                                              required><?php echo str_replace("<br />", "\n", userpage::getAbout($mysqli, $_SESSION['user'])); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mcname" class="col-md-2 control-label"><span
                                                                class="text-info">Je minecraft naam</span></label>
                                                    <div class="col-md-10" id="descdiv">
                                                    <input type="text" class="form-control" name="mcname"
                                                              id="mcname"
                                                              placeholder="Typ hier je minecraft naam" value="<?php echo userpage::getMC($mysqli, $_SESSION['user']); ?>"
                                                              required>
                                                    </div>
                                                </div>
                                                <div class="text-center">
                                                    <button type="submit" class="btn btn-raised btn-success" name="profileedit"
                                                            id="profileedit">Opslaan
                                                    </button>
                                                </div>
                                            </form>
                                            <?php
                                        } else if(isset($_GET['password'])) {?>
                                        <form name="settings" id="settings"
                                              action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                                              enctype="multipart/form-data" method="post" autocomplete="off"
                                              class="form-horizontal">
                                            <div class="form-group">
                                                <label for="oldpassword" class="col-md-2 control-label"><span
                                                            class="text-info">Oud wachtwoord</span></label>
                                                <div class="col-md-10" id="ipdiv">
                                                    <input type="password" class="form-control" name="oldpassword" id="oldpassword"
                                                           placeholder="Typ hier het oude wachtwoord"
                                                           value="" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="newpassword" class="col-md-2 control-label"><span class="text-info">Nieuw wachtwoord</span></label>
                                                <div class="col-md-10" id="emaildiv">
                                                    <input type="password" class="form-control" name="newpassword" id="newpassword"
                                                           placeholder="Typ hier het nieuwe wachtwoord"
                                                           value="" required>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <button type="submit" class="btn btn-raised btn-success" name="submit"
                                                        id="postbutton">Aanpassen
                                                </button>
                                            </div>
                                        </form>
                                        <?php } else {?>
                                            <div class="togglebutton">
                                                <label>
                                                    <div class="btn-group">
                                                        <a class="btn btn-primary <?php if(user::getReceiveNewsEmails($mysqli, $_SESSION['UUID'])) { echo 'active';}?>" href="?email&value=on">
                                                            Aan
                                                        </a>
                                                        <a class="btn btn-danger <?php if(!user::getReceiveNewsEmails($mysqli, $_SESSION['UUID'])) { echo 'active';}?>" href="?email&value=off">
                                                            Uit
                                                        </a>
                                                    </div><br/>Nieuws emails ontvangen van parkcraft
                                                </label>
                                            </div>
                                            <hr />
                                            <div class="togglebutton">
                                                <label>
                                                    <div class="btn-group">
                                                        <a class="btn btn-primary <?php if(user::getReceiveReactionEmails($mysqli, $_SESSION['UUID'])) { echo 'active';}?>" href="?reaction&value=on">
                                                            Aan
                                                        </a>
                                                        <a class="btn btn-danger <?php if(!user::getReceiveReactionEmails($mysqli, $_SESSION['UUID'])) { echo 'active';}?>" href="?reaction&value=off">
                                                            Uit
                                                        </a>
                                                    </div><br/>Email ontvangen wanneer een persoon een reactie geeft op een artikel waar jij al op hebt gereageerd
                                                </label>
                                            </div>
                                            <hr />
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 right-container well">
                    <h4 class="text-danger">Snelkoppelingen</h4>
                    <p><a href="?" class="shortcut"><i class="material-icons">settings</i><span>Algemene instellingen</span></a></p>
                    <p><a href="?profile" class="shortcut"><i class="material-icons">accessibility</i><span>Profiel instellingen</span></a></p>
                    <p><a href="?password" class="shortcut"><i class="material-icons">keyboard</i><span>Wachtwoord</span></a></p>
                    <p><a href="?followed" class="shortcut"><i class="material-icons">keyboard_arrow_right</i><span>Volgend</span></a></p>
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