<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 17-1-2017
 * Time: 21:01
 */
?>
<nav class="navbar navbar-danger">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-inverse-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="home.php" class="text-white"><strong class="navbar-brand"><span><?php echo language::getString($mysqli, 'NAV_BRAND'); ?></span></strong></a>
        </div>
        <?php if(isset($_SESSION['UUID'])) {?>
        <div class="navbar-collapse collapse navbar-inverse-collapse">
            <ul class="nav navbar-nav">
                <li <?php if(strcmp($active, "home") == 0) { echo 'class="active"';}?>><a href="home.php"><?php echo language::getString($mysqli, 'NAV_HOME'); ?></a></li>
                <li class="dropdown">
                    <a href="#" data-target="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo language::getString($mysqli, 'NAV_PARKS'); ?><b class="caret"></b></a>
                    <ul class="dropdown dropdown-menu multi-level">
                        <?php nav::parks($mysqli, $_SESSION['UUID']); ?>
                        <li <?php if(strcmp($active, "parkrequest") == 0) { echo 'class="parkrequest"';}?>><a href="./parkrequest.php"><?php echo language::getString($mysqli, 'NAV_PARK_REQUEST'); ?></a></li>
                    </ul>

                </li>
                <li class="dropdown <?php if(strcmp($active, "parkcraft") == 0) { echo 'active';}?>">
                    <a href="#" data-target="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo language::getString($mysqli, 'COMPANY_NAME'); ?><b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="parkcraft.php?tutorials" ><?php echo language::getString($mysqli, 'NAV_TUTORIALS'); ?></a></li>
                        <li><a href="#" class="notallowed"><?php echo language::getString($mysqli, 'NAV_POTW'); ?></a></li>
                        <li><a href="#" class="notallowed"><?php echo language::getString($mysqli, 'NAV_POTM'); ?></a></li>
                        <li><a href="videos.php"><?php echo language::getString($mysqli, 'NAV_VIDEOS'); ?></a></li>
                        <li><a href="#" class="notallowed"><?php echo language::getString($mysqli, 'NAV_EVENTS'); ?></a></li>
                        <li><a href="parklist.php"><?php echo language::getString($mysqli, 'NAV_PARKLIST'); ?></a></li>
                        <?php if(parkcraft::IsAuthor($mysqli, $_SESSION['UUID'])) {?>
                            <li class="divider"></li>
                                <a href="#" class="dropdown-header"><li class="dropdown-header"><?php echo language::getString($mysqli, 'NAV_AUTORS'); ?></li></a>
                            <?php if(parkcraft::canWriteTutorials($mysqli, $_SESSION['UUID'])) {?>
                                <li><a href="author.php?tutorial"><?php echo language::getString($mysqli, 'NAV_WRITE_TUTORIAL'); ?></a></li>
                            <?php }?>
                            <?php if(parkcraft::canWritePluginVanDeWeek($mysqli, $_SESSION['UUID'])) {?>
                                <li><a href="author.php?pvdw"><?php echo language::getString($mysqli, 'NAV_WRITE_POTW'); ?></a></li>
                            <?php }?>
                            <?php if(parkcraft::canWriteParkVanDeMaand($mysqli, $_SESSION['UUID'])) {?>
                                <li><a href="author.php?pvdm"><?php echo language::getString($mysqli, 'NAV_WRITE_POTM'); ?></a></li>
                            <?php }?>
                        <?php }?>
                    </ul>
                </li>
                <li <?php if(strcmp($active, "vacature") == 0) { echo 'class="active"';}?>><a href="applications.php"><?php echo language::getString($mysqli, 'NAV_VACANCIES'); ?><span class="badge text-white"><?php echo vacature::CountAllVacatures($mysqli); ?></span></a></li>
                <li <?php if(strcmp($active, "plugins") == 0) { echo 'class="active"';}?>><a href="plugins.php"><?php echo language::getString($mysqli, 'NAV_PLUGINS'); ?><span class="badge text-white"><?php echo plugins::plugincount($mysqli); ?></span></a></li>
            </ul>
            <form class="navbar-form navbar-right" action="search.php" method="get">
                <div class="form-group">
                    <input type="hidden" name="articles">
                    <input type="text" class="form-control col-md-8" name="keywords" placeholder="<?php echo language::getString($mysqli, 'NAV_SEARCH'); ?>" value="<?php if(isset($_GET['keywords'])) { echo $_GET['keywords']; }?>">
                </div>
            </form>
            <ul class="nav navbar-nav navbar-right">
                <li <?php if(strcmp($active, "messenger") == 0) { echo 'class="active"';}?>><a href="messenger.php"><?php echo language::getString($mysqli, 'NAV_MESSAGES'); ?><span class="badge text-white" id="chatcounts"><?php echo chats::countNotReadedMessages($mysqli, $_SESSION['UUID']);?></span></a></li>
                <?php if(staff::canUseStaffPanel($mysqli, $_SESSION['UUID'])) {?>
                    <li <?php if(strcmp($active, "staff") == 0) { echo 'class="active"';}?>><a href="staff.php?home"><?php echo language::getString($mysqli, 'NAV_STAFFPANEL'); ?></a></li>
                <?php }?>
                <li class="dropdown">
                    <a href="#" data-target="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo user::getName($mysqli) ?>
                        <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="settings.php"><?php echo language::getString($mysqli, 'NAV_SETTINGS'); ?></a></li>
                        <li><a href="settings.php?followed"><?php echo language::getString($mysqli, 'NAV_FOLLOWED'); ?></a></li>
                        <li><a href="help.php"><?php echo language::getString($mysqli, 'NAV_HELP'); ?></a></li>
                        <li><a href="user.php?id=<?php echo $_SESSION['user'];?>"><?php echo language::getString($mysqli, 'NAV_PROFILE'); ?></a></li>
                        <li class="divider"></li>
                        <li><a href="logout.php"><?php echo language::getString($mysqli, 'NAV_LOGOUT'); ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <?php } else if($cvwl) {?>
        <div class="navbar-collapse collapse navbar-inverse-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="index.php"><?php echo language::getString($mysqli, 'NAV_LOGIN'); ?></a></li>
            </ul>
        </div>
        <?php } else {
            header("Location: index.php");
            exit;
        }?>
    </div>
    <script type="text/javascript">
        $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
            // Avoid following the href location when clicking
            event.preventDefault();
            // Avoid having the menu to close when clicking
            event.stopPropagation();
            // Re-add .open to parent sub-menu item
            $(this).parent().addClass('open');
            $(this).parent().find("ul").parent().find("li.dropdown").addClass('open');
        });
    </script>
  
</nav>
