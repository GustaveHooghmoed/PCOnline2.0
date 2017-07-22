<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
ob_start();
session_start();
$email = '';
$name = '';
$cvwl = false;
include 'includes/connectdb.php';
include 'includes/PCO_API.php';
if(isset($_POST['submit'])) {
    require_once "includes/recaptchalib.php";
    $recaptha = $_POST["g-recaptcha-response"];
    $secret = "6LdRqRkUAAAAAGm3RGJKbUciYUF9yr4SsJ5UgEWj";
    $name = $_POST['name'];
    $email = $_POST['email'];
    $reCaptcha = new ReCaptcha($secret);
    $response = $reCaptcha->verifyResponse(
        $_SERVER["REMOTE_ADDR"],
        $recaptha
    );
    if($response != null && $response->success) {
        $name = trim($name);
        $name = strip_tags($name);
        $name = mysqli_real_escape_string($mysqli, $name);

        $email = trim($email);
        $email = strip_tags($email);
        $email = mysqli_real_escape_string($mysqli, $email);


        $password = $_POST['pass'];
        $passrepeat = $_POST = ['passrepeat'];
        if (strcmp($password, $passrepeat) != 0) {
            $warning = "De wachtwoorden komen niet overeen.";
        } else {
            if (user::exist($mysqli, $email)) {
                $warning = "Dit email adres is al in gebruik.";
            } else {
                if (user::register($mysqli, $name, $email, $password)) {
                    $info = "Succesvol geregistreerd. Ga naar je mail om je account te activeren. (Bekijk ook de spam map in uw email)";
                } else {
                    $danger = "Er is een fout opgetreden, probeer het later opniew.";
                }
            }
        }
    } else {
        $info = "Vul aub de captcha in!";
    }
}
if(isset($_SESSION['user'])) {
    if(!user::hasAccess($mysqli, $_SESSION['UUID'])) {
    } else {
        if(!user::isActivated($mysqli, $_SESSION['UUID'])) {
        } else {
            header("Location: home.php");
            exit;
        }
    }
}
$active = 'home';
?>
<?php system::copyRightSign();?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ParkCraft Online</title>
    <?php include 'includes/imports.php';?>
    <link href="includes/css/login.css" rel="stylesheet">
</head>
<body>
<div class="container login">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2 logincontainer">
            <div class="logo">
                <img src="resources/header.png" alt="logo">
            </div>
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
            <div class="panel panel-danger">
                <div class="panel-heading text-center">Registreren</div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <form name="register" id="register" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off" class="form-horizontal">
                            <div class="form-group">
                                <label for="name" class="col-md-2 control-label"><span class="text-info">Naam</span></label>
                                <div class="col-md-10" id="namediv">
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Typ hier jouw naam" value="<?php echo $name;?>" onkeyup="namecheck()" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-md-2 control-label"><span class="text-info">Email</span></label>
                                <div class="col-md-10" id="emaildiv">
                                    <input type="text" class="form-control" name="email" id="email" placeholder="Typ hier jouw email" value="<?php echo $email;?>" onkeyup="emailcheck()" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="pass" class="col-md-2 control-label"><span class="text-info">Wachtwoord</span></label>
                                <div class="col-md-10" id="passdiv">
                                    <input type="password" class="form-control" name="pass" id="pass" placeholder="Typ hier jouw wachtwoord" value="" onkeyup="passcheck()" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="passrepeat" class="col-md-2 control-label"><span class="text-info">Wachtwoord herhalen</span></label>
                                <div class="col-md-10" id="passrepeatdiv">
                                    <input type="password" class="form-control" name="passrepeat" id="passrepeat" placeholder="Herhaal jouw wachtwoord" value="" onkeyup="passcheck()" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="recaptcha" class="col-md-2 control-label"><span class="text-info">ReCaptcha</span></label>
                                <div class="col-md-10" id="recaptcha">
                                    <div class="g-recaptcha" data-sitekey="6LdRqRkUAAAAAMX2BY0GOC7SSx-jsAeT14_uZubB"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>
                                    Als je op de knop registreren drukt ga je akkoord met de <a href="resources/Privacyreglement.pdf" target="_blank">Algemene Voorwaarden</a>.
                                </label>
                            </div>
                            <div class="text-center">
                                <a href="index.php" class="btn btn-raised btn-primary" name="submit">Terug</a>
                                <button type="submit" class="btn btn-raised btn-success" name="submit" id="registerbutton">Registreren</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/register.js"></script>
<script>buttonendis();</script>
</body>
</html>
<?php
mysqli_close($mysqli);
?>
