<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
$cvwl = false;
include 'includes/phpimports.php';
include 'includes/HTMLPurifier.standalone.php';
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$config->set('HTML.SafeIframe', true);
$config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo

$parkid = $_REQUEST['id'];
if(!park::exist($mysqli, $parkid) || (park::isDeleted($mysqli, $parkid))) {
    header("Location: home.php");
    exit;
}
if(!park::IsUserStaff($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
    header("Location: index.php");
    exit;
}
if(isset($_GET['removeapplication'])) {
    if(!park::CanManageJobs($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: parksettings.php?id=$parkid&home=&warning=Geen toegang tot dit gedeelte.");
        exit;
    }
    $id = $_GET['removeapplication'];
    $jobid = $_GET['pi'];
    vacature::removeApplication($mysqli, $id);
    header("Location: parksettings.php?id=$parkid&jobs=&application=$jobid&info=Sollicitatie verwijderd!");
    exit;
} else if(isset($_GET['offeragain'])) {
    if(!park::CanManageJobs($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: parksettings.php?id=$parkid&home=&warning=Geen toegang tot dit gedeelte.");
        exit;
    }
    $id = $_GET['offeragain'];
    vacature::opnieuwAanbieden($mysqli, $id);
    header("Location: parksettings.php?id=$parkid&jobs=&info=Vacature opnieuw aangeboden!!");
    exit;
} else if(isset($_GET['removevacature'])){
    if(!park::CanManageJobs($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
        exit;
    }
    $parkid = $_GET['id'];
    $vacature = $_GET['removevacature'];
    vacature::removeVacature($mysqli, $parkid, $vacature);
    header("Location: parksettings.php?id=$parkid&jobs=&info=Vacature verwijderd!");
    exit;
} else if(isset($_POST['editvacature'])) {
    $name = $_POST['naam'];
    $name = strip_tags($name);
    $name = trim($name);
    $name = mysqli_real_escape_string($mysqli, $name);
    $description = $_POST['beschrijving'];
    $body = preg_replace("/\r\n|\r/", "<br />", $description);
    $body = strip_tags($body, '<strong>, <i>, <br>');
    $body = trim($body);
    $body = mysqli_real_escape_string($mysqli, $body);
    $jobid = $_POST['job_id'];
    if(park::CanManageJobs($mysqli, $parkid, $_SESSION['UUID']) || staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        vacature::editVacature($mysqli, $parkid, $name, $body, $jobid);
        header("Location: parksettings.php?id=$parkid&jobs=&info=Vacature aangepast!");
        exit;
    }
    header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
    exit;
}else if(isset($_POST['addvacature'])) {
    $name = $_POST['naam'];
    $name = strip_tags($name);
    $name = trim($name);
    $name = mysqli_real_escape_string($mysqli, $name);
    $description = $_POST['beschrijving'];
    $body = preg_replace("/\r\n|\r/", "<br />", $description);
    $body = strip_tags($body, '<strong>, <i>, <br>');
    $body = trim($body);
    $body = mysqli_real_escape_string($mysqli, $body);
    if(park::CanManageJobs($mysqli, $parkid, $_SESSION['UUID']) || staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        vacature::addVacature($mysqli, $parkid, $name, $body);
        header("Location: parksettings.php?id=$parkid&jobs=&info=Vacature geplaatst!");
        exit;
    }
    header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
    exit;
}else if(isset($_POST['editpostbutton'])) {
    $title = $_POST['titel'];
    $title = strip_tags($title);
    $title = trim($title);
    $title = mysqli_real_escape_string($mysqli, $title);
    //$article = preg_replace("/\r\n|\r/", "[enter]", $article);
    //$article = strip_tags($article, '<strong>, <i>, <br>');
    //$article = trim($article);
    //$article = mysqli_real_escape_string($mysqli, $article);
    $id = $_POST['postid'];
    $parkid = $_POST['id'];
    //$body = preg_replace("/\r\n|\r/", "<br />", $article);
    //$body = strip_tags($body, '<strong>, <i>, <br>');
    //$body = trim($body);
	$body = $purifier->purify($_POST['article']);
    $body = mysqli_real_escape_string($mysqli, $body);
    if(park::CanWriteArticle($mysqli, $parkid, $_SESSION['UUID']) || staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        if(article::editArticle($mysqli, $parkid, $id, $title, $body, $_SESSION['UUID'])) {
            header("Location: parksettings.php?id=$parkid&postedit=&info=Artikel met succes bijgewerkt!");
            exit;
        } else {
            header("Location: parksettings.php?id=$parkid&warning=Er is een fout opgetreden!");
            exit;
        }
    }
    header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
    exit;
}else if(isset($_GET['removearticle'])){
    if(!park::CanWriteArticle($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
        exit;
    }
    $parkid = $_GET['id'];
    $postid = $_GET['removearticle'];
    article::deletepost($mysqli, $parkid, $postid, $_SESSION['UUID']);
    header("Location: parksettings.php?id=$parkid&postedit");
    exit;
} else if(isset($_GET['undoremovearticle'])){
    if(!park::CanWriteArticle($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
        exit;
    }
    $parkid = $_GET['id'];
    $postid = $_GET['undoremovearticle'];
    article::undeletepost($mysqli, $parkid, $postid, $_SESSION['UUID']);
    header("Location: parksettings.php?id=$parkid&postedit");
    exit;
} else if(isset($_GET['removestaff'])) {
    if(!park::CanManageStaff($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
        exit;
    }
    $parkid = $_GET['id'];
    $user = $_GET['removestaff'];
    park::removestaff($mysqli, $parkid, $user);
    header("Location: parksettings.php?id=$parkid&editstaff");
    exit;
} else if(isset($_POST['addstaff'])) {
    if (!park::CanManageStaff($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
        exit;
    }
    $parkid = $_POST['id'];
    $email = $_POST['email'];
    if (!park::addstaff($mysqli, $parkid, $email)) {
        header("Location: parksettings.php?id=$parkid&editstaff=&warning=Dit email adres komt niet voor in de database");
        exit;
    }
    header("Location: parksettings.php?id=$parkid&editstaff");
    exit;
} else if(isset($_POST['changeowner'])) {
    if(!park::IsUserOwner($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
        exit;
    }
    $parkid = $_POST['id'];
    $email = $_POST['email'];
    if(!park::changeOwner($mysqli, $parkid, $email)) {
        header("Location: parksettings.php?id=$parkid&editstaff=&warning=Dit email adres komt niet voor in de database");
        exit;
    }
    header("Location: parksettings.php?id=$parkid");
    exit;
} else if(isset($_POST['deletepark'])) {
    if(!park::IsUserOwner($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
        exit;
    }
    $parkid = $_POST['id'];
    park::delete($mysqli, $parkid, 1);
    header("Location: parksettings.php?id=$parkid");
    exit;
} else if(isset($_POST['addride'])) {
    if(!park::CanManageRides($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
        exit;
    }
    $parkid = $_POST['id'];
    $ridename = $_POST['ridename'];
    rides::addride($mysqli, $parkid, $ridename);
    header("Location: parksettings.php?id=$parkid&rides=&info=Attractie toegevoegd!");
    exit;
} else if(isset($_POST['addshow'])) {
    if(!park::CanManageRides($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
        exit;
    }
    $parkid = $_POST['id'];
    $ridename = $_POST['showname'];
    rides::addshow($mysqli, $parkid, $ridename);
    header("Location: parksettings.php?id=$parkid&rides=&info=Show toegevoegd!");
    exit;
} else if(isset($_GET['removeride'])) {
    if(!park::CanManageRides($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
        exit;
    }
    $parkid = $_GET['id'];
    $ridecode = $_GET['removeride'];
    rides::removeride($mysqli, $ridecode);
    header("Location: parksettings.php?id=$parkid&rides");
    exit;
} else if(isset($_POST['edit'])) {
    if(!park::CanManageStaff($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
        exit;
    }
    $useruuid = $_POST['useruuid'];
    $parkid = $_POST['id'];
    $writearticle = $_POST['cw'];
    $caneditsettings = $_POST['ces'];
    $canmanagestaff = $_POST['cms'];
    $canmanagerides = $_POST['cmr'];
    $canmanagejobs = $_POST['cmj'];
    $prefix = $_POST['prefix'];
    park::editstaff($mysqli, $parkid, $useruuid, $writearticle, $caneditsettings, $canmanagestaff, $canmanagerides, $canmanagejobs, $prefix);
    header("Location: parksettings.php?id=$parkid&editstaff");
    exit;
} else if(isset($_POST['submit'])) {
    if(!park::CanEditSettings($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: parksettings.php?id=$parkid&warning=U heeft geen toegang tot dit!");
        exit;
    }
    $imgh = $_FILES['header'];
    $headerimg = '';
    if(!$_FILES['header']['size'] == 0) {
        $headerimg = common::uploadimagenotmp($imgh);
    }
    $imgl = $_FILES['logo'];
    $logoimg = '';
    if(!$_FILES['logo']['size'] == 0) {
        $logoimg = common::uploadimagenotmp($imgl);
    }
    $imgb = $_FILES['background'];
    $backgroundimg = '';
    if(!$_FILES['background']['size'] == 0) {
        $backgroundimg = common::uploadimagenotmp($imgb);
    }

    $naam = $_POST['naam'];
    $description = $_POST['description'];
    $ip = $_POST['ip'];
    $email = $_POST['email'];
    $park = $_POST['id'];
    park::updatesettings($mysqli, $park, $logoimg, $headerimg, $naam, $description, $ip, $email, $backgroundimg);
    $parkid = $park;
    header("Location: parksettings.php?id=$parkid&settings");
    exit;
} else {
    $parkid = $_GET['id'];
}
$active = '';
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
                    <ol class="breadcrumb">
                        <?php
                        if(isset($_GET['postedit'])) {
                            ?>
                            <li><a href="?id=<?php echo $parkid;?>"><?php echo park::getName($mysqli, $parkid);?></a></li>
                            <li class="active">Artikelen beheren</li>
                            <?php
                        } else if(isset($_GET['editstaff'])) {
                            ?>
                            <li><a href="?id=<?php echo $parkid;?>"><?php echo park::getName($mysqli, $parkid);?></a></li>
                            <li class="active">Staf beheren</li>
                            <?php
                        } else if(isset($_GET['rides'])) {
                            ?>
                            <li><a href="?id=<?php echo $parkid;?>"><?php echo park::getName($mysqli, $parkid);?></a></li>
                            <li class="active">Statussen beheren</li>
                            <?php
                        } else if(isset($_GET['settings'])) {
                            ?>
                            <li><a href="?id=<?php echo $parkid;?>"><?php echo park::getName($mysqli, $parkid);?></a></li>
                            <li class="active">Instellingen</li>
                            <?php
                        } else if(isset($_GET['jobs'])) {
                            ?>
                            <li><a href="?id=<?php echo $parkid;?>"><?php echo park::getName($mysqli, $parkid);?></a></li>
                            <li class="active">Vacatures beheren</li>
                            <?php
                        } else {
                            ?>
                            <li class="active"><?php echo park::getName($mysqli, $parkid);?></li>
                            <?php
                        }?>
                    </ol>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                                <?php
                                if(isset($_GET['postedit'])) {
                                    ?><h3 class="panel-title">Artikelen beheren</h3><?php
                                } else if(isset($_GET['editstaff'])) {
                                    ?><h3 class="panel-title">Staff beheren</h3><?php
                                } else if(isset($_GET['rides'])) {
                                    ?><h3 class="panel-title">Statussen beheren</h3><?php
                                } else if(isset($_GET['settings'])) {
                                    ?><h3 class="panel-title">Instellingen</h3><?php
                                } else if(isset($_GET['jobs'])) {
                                    ?><h3 class="panel-title">Vacatures beheren</h3><?php
                                } else {
                                    ?><h3 class="panel-title">Home</h3><?php
                                }?>
                            </ol>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    if(isset($_GET['postedit'])) {
                                        if(!park::CanWriteArticle($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
                                            header("Location: parksettings.php?id=$parkid");
                                            exit;
                                        }
                                        park::loadarticles($mysqli, $parkid);
                                    }
                                    else if(isset($_GET['editstaff'])) {
                                        if(!park::IsUserOwner($mysqli, $parkid, $_SESSION['UUID']) && !park::CanManageStaff($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
                                            header("Location: parksettings.php?id=$parkid");
                                            exit;
                                        }
                                        ?>
                                        <?php park::loadstaff($mysqli, $parkid);
                                    }
                                    else if(isset($_GET['rides'])) {
                                        if(!park::IsUserOwner($mysqli, $parkid, $_SESSION['UUID']) && !park::CanManageRides($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
                                            header("Location: parksettings.php?id=$parkid");
                                            exit;
                                        }
                                        ?>
                                        <code><span>Park APIKey: <?php echo API::getKeyOfPark($mysqli, $parkid);?></span></a></code><br />
                                        <a href="help.php?plugin">Hoe beheer ik de statussen via mijn server?</a>
                                        <?php rides::loadrides($mysqli, $parkid);
                                    }
                                    else if(isset($_GET['settings'])) {
                                        if(!park::IsUserOwner($mysqli, $parkid, $_SESSION['UUID']) && !park::CanEditSettings($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
                                            header("Location: parksettings.php?id=$parkid");
                                            exit;
                                        }
                                        ?>
                                        <form name="settings" id="settings"
                                              action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                                              enctype="multipart/form-data" method="post" autocomplete="off"
                                              class="form-horizontal">
                                            <div class="form-group">
                                                <label for="header" class="col-md-2 control-label"><span class="text-info">Logo</span></label>

                                                <div class="col-md-10" id="logodiv">
                                                    <img src="<?php echo park::getLogo($mysqli, $parkid); ?>" alt=""
                                                         class="logo" id="logoprv" style="width: 20%;"/>
                                                    <input type="file" id="logo" multiple="" name="logo" accept="image/*"
                                                           onchange="LoadLogoPreview(this)">
                                                    <input type="text" readonly="" class="form-control"
                                                           placeholder="Kies een afbeelding" id="logotext">
                                                    <script>
                                                        $("#logo").change(function () {
                                                            if ($("#logo").val() == '') {
                                                                document.getElementById("logotext").placeholder = 'Kies een afbeelding';
                                                            } else {
                                                                document.getElementById("logotext").placeholder = $("#logo").val().replace(/C:\\fakepath\\/i, '');
                                                            }
                                                        });
                                                        function LoadLogoPreview(fileInput) {
                                                            var files = fileInput.files;
                                                            for (var i = 0; i < files.length; i++) {
                                                                var file = files[i];
                                                                var imageType = /image.*/;
                                                                if (!file.type.match(imageType)) {
                                                                    continue;
                                                                }
                                                                var img = document.getElementById("logoprv");
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
                                                <label for="header" class="col-md-2 control-label"><span class="text-info">Header</span></label>

                                                <div class="col-md-10" id="headerdiv">
                                                    <img src="<?php echo park::getHeader($mysqli, $parkid); ?>" alt=""
                                                         class="logo" id="headerprv" style="width: 100%;"/>
                                                    <input type="file" id="header" multiple="" name="header"
                                                           accept="image/*" onchange="LoadHeaderPreview(this)">
                                                    <input type="text" readonly="" class="form-control"
                                                           placeholder="Kies een afbeelding" id="headertext">
                                                    <script>
                                                        $("#header").change(function () {
                                                            if ($("#header").val() == '') {
                                                                document.getElementById("headertext").placeholder = 'Kies een afbeelding';
                                                            } else {
                                                                document.getElementById("headertext").placeholder = $("#header").val().replace(/C:\\fakepath\\/i, '');
                                                            }
                                                        });
                                                        function LoadHeaderPreview(fileInput) {
                                                            var files = fileInput.files;
                                                            for (var i = 0; i < files.length; i++) {
                                                                var file = files[i];
                                                                var imageType = /image.*/;
                                                                if (!file.type.match(imageType)) {
                                                                    continue;
                                                                }
                                                                var img = document.getElementById("headerprv");
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
                                                <label for="background" class="col-md-2 control-label"><span class="text-info">Achtergrond</span></label>

                                                <div class="col-md-10" id="backgrounddiv">
                                                    <img src="<?php echo park::getBackrgound($mysqli, $parkid); ?>" alt=""
                                                         class="logo" id="backgroundprv" style="width: 100%;"/>
                                                    <input type="file" id="background" multiple="" name="background"
                                                           accept="image/*" onchange="LoadBackgroundPreview(this)">
                                                    <input type="text" readonly="" class="form-control"
                                                           placeholder="Kies een afbeelding" id="backgroundtext">
                                                    <script>
                                                        $("#background").change(function () {
                                                            if ($("#background").val() == '') {
                                                                document.getElementById("backgroundtext").placeholder = 'Kies een afbeelding';
                                                            } else {
                                                                document.getElementById("backgroundtext").placeholder = $("#background").val().replace(/C:\\fakepath\\/i, '');
                                                            }
                                                        });
                                                        function LoadBackgroundPreview(fileInput) {
                                                            var files = fileInput.files;
                                                            for (var i = 0; i < files.length; i++) {
                                                                var file = files[i];
                                                                var imageType = /image.*/;
                                                                if (!file.type.match(imageType)) {
                                                                    continue;
                                                                }
                                                                var img = document.getElementById("backgroundprv");
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
                                                <label for="naam" class="col-md-2 control-label"><span class="text-info">Naam</span></label>
                                                <div class="col-md-10" id="naamdiv">
                                                    <input type="hidden" name="id" value="<?php echo $parkid; ?>">
                                                    <input type="text" class="form-control" name="naam" id="naam"
                                                           placeholder="Typ hier de naam van het park"
                                                           value="<?php echo park::getName($mysqli, $parkid); ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="description" class="col-md-2 control-label"><span
                                                            class="text-info">Description</span></label>
                                                <div class="col-md-10" id="descdiv">
                                                    <textarea type="text" class="form-control" name="description"
                                                              id="description"
                                                              placeholder="Typ hier de beschrijving van het park" rows="10"
                                                              required><?php echo park::getDescription($mysqli, $parkid); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="ip" class="col-md-2 control-label"><span
                                                            class="text-info">IP</span></label>
                                                <div class="col-md-10" id="ipdiv">
                                                    <input type="text" class="form-control" name="ip" id="ip"
                                                           placeholder="Typ hier het IP van het park"
                                                           value="<?php echo park::getIP($mysqli, $parkid); ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="email" class="col-md-2 control-label"><span class="text-info">Email</span></label>
                                                <div class="col-md-10" id="emaildiv">
                                                    <input type="email" class="form-control" name="email" id="email"
                                                           placeholder="Typ hier de email van het park"
                                                           value="<?php echo park::getEmail($mysqli, $parkid); ?>" required>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <button type="submit" class="btn btn-raised btn-success" name="submit"
                                                                                           id="postbutton">Aanpassen
                                                </button>
                                            </div>
                                        </form>
                                        <?php
                                    }
                                    else if(isset($_GET['jobs'])) {
                                        if(!park::IsUserOwner($mysqli, $parkid, $_SESSION['UUID']) && !park::CanManageJobs($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
                                            header("Location: parksettings.php?id=$parkid");
                                            exit;
                                        }
                                        if(isset($_GET['application'])) {
                                            vacature::loadApplications($mysqli, $_GET['application'], $parkid);
                                        } else {
                                            vacature::loadVacaturesSettings($mysqli, $parkid);
                                        }
                                    }
                                    else {
                                        $tijd = date("G");
                                        if ($tijd < 6) {
                                            echo "Goedenacht ".user::getName($mysqli);
                                        } elseif ($tijd < 12) {
                                            echo "Goedemorgen ".user::getName($mysqli);
                                        } elseif ($tijd < 18) {
                                            echo "Goedemiddag ".user::getName($mysqli);
                                        } else {
                                            echo "Goedenavond ".user::getName($mysqli);
                                        }?>
                                    <hr />
                                        <h3>Volgers</h3>
                                    <?php
                                    $followers = park::getFollowers($mysqli, $parkid);
                                    ?>
                                        <span class="countfol"><?php echo $followers;?></span>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-primary countfoll" style="width: 0%"></div>
                                        </div>
                                        <script type="text/javascript">
                                            $('.countfol').each(function () {
                                                $(this).prop('Counter',0).animate({
                                                    Counter: $(this).text()
                                                }, {
                                                    duration: 4000,
                                                    easing: 'swing',
                                                    step: function (now) {
                                                        $(this).text(Math.ceil(now));
                                                        $('.countfoll').css('width', ((100 / <?php echo $followers;?>) * Math.ceil(now)) + '%');
                                                    }
                                                });
                                            });
                                        </script>
                                        <?php
                                    }?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- col-9 -->
                </div>

                <div class="col-md-3 right-container well">
                    <h4 class="text-danger">Snelkoppelingen</h4>
                    <p><a href="?id=<?php echo $parkid;?>" class="shortcut"><i class="material-icons">home</i><span>Home</span></a></p>
                    <?php if(park::IsUserOwner($mysqli, $parkid, $_SESSION['UUID']) || park::CanEditSettings($mysqli, $parkid, $_SESSION['UUID']) || staff::canManageParks($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?id=<?php echo $parkid;?>&settings" class="shortcut"><i class="material-icons">settings</i><span>Park instellingen</span></a></p>
                    <?php }?>
                    <?php if(park::IsUserOwner($mysqli, $parkid, $_SESSION['UUID']) || park::CanManageStaff($mysqli, $parkid, $_SESSION['UUID']) || staff::canManageParks($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?id=<?php echo $parkid;?>&editstaff" class="shortcut"><i class="material-icons">group_add</i><span>Staff beheren</span></a></p>
                    <?php }?>
                    <?php if(park::IsUserOwner($mysqli, $parkid, $_SESSION['UUID']) || park::CanManageRides($mysqli, $parkid, $_SESSION['UUID']) || staff::canManageParks($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?id=<?php echo $parkid;?>&rides" class="shortcut"><i class="material-icons">airline_seat_recline_normal</i><span>Statussen beheren</span></a></p>
                    <?php }?>
                    <?php if(park::IsUserOwner($mysqli, $parkid, $_SESSION['UUID']) || park::CanWriteArticle($mysqli, $parkid, $_SESSION['UUID']) || staff::canManageParks($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?id=<?php echo $parkid;?>&postedit" class="shortcut"><i class="material-icons">forum</i><span>Artikelen beheren</span></a></p>
                    <?php }?>
                    <?php if(park::IsUserOwner($mysqli, $parkid, $_SESSION['UUID']) || park::CanManageJobs($mysqli, $parkid, $_SESSION['UUID']) || staff::canManageParks($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?id=<?php echo $parkid;?>&jobs" class="shortcut"><i class="material-icons">local_offer</i><span>Vacatures beheren</span></a></p>
                    <?php }?>
                    <?php if(park::IsUserOwner($mysqli, $parkid, $_SESSION['UUID']) || staff::canManageParks($mysqli, $_SESSION['UUID'])) {?>
                        <p><a data-toggle="modal" data-target="#changeOwner" class="shortcut"><i class="material-icons">perm_identity</i><span>Beheerder instellingen</span></a></p>
                        <div class="modal fade" tabindex="-1" role="dialog" id="changeOwner">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Beheerder instellingen</h4>
                                    </div>
                                    <form name="setowner" id="setowner" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post" autocomplete="off" class="form-horizontal">
                                        <div class="modal-body">
                                            <span>Wie is de beheerder van dit park?<br /> <span class="text-danger">(Let op: deze persoon moet geregistreerd zijn op ParkCraft!)</span></span><input type="email" name="email" value="<?php echo park::getOwnerEmail($mysqli, $parkid);?>"  class="form-control"/><br />
                                            <input type="hidden" value="<?php echo $parkid;?>" name="id"/>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-raised btn-success" name="changeowner" id="changeowner">Aanpassen
                                            </button>
                                        </div>
                                    </form>
                                    <form name="deletepark" id="deletepark" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post" autocomplete="off" class="form-horizontal">
                                        <div class="modal-body">
                                            <input type="hidden" value="<?php echo $parkid;?>" name="id"/>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-raised btn-danger" name="deletepark" id="deletepark">Park verwijderen
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php }?>
                    <?php ads::skycraper();?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
	    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.6/summernote.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.6/summernote.js"></script>
    <script src="assets/js/summernote-nl-NL.js"></script>
    <script>
        $(document).ready(function () {
            $('[id=article]').summernote({
                lang: 'nl-NL' // default: 'en-US'
            });
        });
    </script>

    </body>
    </html>
<?php
mysqli_close($mysqli);
?>