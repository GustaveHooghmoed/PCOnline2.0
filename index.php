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
$cvwl = false;
include 'includes/connectdb.php';
include 'includes/PCO_API.php';
if(isset($_POST['submit'])) {

    $email = $_POST['email'];
    $email = trim($email);
    $email = strip_tags($email);
    $email = mysqli_real_escape_string($mysqli, $email);
    $password = $_POST['password'];
    $redirect = '/home.php';
    $remeberme = false;
    if(isset($_POST['rememberme'])) {
        $remeberme = true;
    }
    if(isset($_POST['redirect'])) {
        $redirect = $_POST['redirect'];
    }
    if(!user::login($mysqli, $email, $password, $redirect, $remeberme)) {
        $warning= "De gegevens die u heeft ingevuld zijn onjuist.";
    }
}
if(isset($_POST['passrequest'])) {
    user::sendChangePassword($mysqli, $_POST['email']);
    header("Location: index.php?info=Er is een mail verzonden naar het ingevoerde email adres!");
    exit;
}
$cp = '';
if(isset($_GET['changepassword'])) {
    $code = $_GET['code'];
    $code = trim($code);
    $code = strip_tags($code);
    $code = mysqli_real_escape_string($mysqli, $code);
    if(user::IsPasswordCodeValid($mysqli, $code)) {
        $cp = $code;
    } else {
        $warning = "Deze wachtwoord herstel code is ongeldig!";
    }
}
if(isset($_POST['newpassword'])) {
    $code = $_POST['code'];
    $code = trim($code);
    $code = strip_tags($code);
    $code = mysqli_real_escape_string($mysqli, $code);
    $password = $_POST['pass1'];
    if(user::IsPasswordCodeValid($mysqli, $code)) {
        user::changeForgotPassword($mysqli, $password, $code);
        header("Location: index.php?info=Uw wachtwoord is met succes veranderd!");
        exit;
    }
}
if(isset($_SESSION['user'])) {
    if(!user::hasAccess($mysqli, $_SESSION['UUID'])) {
        $warning= "Je hebt geen toegang tot ParkCraft Online.";
    } else {
        if(!user::isActivated($mysqli, $_SESSION['UUID'])) {
            $info= "Je hebt account is nog niet geactiveerd.";
        } else {
            header("Location: home.php");
            exit;
        }
    }
}
if(isset($_COOKIE["pcoemail"]) && isset($_COOKIE["pcosessionid"])) {
    $redirect = '/home.php';
    if(isset($_GET['redirect'])) {
        $redirect = $_GET['redirect'];
    }
    if(!user::loginWithCookie($mysqli, $_COOKIE["pcoemail"], $_COOKIE["pcosessionid"], $redirect)) {
        $warning = 'Cookie is niet geldig, log opnieuw in a.u.b!';
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
                <div class="panel-heading text-center">Inloggen op ParkCraft Online</div>
                <div class="panel-body">
                    <div class="row">
                        <div>
                            <a href="https://www.mixamcraft.be/">
                                <img src="https://i.imgur.com/uFsNiEB.gif" alt="reclame" class="img-responsive center-block">
                            </a>
                        </div>
                        <div class="col-md-6 ">
                            <div style="border-right: 1px solid #eeeeee; padding-left: 20px; padding-right: 35px;">
                                <form name="signin" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off" class="form-horizontal">
                                    <div class="form-group">
                                        <label for="email"><span class="text-info">Email</span></label>
                                        <input type="email" name="email" id="email" placeholder="Vul hier je email in." value="<?php echo $email;?>" class="form-control" autofocus="autofocus" required="required">
                                    </div>
                                    <div class="form-group">
                                        <label for="password"><span class="text-info">Wachtwoord</span></label>
                                        <input type="password" name="password" id="password" placeholder="Vul hier je wachtwoord in." value="" class="form-control" autofocus="autofocus" required="required">
                                    </div>
                                    <?php     if(isset($_GET['redirect'])) {?>
                                        <input type="hidden" name="redirect" id="redirect" value="<?php echo $_GET['redirect'];?>" class="form-control">
                                    <?php }?>
                                    <div class="form-group">
                                        <input type="checkbox" value="rememberme" id="rememberme" name="rememberme">
                                        <label for="rememberme"><span class="text-info">Ingelogd blijven</span></label>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-raised btn-success" name="submit">Login</button>
                                    </div>
                                    <div class="text-center">
                                        <a href="" data-toggle="modal" data-target="#forgotpassword"><span class="text-danger">Wachtwoord vergeten</span></a>
                                    </div>
                                </form>
                                <div id="forgotpassword" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">Wachtwoord vergeten</h4>
                                            </div>
                                            <div class="modal-body">
                                                <form name="pass" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off" class="form-horizontal">
                                                    <div class="form-group">
                                                        <label for="email"><span class="text-info">Email</span></label>
                                                        <input type="email" name="email" id="email" placeholder="Vul hier je email in." value="<?php echo $email;?>" class="form-control" autofocus="autofocus" required="required">
                                                    </div>
                                                    <div class="text-center">
                                                        <button type="submit" class="btn btn-raised btn-success" name="passrequest" >Aanvragen</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <?php if(!empty($cp)) {
                                    ?>
                                    <div id="newpassword" class="modal fade" role="dialog">
                                        <div class="modal-dialog">

                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">Nieuw wachtwoord</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form name="pass" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off" class="form-horizontal">
                                                        <div class="form-group">
                                                            <label for="pass1"><span class="text-info">Wachtwoord</span></label>
                                                            <input type="hidden" name="code" id="code" value="<?php echo $cp;?>">
                                                            <input type="password" name="pass1" id="pass1" placeholder="Vul hier je nieuwe wachtwoord in." value="" class="form-control" autofocus="autofocus" required="required">
                                                        </div>
                                                        <div class="text-center">
                                                            <button type="submit" class="btn btn-raised btn-success" name="newpassword" >Toepassen</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        $(window).load(function(){
                                            $('#newpassword').modal('show');
                                        });
                                    </script>
                                    <?php
                                }?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h3>Functies ParkCraft Online</h3>
                            <ul>
                                <li><h4>Parken volgen</h4></li>
                                <li><h4>Je park toevoegen aan de community</h4></li>
                                <li><h4>Artikelen schrijven voor je park</h4></li>
                                <li><h4>Je parkpagina volledig aanpassen</h4></li>
                                <li><h4>En nog veel meer...</h4></li>
                            </ul>
                            <div class="text-center">
                                <a href="register.php" class="btn btn-raised btn-danger">Registreren</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
<?php     mysqli_close($mysqli);?>
