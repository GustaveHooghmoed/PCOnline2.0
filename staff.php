<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
$cvwl = false;
include 'includes/phpimports.php';
if(!staff::canUseStaffPanel($mysqli, $_SESSION['UUID'])) {
    header("Location: home.php");
    exit;
}if(isset($_GET['remote'])) {
    if(!staff::canManageUsers($mysqli, $_SESSION['UUID'])) {
        header("Location: staff.php?home=&warning=Geen toegang tot dit gedeelte.");
        exit;
    }
    $uuid = $_GET['remote'];
    staff::remoteAccount($mysqli, $uuid);
    exit;
}
if(isset($_GET['removeapplication'])) {
    if(!staff::canManageApplications($mysqli, $_SESSION['UUID'])) {
        header("Location: staff.php?home=&warning=Geen toegang tot dit gedeelte.");
        exit;
    }
    $id = $_GET['removeapplication'];
    $page = $_GET['pi'];
    vacature::removeApplication($mysqli, $id);
    header("Location: staff.php?applications=&id=$page&info=Sollicitatie verwijderd!");
    exit;
}
if(isset($_GET['removevacature'])) {
    if(!staff::canManageApplications($mysqli, $_SESSION['UUID'])) {
        header("Location: staff.php?home=&warning=Geen toegang tot dit gedeelte.");
        exit;
    }
    $jobid = $_GET['removevacature'];
    $parkid = $_GET['parkid'];
    $page = $_GET['pi'];
    vacature::removeVacature($mysqli, $parkid, $jobid);
    header("Location: staff.php?applications=&page=$page&info=Vacature verwijderd!");
    exit;
}
if(isset($_GET['undoremovevacature'])) {
    if(!staff::canManageApplications($mysqli, $_SESSION['UUID'])) {
        header("Location: staff.php?home=&warning=Geen toegang tot dit gedeelte.");
        exit;
    }
    $jobid = $_GET['undoremovevacature'];
    $parkid = $_GET['parkid'];
    $page = $_GET['pi'];
    vacature::unremoveVacature($mysqli, $parkid, $jobid);
    header("Location: staff.php?applications=&page=$page&info=Verwijderen ongedaan gemaakt!");
    exit;
}
if(isset($_POST['mailbutton'])) {
    if(!staff::canSendMail($mysqli, $_SESSION['UUID'])) {
        header("Location: staff.php?home=&warning=Geen toegang tot dit gedeelte.");
        exit;
    }
    $subject = $_POST['subject'];
    $body = preg_replace("/\r\n|\r/", "<br />", $_POST["mail"]);
    user::sendEmailToEveryone($mysqli, $subject, $body);
    header("Location: staff.php?mail&info=Email verstuurd");
    exit;
}
if(isset($_GET['parkrequest']) && isset($_GET['refuse']) && !empty($_GET['refuse'])) {
    if(!staff::canManageParkRequests($mysqli, $_SESSION['UUID'])) {
        header("Location: staff.php?home=&warning=Geen toegang tot dit gedeelte.");
        exit;
    }
    park::refuserequest($mysqli, $_GET['refuse']);
    header("Location: staff.php?parkrequest=&info=Park met succes geweigerd!");
    exit;
}
if(isset($_GET['parkrequest']) && isset($_GET['accept']) && !empty($_GET['accept'])) {
    if(!staff::canManageParkRequests($mysqli, $_SESSION['UUID'])) {
        header("Location: staff.php?home=&warning=Geen toegang tot dit gedeelte.");
        exit;
    }
    park::acceptrequest($mysqli, $_GET['accept']);
    header("Location: staff.php?parkrequest=&info=Park met succes geaccepteerd!");
    exit;
}
if(isset($_POST['edituserbutton'])) {
    if(!staff::canManageUsers($mysqli, $_SESSION['UUID'])) {
        header("Location: staff.php?home=&warning=Geen toegang tot dit gedeelte.");
        exit;
    }
    $uuid = $_POST['uuid'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $rank = $_POST['rank'];
    $access = $_POST['ttpo'];
    $activated = $_POST['ag'];
    $pageid = $_POST['pi'];
    user::update($mysqli, $name, $email, $rank, $access, $activated, $uuid);
    header("Location: staff.php?users=&page=$pageid&info=Gebruiker aangepast");
    exit;
}
if(isset($_GET['undoremove']) || isset($_GET['remove'])) {
    if(!staff::canManageParks($mysqli, $_SESSION['UUID'])) {
        header("Location: staff.php?home=&warning=Geen toegang tot dit gedeelte.");
        exit;
    }
    $uuid = $_GET['parks'];
    if(isset($_GET['undoremove'])) {
        park::delete($mysqli, $uuid, 0);
    }
    if(isset($_GET['remove'])) {
        park::delete($mysqli, $uuid, 1);
    }
    $pageid = $_GET['pi'];
    header("Location: staff.php?parks=&page=$pageid&info=Park aangepast");
    exit;
}
if(isset($_GET['removepost']) || isset($_GET['undoremovepost'])) {
    if(!staff::canManagePosts($mysqli, $_SESSION['UUID'])) {
        header("Location: staff.php?home=&warning=Geen toegang tot dit gedeelte.");
        exit;
    }
    $post = $_GET['posts'];
    if(isset($_GET['undoremovepost'])) {
        article::undeletepoststaff($mysqli, $post, $_SESSION['UUID']);
    }
    if(isset($_GET['removepost'])) {
        article::deletepoststaff($mysqli, $post, $_SESSION['UUID']);
    }
    $pageid = $_GET['pi'];
    header("Location: staff.php?posts=&page=$pageid&info=Artikel aangepast");
    exit;
}
if(isset($_GET['removereaction'])) {
    $id = $_GET['id'];
    article::RemoveReaction($mysqli, $id);

    $pageid = $_GET['pi'];
    header("Location: staff.php?reactions=&page=$pageid&info=Reactie verwijderd");
    exit;
}
if(isset($_GET['removead'])) {
    $id = $_GET['removead'];
    ads::RemoveAd($mysqli, $id);

    header("Location: staff.php?ads&info=Advertentie verwijderd");
    exit;
}
if(isset($_POST['addad'])) {
    if(!staff::canManageadvertisements($mysqli, $_SESSION['UUID'])) {
        header("Location: staff.php?home=&warning=Geen toegang tot dit gedeelte.");
        exit;
    }
    $name = $_POST['name'];
    $code = $_POST['code'];
    $pages = $_POST['pages'];
    $sort = $_POST['sort'];
    ads::addAd($mysqli, $name, $code, $sort, $pages);
    header("Location: staff.php?ads=&info=Advertentie toegevoegd");
    exit;
}
$active = 'staff';
$keywords;
if(isset($_GET['keywords'])) {
    $keywords = $_GET['keywords'];
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
                            <?php if(isset($_GET['parkrequest'])) {?>
                                <h3 class="panel-title">Park Aanvragen</h3>
                            <?php } else if(isset($_GET['parks'])) {?>
                                <h3 class="panel-title">Parken</h3>
                            <?php } else if(isset($_GET['reactions'])) {?>
                                <h3 class="panel-title">Reacties</h3>
                            <?php } else if(isset($_GET['mail'])) {?>
                                <h3 class="panel-title">Mail naar alle gebruikers</h3>
                            <?php } else if(isset($_GET['users'])) {?>
                                <h3 class="panel-title">Gebruikers</h3>
                            <?php } else if(isset($_GET['statistics'])) {?>
                                <h3 class="panel-title">Statistieken</h3>
                            <?php } else if(isset($_GET['applications'])) {?>
                                <h3 class="panel-title">Vacatures</h3>
                            <?php } else if(isset($_GET['ads'])) {?>
                                <h3 class="panel-title">Advertenties</h3>
                            <?php } else if(isset($_GET['addad'])) {?>
                                <h3 class="panel-title">Advertentie toevoegen</h3>
                            <?php } else{?>
                                <h3 class="panel-title">Home</h3>
                            <?php }?>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php if(isset($_GET['parkrequest']) && staff::canManageParkRequests($mysqli, $_SESSION['UUID'])) {
                                        park::loadrequests($mysqli);
                                    } else if(isset($_GET['users']) && staff::canManageUsers($mysqli, $_SESSION['UUID'])) {
                                        if(!isset($_GET['id'])) {
                                            ?>
                                            <form action="staff.php" method="get">
                                                <div class="form-group">
                                                    <input type="hidden" class="form-control col-md-8" name="users">
                                                    <div style="display:inline-block;">
                                                        <input type="text" class="form-control" name="keywords"
                                                               placeholder="Zoeken" value="<?php echo $keywords;?>">
                                                    </div>
                                                    <div style="display:inline-block;">
                                                        <a href="staff.php?users=&page=1" class="btn-sm btn-danger">Reset</a>
                                                    </div>
                                                </div>
                                            </form>
                                            <?php
                                        }
                                         if(isset($_GET['id'])) {
                                            user::loadUserIn($mysqli, $_GET['id']);
                                        } else if($_GET['keywords']) {
                                            user::loadAllUsersSearch($mysqli, $_GET['keywords']);
                                        } else if(isset($_GET['page'])) {
                                            if($_GET['page'] != 0) {
                                                $pageid = $_GET['page'] - 1;
                                                user::loadAllUsers($mysqli, $pageid);
                                            }
                                        }
                                    } else if(isset($_GET['parks']) && staff::canManageParks($mysqli, $_SESSION['UUID'])) {
                                        ?>
                                        <form action="staff.php" method="get">
                                            <div class="form-group">
                                                <input type="hidden" class="form-control col-md-8" name="parks">
                                                <div style="display:inline-block;">
                                                    <input type="text" class="form-control" name="keywords"
                                                           placeholder="Zoeken" value="<?php echo $keywords;?>">
                                                </div>
                                                <div style="display:inline-block;">
                                                    <a href="staff.php?parks=&page=1" class="btn-sm btn-danger">Reset</a>
                                                </div>
                                            </div>
                                        </form>
                                        <?php
                                        if(isset($_GET['keywords'])) {
                                            park::LoadParksSearch($mysqli, $pageid, $_GET['keywords']);
                                        } else if(isset($_GET['page'])) {
                                            if($_GET['page'] != 0) {
                                                $pageid = $_GET['page'] - 1;
                                                park::LoadParks($mysqli, $pageid);
                                            }
                                        }
                                    } else if(isset($_GET['applications']) && staff::canManageApplications($mysqli, $_SESSION['UUID'])) {
                                        if(isset($_GET['page'])) {
                                            if($_GET['page'] != 0) {
                                                $pageid = $_GET['page'] - 1;
                                                vacature::loadVacaturesStaff($mysqli, $pageid);
                                            }
                                        } else if(isset($_GET['id'])) {
                                            vacature::loadApplicationsStaff($mysqli, $_GET['id']);
                                        }
                                    } else if(isset($_GET['reactions']) && staff::canManageComments($mysqli, $_SESSION['UUID'])) {
                                        if(isset($_GET['page'])) {
                                            if($_GET['page'] != 0) {
                                                $pageid = $_GET['page'] - 1;
                                                article::loadAllReactions($mysqli, $pageid);
                                            }
                                        }
                                    } else if(isset($_GET['posts']) && staff::canManagePosts($mysqli, $_SESSION['UUID'])) {
                                        if(isset($_GET['page'])) {
                                            if($_GET['page'] != 0) {
                                                $pageid = $_GET['page'] - 1;
                                                article::loadAllPosts($mysqli, $pageid);
                                            }
                                        }
                                    } else if(isset($_GET['ads']) && staff::canManageadvertisements($mysqli, $_SESSION['UUID'])) {
                                        ?>
                                        <a type="button" class="btn btn-info" data-toggle="modal" href="staff.php?addad">Advertentie toevoegen</a>
                                        <?php
                                        ads::getAllAds($mysqli);
                                    } else if(isset($_GET['addad']) && staff::canManageadvertisements($mysqli, $_SESSION['UUID'])) {
                                        ?>
                                        <form name="addad" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off" class="form-horizontal">
                                            <div class="form-group">
                                                <label for="name"><span class="text-info">Naam</span></label>
                                                <input type="text" name="name" id="name" placeholder="Vul hier de naam in." class="form-control" autofocus="autofocus" required="required">
                                            </div>
                                            <div class="form-group">
                                                <label for="code"><span class="text-info">Code</span></label>
                                                <textarea class="form-control" name="code" id="code"
                                                          placeholder="Typ hier de code" value="" rows="10"
                                                          required></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="sort"><span class="text-info">Soort</span></label>
                                                <div class="">
                                                    <label><input type="radio" name="sort" value="0">Skycraper</label>
                                                </div>
                                                <div class="">
                                                    <label><input type="radio" name="sort" value="1">Vierkant</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="page"><span class="text-info">Pagina's waarom de advertentie gezien moet worden</span></label>
                                                <input type="text" name="page" id="page" placeholder="Vul hier de paginas in." class="form-control" autofocus="autofocus" required="required">
                                            </div>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-raised btn-success" name="addad" >Toevoegen</button>
                                            </div>
                                        </form>
                                        <?php
                                    } else if(isset($_GET['reviewposts']) && staff::canManagePosts($mysqli, $_SESSION['UUID'])) {
                                        if(isset($_GET['page'])) {
                                            if($_GET['page'] != 0) {
                                                $pageid = $_GET['page'] - 1;
                                                article::loadReviewPosts($mysqli, $pageid);
                                            }
                                        }
                                    
                                    } else if(isset($_GET['deletedposts']) && staff::canManagePosts($mysqli, $_SESSION['UUID'])) {
                                        if(isset($_GET['page'])) {
                                            if($_GET['page'] != 0) {
                                                $pageid = $_GET['page'] - 1;
                                                article::loadDeletedPosts($mysqli, $pageid);
                                            }
                                        }
                                    
                                    } else if(isset($_GET['rejectedposts']) && staff::canManagePosts($mysqli, $_SESSION['UUID'])) {
                                        if(isset($_GET['page'])) {
                                            if($_GET['page'] != 0) {
                                                $pageid = $_GET['page'] - 1;
                                                article::loadRejectedPosts($mysqli, $pageid);
                                            }
                                        }
                                    
                                    } else if(isset($_GET['mail'])) {
                                        if(!staff::canSendMail($mysqli, $_SESSION['UUID'])) {
                                            header("Location: staff.php?warning=Geen toegang tot dit gedeelte.");
                                            exit;
                                        }
                                        ?>
                                        <form name="mail" id="mail" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data" method="post" autocomplete="off" class="form-horizontal">
                                            <div class="form-group">
                                                <label for="title" class="col-md-2 control-label"><span class="text-info">Titel</span></label>
                                                <div class="col-md-10" id="titlediv">
                                                    <input type="text" class="form-control" name="subject" id="subject" placeholder="Typ hier het onderwerp..." value="" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="article" class="col-md-2 control-label"><span class="text-info">De Mail</span></label>
                                                <div class="col-md-10" id="titlediv">
                                                    <textarea type="text" class="form-control" name="mail" id="mail" placeholder="Typ hier de mail" value="" rows="10" required></textarea>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <button type="submit" class="btn btn-raised btn-success" name="mailbutton" id="mailbutton" form="mail">Post</button>
                                            </div>
                                        </form>
                                        <?php
                                    } else if(isset($_GET['home'])){?>
                                        <p>Home</p>
                                    <br>
                                    <h3>More Coming Soon</h3>
                                    <?php } else if(isset($_GET['statistics'])){?>
                                        <div class="jumbotron">
                                            <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
                                            <h3>Pagina weergaven</h3>
                                            <script type="text/javascript" src="assets/js/jquery.canvasjs.min.js"></script>
                                            <div id="chartContainer" style="height: 300px; width: 100%;"></div>
                                            <script type="text/javascript">
                                                window.onload = function () {
                                                    var chart = new CanvasJS.Chart("chartContainer",
                                                        {

                                                            title:{
                                                                text: "Weergaven",
                                                                fontSize: 15
                                                            },
                                                            animationEnabled: true,
                                                            axisX:{

                                                                gridColor: "Silver",
                                                                tickColor: "silver",
                                                                valueFormatString: "DD/MMM"

                                                            },
                                                            toolTip:{
                                                                shared:true
                                                            },
                                                            theme: "theme2",
                                                            axisY: {
                                                                gridColor: "Silver",
                                                                tickColor: "silver"
                                                            },
                                                            legend:{
                                                                verticalAlign: "center",
                                                                horizontalAlign: "right"
                                                            },
                                                            data: [
                                                                {
                                                                    type: "line",
                                                                    showInLegend: true,
                                                                    lineThickness: 2,
                                                                    name: "Weergaven",
                                                                    markerType: "square",
                                                                    color: "#F08080",
                                                                    dataPoints: [
                                                                        <?php
                                                                        statistics::totalPageVisits($mysqli);
                                                                        ?>
                                                                    ]
                                                                },
                                                            ],
                                                            legend:{
                                                                cursor:"pointer",
                                                                itemclick:function(e){
                                                                    if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                                                                        e.dataSeries.visible = false;
                                                                    }
                                                                    else{
                                                                        e.dataSeries.visible = true;
                                                                    }
                                                                    chart.render();
                                                                }
                                                            }
                                                        });

                                                    chart.render();
                                                }
                                            </script>
                                            <hr />
                                            <h3>Vandaag aantal unieke gebruikers</h3>
                                            <?php
                                            $countpostu = statistics::getTodayUniqueVisitors($mysqli);
                                            ?>
                                            <span class="countpostu"><?php echo $countpostu;?></span>
                                            <div class="progress progress-striped">
                                                <div class="progress-bar progress-bar-info countpostlu" style="width: 0%"></div>
                                            </div>
                                            <script type="text/javascript">
                                                $('.countpostu').each(function () {
                                                    $(this).prop('Counter',0).animate({
                                                        Counter: $(this).text()
                                                    }, {
                                                        duration: 4000,
                                                        easing: 'swing',
                                                        step: function (now) {
                                                            $(this).text(Math.ceil(now));
                                                            $('.countpostlu').css('width', ((100 / <?php echo $countpostu;?>) * Math.ceil(now)) + '%');
                                                        }
                                                    });
                                                });
                                            </script>
                                            <hr />
                                            <h3>Geweigerde parkaanvragen</h3>
                                            <?php
                                                $countpr = statistics::parkRequestsRejected($mysqli);
                                            ?>
                                            <span class="countpr"><?php echo $countpr;?></span>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-danger countprl" style="width: 0%"></div>
                                            </div>
                                            <script type="text/javascript">
                                                $('.countpr').each(function () {
                                                    $(this).prop('Counter',0).animate({
                                                        Counter: $(this).text()
                                                    }, {
                                                        duration: 4000,
                                                        easing: 'swing',
                                                        step: function (now) {
                                                            $(this).text(Math.ceil(now));
                                                            $('.countprl').css('width', ((100 / <?php echo $countpr;?>) * Math.ceil(now)) + '%');
                                                        }
                                                    });
                                                });
                                            </script>
                                            <hr />
                                            <h3>Aantal parken</h3>
                                            <?php
                                            $countp = statistics::parks($mysqli);
                                            ?>
                                            <span class="countp"><?php echo $countp;?></span>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-info countpl" style="width: 0%"></div>
                                            </div>
                                            <script type="text/javascript">
                                                $('.countp').each(function () {
                                                    $(this).prop('Counter',0).animate({
                                                        Counter: $(this).text()
                                                    }, {
                                                        duration: 4000,
                                                        easing: 'swing',
                                                        step: function (now) {
                                                            $(this).text(Math.ceil(now));
                                                            $('.countpl').css('width', ((100 / <?php echo $countp;?>) * Math.ceil(now)) + '%');
                                                        }
                                                    });
                                                });
                                            </script>
                                            <hr />
                                            <h3>Aantal park evenementen</h3>
                                            <?php
                                            $countpe = statistics::parkEvents($mysqli);
                                            ?>
                                            <span class="countpe"><?php echo $countpe;?></span>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-warning countpel" style="width: 0%"></div>
                                            </div>
                                            <script type="text/javascript">
                                                $('.countpe').each(function () {
                                                    $(this).prop('Counter',0).animate({
                                                        Counter: $(this).text()
                                                    }, {
                                                        duration: 4000,
                                                        easing: 'swing',
                                                        step: function (now) {
                                                            $(this).text(Math.ceil(now));
                                                            $('.countpel').css('width', ((100 / <?php echo $countpe;?>) * Math.ceil(now)) + '%');
                                                        }
                                                    });
                                                });
                                            </script>
                                            <hr />
                                            <h3>Aantal park attracties</h3>
                                            <?php
                                            $countpr = statistics::parkRides($mysqli);
                                            ?>
                                            <span class="countpa"><?php echo $countpr;?></span>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-primary countpal" style="width: 0%"></div>
                                            </div>
                                            <script type="text/javascript">
                                                $('.countpa').each(function () {
                                                    $(this).prop('Counter',0).animate({
                                                        Counter: $(this).text()
                                                    }, {
                                                        duration: 4000,
                                                        easing: 'swing',
                                                        step: function (now) {
                                                            $(this).text(Math.ceil(now));
                                                            $('.countpal').css('width', ((100 / <?php echo $countpr;?>) * Math.ceil(now)) + '%');
                                                        }
                                                    });
                                                });
                                            </script>
                                            <hr />
                                            <h3>Aantal park stafleden</h3>
                                            <?php
                                            $countps = statistics::parkStaff($mysqli);
                                            ?>
                                            <span class="countps"><?php echo $countps;?></span>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-warning countpsl" style="width: 0%"></div>
                                            </div>
                                            <script type="text/javascript">
                                                $('.countps').each(function () {
                                                    $(this).prop('Counter',0).animate({
                                                        Counter: $(this).text()
                                                    }, {
                                                        duration: 4000,
                                                        easing: 'swing',
                                                        step: function (now) {
                                                            $(this).text(Math.ceil(now));
                                                            $('.countpsl').css('width', ((100 / <?php echo $countps;?>) * Math.ceil(now)) + '%');
                                                        }
                                                    });
                                                });
                                            </script>
                                            <hr />
                                            <h3>Aantal artikelen</h3>
                                            <?php
                                            $countpost = statistics::posts($mysqli);
                                            ?>
                                            <span class="countpost"><?php echo $countpost;?></span>
                                            <div class="progress progress-striped">
                                                <div class="progress-bar progress-bar-info countpostl" style="width: 0%"></div>
                                            </div>
                                            <script type="text/javascript">
                                                $('.countpost').each(function () {
                                                    $(this).prop('Counter',0).animate({
                                                        Counter: $(this).text()
                                                    }, {
                                                        duration: 4000,
                                                        easing: 'swing',
                                                        step: function (now) {
                                                            $(this).text(Math.ceil(now));
                                                            $('.countpostl').css('width', ((100 / <?php echo $countpost;?>) * Math.ceil(now)) + '%');
                                                        }
                                                    });
                                                });
                                            </script>
                                            <hr />
                                            <h3>Aantal reacties op artikelen</h3>
                                            <?php
                                            $countcomments = statistics::reactions($mysqli);
                                            ?>
                                            <span class="countcomments"><?php echo $countcomments;?></span>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-success countcommentsl" style="width: 0%"></div>
                                            </div>
                                            <script type="text/javascript">
                                                $('.countcomments').each(function () {
                                                    $(this).prop('Counter',0).animate({
                                                        Counter: $(this).text()
                                                    }, {
                                                        duration: 4000,
                                                        easing: 'swing',
                                                        step: function (now) {
                                                            $(this).text(Math.ceil(now));
                                                            $('.countcommentsl').css('width', ((100 / <?php echo $countcomments;?>) * Math.ceil(now)) + '%');
                                                        }
                                                    });
                                                });
                                            </script>
                                            <hr />
                                            <h3>Aantal gebruikers</h3>
                                            <?php
                                            $countusers = statistics::users($mysqli);
                                            $countusersactivated = statistics::usersActivated($mysqli);
                                            $countusersnotactivated = $countusers - $countusersactivated;
                                            ?>
                                            <span class="text-success">Aantal gebruikers: </span> <span class="countusers"><?php echo $countusers;?></span><br/>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-success countusersl" style="width: 0%"></div>
                                            </div>
                                            <span class="text-success">Aantal geactiveerde gebruikers: </span><span class="countusersa"><?php echo $countusersactivated;?></span><br/>
                                            <span class="text-danger">Aantal niet geactiveerde gebruikers: </span><span class="countusersna"><?php echo $countusersnotactivated;?></span>
                                            <div class="progress progress-striped active">
                                                <div class="progress-bar progress-bar-success countusersla" style="width: 0%"></div>
                                                <div class="progress-bar progress-bar-danger countuserslna" style="width: 0%"></div>
                                            </div>
                                            <script type="text/javascript">
                                                $('.countusers').each(function () {
                                                    $(this).prop('Counter',0).animate({
                                                        Counter: $(this).text()
                                                    }, {
                                                        duration: 4000,
                                                        easing: 'swing',
                                                        step: function (now) {
                                                            $(this).text(Math.ceil(now));
                                                            $('.countusersl').css('width', (100 / <?php echo $countusers;?> * now) + '%');
                                                        }
                                                    });
                                                });
                                                $('.countusersa').each(function () {
                                                    $(this).prop('Counter',0).animate({
                                                        Counter: $(this).text()
                                                    }, {
                                                        duration: 4000,
                                                        easing: 'swing',
                                                        step: function (now) {
                                                            $(this).text(Math.ceil(now));
                                                            $('.countusersla').css('width', (100 / $('.countusers').text() * <?php echo $countusersactivated;?>) + '%');
                                                        }
                                                    });
                                                });
                                                $('.countusersna').each(function () {
                                                    $(this).prop('Counter',0).animate({
                                                        Counter: $(this).text()
                                                    }, {
                                                        duration: 4000,
                                                        easing: 'swing',
                                                        step: function (now) {
                                                            $(this).text(Math.ceil(now));
                                                            $('.countuserslna').css('width', (100 / $('.countusers').text() * <?php echo $countusersnotactivated;?>) + '%');
                                                        }
                                                    });
                                                });
                                            </script>
                                        </div>
                                    <?php } else {
                                        header("Location: staff.php?home=&warning=Geen toegang tot dit gedeelte.");
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
                    <?php if(staff::canManageParkRequests($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?parkrequest" class="shortcut"><i class="material-icons">group_add</i><span>Park aanvragen beheren</span></a></p>
                    <?php }
                    if(staff::canManageUsers($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?users=&page=1" class="shortcut"><i class="material-icons">accessibility</i><span>Gebruikers beheren</span></a></p>
                    <?php }
                    if(staff::canManagePosts($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?posts=&page=1" class="shortcut"><i class="material-icons">assignment</i><span>Artikelen Beheren</span></a></p>
                    <?php }
                    if(staff::canManageParks($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?parks=&page=1" class="shortcut"><i class="material-icons">account_balance</i><span>Parken beheren</span></a></p>
                    <?php }
                    if(staff::canManageApplications($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?applications=&page=1" class="shortcut"><i class="material-icons">local_offer</i><span>Vacatures beheren</span></a></p>
                    <?php }
                    if(staff::canManageComments($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?reactions=&page=1" class="shortcut"><i class="material-icons">textsms</i><span>Reacties beheren</span></a></p>
                    <?php }
                    if(staff::canManageadvertisements($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?ads" class="shortcut"><i class="material-icons">assessment</i><span>Advertenties beheren</span></a></p>
                    <?php }
                    if(staff::canSendMail($mysqli, $_SESSION['UUID'])) {?>
                        <p><a href="?mail" class="shortcut"><i class="material-icons">mail</i><span>Mail</span></a></p>
                    <?php }?>
                    <p><a href="?statistics" class="shortcut"><i class="material-icons">timeline</i><span>Statistieken</span></a></p>
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
