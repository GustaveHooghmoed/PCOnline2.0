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
if (!park::exist($mysqli, $parkid) || (park::isDeleted($mysqli, $parkid))) {
    header("Location: home.php");
    exit;
}
if (!park::IsUserStaff($mysqli, $parkid, $_SESSION['UUID'])) {
    header("Location: home.php");
    exit;
}
if (!park::CanWriteArticle($mysqli, $parkid, $_SESSION['UUID'])) {
    header("Location: home.php");
    exit;
}
if (isset($_POST['submit'])) {
    $parkid = $_POST['id'];
    $title = $_POST['title'];
    $title = trim($title);
    $title = strip_tags($title);
    $title = mysqli_real_escape_string($mysqli, $title);
    $body = $_POST['article'];
    $body = $purifier->purify($_POST['article']);
    //  $body = preg_replace("/\r\n|\r/", "[enter]", $body);
    //$body = strip_tags($body, '<strong>, <i>, <br>');
    // $body = trim($body);
    $body = mysqli_real_escape_string($mysqli, $body);

    $bodyimg = '';
    if (isset($_FILES['headerimage'])) {
        $imga = $_FILES['articleimage'];
        $bodyimg = common::uploadimagenotmp($imga);
    } else {
        $bodyimg = 'Invalid URL';
    }

    $headerimg = '';
    if (isset($_FILES['headerimage'])) {
        $imgb = $_FILES['headerimage'];
        $headerimg = common::uploadimagenotmp($imgb);
    } else {
        $headerimg = 'Invalid URL';
    }
    if (park::IsUserStaff($mysqli, $parkid, $_SESSION['UUID']) || park::IsUserOwner($mysqli, $parkid, $_SESSION['UUID'])) {
        article::post($mysqli, $parkid, $title, $body, $headerimg, $bodyimg);
        header("Location: home.php");
        exit;
    } else {
        header("Location: home.php");
        exit;
    }
}
$active = '';
?>
<?php system::copyRightSign(); ?>
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
                            <h3 class="panel-title">Artikel</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <form name="register" id="register"
                                          action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                                          enctype="multipart/form-data" method="post" autocomplete="off"
                                          class="form-horizontal">
                                        <div class="form-group">
                                            <label for="title" class="col-md-2 control-label"><span class="text-info">Titel</span></label>
                                            <div class="col-md-10" id="titlediv">
                                                <input type="hidden" name="id" value="<?php echo $parkid; ?>" required>
                                                <input type="text" class="form-control" name="title" id="title"
                                                       placeholder="Typ hier de titel van het artikel" value=""
                                                       required>
                                            </div>
                                        </div>
                                        <div class="form-group">

                                            <label for="article" class="col-md-2 control-label"><span class="text-info">Artikel</span></label>
                                            <div class="col-md-10" id="titlediv">
                                                <textarea type="text" class="form-control" name="article" id="article"
                                                          placeholder="Typ hier het artikel" value="" rows="10"
                                                          required></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="articleheader" class="col-md-2 control-label"><span
                                                        class="text-info">Header<br/><small><span class="text-danger">Beste afmeting is 500x300 pixels</span></small></span></label>
                                            <div class="col-md-10" id="headdiv">
                                                <input type="file" id="headerimage" name="headerimage" accept="image/*"
                                                       required>
                                                <input type="text" readonly="" class="form-control"
                                                       placeholder="Kies een afbeelding" id="headertext">
                                                <script>
                                                    $("#headerimage").change(function () {
                                                        if ($("#headerimage").val() == '') {
                                                            document.getElementById("headertext").placeholder = 'Kies een afbeelding';
                                                        } else {
                                                            document.getElementById("headertext").placeholder = $("#headerimage").val().replace(/C:\\fakepath\\/i, '');
                                                        }
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="articleimage" class="col-md-2 control-label"><span
                                                        class="text-info">Artikel afbeelding(en)<br/><small><span
                                                                class="text-danger">Beste afmeting is 500x300 pixels.</span></small></span></label>
                                            <div class="col-md-10" id="articlediv">
                                                <input type="file" id="articleimage" name="articleimage"
                                                       accept="image/*" onchange="loadBodyPreview(this)" required>
                                                <input type="text" readonly="" class="form-control"
                                                       placeholder="Kies een afbeelding" id="articletext">
                                                <script>
                                                    $("#articleimage").change(function () {
                                                        if ($("#articleimage").val() == '') {
                                                            document.getElementById("articletext").placeholder = 'Kies een afbeelding';
                                                        } else {
                                                            document.getElementById("articletext").placeholder = $("#articleimage").val().replace(/C:\\fakepath\\/i, '');
                                                        }
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-danger">Alle velden moeten worden ingevuld (inclusief de
                                                afbeelding velden)</p>
                                            <p class="text-danger">Het verwerken van een artikel kan eventjes duren! Dus
                                                heb geduld tijdens heb posten van je artikel.</p>
                                            <button type="submit" class="btn btn-raised btn-success" name="submit"
                                                    id="postbutton">Post
                                            </button>
                                            <button type="button" onclick="loadPreview()"
                                                    class="btn btn-raised btn-info" data-toggle="modal"
                                                    data-target="#preview">Preview
                                            </button>
                                        </div>
                                        <div class="modal fade" id="preview" role="dialog">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            &times;
                                                        </button>
                                                        <h4 class="modal-title">Artikel preview</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <img src="nothing" alt="Geen afbeelding gekozen"
                                                             class="img-responsive center-block" id="prvimage"/>
                                                        <h3 id="prvtitle"></h3>
                                                        <p id="prvbody"></p>
                                                    </div>
                                                    <script>
                                                        function loadPreview() {
                                                            document.getElementById('prvtitle').innerHTML = document.getElementById('title').value;
                                                            document.getElementById('prvbody').innerHTML = document.getElementById('article').value.replace(/\r?\n/g, '<br />');
                                                            ;
                                                        }

                                                        function loadBodyPreview(fileInput) {
                                                            var files = fileInput.files;
                                                            for (var i = 0; i < files.length; i++) {
                                                                var file = files[i];
                                                                var imageType = /image.*/;
                                                                if (!file.type.match(imageType)) {
                                                                    continue;
                                                                }
                                                                var img = document.getElementById("prvimage");
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
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default"
                                                                data-dismiss="modal">Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 right-container well">
                    <h4 class="text-danger">Artikel Schrijven</h4>
                    <p class="text-muted">Bij het schrijven van het artikel maak je gebruik van een WYSIWYG editor, hiermee is het schrijven van een artikel nog makkelijker! </p>
                    <p class="text-muted">Je kan nu plaatjes en youtube filmpjes in je artikel toevoegen.</p>
                    <p class="text-muted">Het is makkelijker dan ooit om lijstjes te maken!</p>
                    <p class="text-danger">Alle javascript codes worden weg gefilterd!</p>
                    <hr>
                    <?php ads::skycraper(); ?>
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
            $('#article').summernote({
                lang: 'nl-NL' // default: 'en-US'
            });
        });
    </script>

    </body>
    </html>
<?php
mysqli_close($mysqli);
?>