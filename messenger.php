<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
$cvwl = false;
include 'includes/phpimports.php';
$active = 'messenger';
if(isset($_GET['startchat'])) {
    if(user::existUUID($mysqli, $_GET['startchat'])) {
        if(strcmp($_GET['startchat'], $_SESSION['UUID']) !=0) {
            chats::startChat($mysqli, $_GET['startchat'], $_SESSION['UUID']);
        }
    }
    header("Location: messenger.php?id=".chats::getChatID($mysqli, $_GET['startchat'], $_SESSION['UUID'])."");
    exit;
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
                            <h3 class="panel-title">Parkcraft Messenger</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <a href="messenger.php?newchat" class="btn btn-danger btn-sm">Nieuw gesprek</a>
                                <hr />
                                <div class="col-md-12">
                                    <div>
                                        <?php
                                        if(isset($_GET['id'])) {
                                            if(chats::isChatOfUser($mysqli, $_GET['id'], $_SESSION['UUID'])) {
                                                ?>
                                                <div id="chat" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h4 class="modal-title">Gesprek
                                                                    met <?php echo chats::getNameOfChatter($mysqli, $_GET['id']); ?></h4>
                                                            </div>
                                                            <div class="modal-body" id="chatbox"
                                                                 style="height:30em;width:auto;border:1px solid #ccc;overflow:auto;word-break: break-all;">
                                                                <?php chats::loadChat($mysqli, $_GET['id']); ?>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <div class="form-group">
                                                                    <label for="bericht" class="col-md-2 control-label">Bericht</label>

                                                                    <div class="col-md-10">
                                                                        <input type="text" class="form-control" id="bericht" placeholder="Bericht">
                                                                    </div>
                                                                </div>
                                                                <button id="send" class="btn btn-success">Verzenden</button>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <script type="text/javascript">
                                                    $(window).load(function () {
                                                        $('#chat').modal('show');
                                                        $('#chat').on('hide.bs.modal', function () {
                                                            window.location = "/messenger.php";
                                                        });
                                                        $('#chat').on('hidden', function () {
                                                            window.location = "/messenger.php";
                                                        });
                                                    });
                                                </script>
                                                <?php
                                            }
                                        }else if(isset($_GET['newchat'])) {
                                        ?>
                                        <div id="newchat" class="modal fade" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title">Nieuw gesprek</h4>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <div class="form-group">
                                                            <label for="search" class="col-md-2 control-label">Zoeken</label>

                                                            <div class="col-md-10">
                                                                <input type="text" class="form-control" id="keywords" placeholder="Zoeken">
                                                            </div>
                                                        </div>
                                                        <div class="modal-body" id="results"
                                                             style="height:30em;width:auto;border:1px solid #ccc;overflow:auto;word-break: break-all;">

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <script type="text/javascript">
                                            $(window).load(function () {
                                                $('#newchat').modal('show');
                                                $("#keywords").keyup(function() {
                                                    var keywords = $('#keywords').val().replace(/ /g,'%20');;
                                                    $('#results').load('https://parkcraft.nl/chat-api.php?search='+keywords);
                                                });
                                            });
                                        </script>
                                        <?php
                                        }?>
                                        <div id="chats"><?php
                                            chats::loadChats($mysqli, $_SESSION['UUID']);
                                            ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(isset($_SESSION['UUID'])) {?>
                    <div class="col-md-3 right-container well">
                        <h4 class="text-danger">Wie te volgen</h4>
                        <?php park::loadWhoToFollow($mysqli,$_SESSION['UUID']);?>
                        <?php ads::skycraper($mysqli, basename(__FILE__));?>
                    </div>
                <?php }?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="includes/chat.js"></script>
    <script>
        function openChat(id) {
            window.open("messenger.php?id=" + id,"_self")
        }
    </script>
    </body>
    </html>
<?php
mysqli_close($mysqli);
?>