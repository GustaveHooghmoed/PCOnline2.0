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
            <a href="home.php" class="text-white"><strong class="navbar-brand"><span>ParkCraft Online</span></strong></a>
        </div>
        <?php if(isset($_SESSION['UUID'])) {?>
        <div class="navbar-collapse collapse navbar-inverse-collapse">
            <ul class="nav navbar-nav">
                <li <?php if(strcmp($active, "home") == 0) { echo 'class="active"';}?>><a href="home.php">Home</a></li>
                <li class="dropdown">
                    <a href="#" data-target="#" class="dropdown-toggle" data-toggle="dropdown">Parken<b class="caret"></b></a>
                    <ul class="dropdown dropdown-menu multi-level">
                        <?php nav::parks($mysqli, $_SESSION['UUID']); ?>
                        <li <?php if(strcmp($active, "parkrequest") == 0) { echo 'class="parkrequest"';}?>><a href="./parkrequest.php">Park aanvragen</a></li>
                    </ul>

                </li>
                <li class="dropdown <?php if(strcmp($active, "parkcraft") == 0) { echo 'active';}?>">
                    <a href="#" data-target="#" class="dropdown-toggle" data-toggle="dropdown">ParkCraft<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="parkcraft.php?tutorials" >Tutorials</a></li>
                        <li><a href="#" class="notallowed">Plugin van de Week</a></li>
                        <li><a href="#" class="notallowed">Park van de Maand</a></li>
                        <li><a href="videos.php">Video's</a></li>
                        <li><a href="#" class="notallowed">Evenementen</a></li>
                        <li><a href="parklist.php">Parken lijst</a></li>
                        <?php if(parkcraft::IsAuthor($mysqli, $_SESSION['UUID'])) {?>
                            <li class="divider"></li>
                            <a href="#" class="dropdown-header"><li class="dropdown-header">Auteurs</li></a>
                            <?php if(parkcraft::canWriteTutorials($mysqli, $_SESSION['UUID'])) {?>
                                <li><a href="author.php?tutorial">Tutorial schrijven</a></li>
                            <?php }?>
                            <?php if(parkcraft::canWritePluginVanDeWeek($mysqli, $_SESSION['UUID'])) {?>
                                <li><a href="author.php?pvdw">Plugin van de week schrijven</a></li>
                            <?php }?>
                            <?php if(parkcraft::canWriteParkVanDeMaand($mysqli, $_SESSION['UUID'])) {?>
                                <li><a href="author.php?pvdm">Park van de maand schrijven</a></li>
                            <?php }?>
                        <?php }?>
                    </ul>
                </li>
                <li <?php if(strcmp($active, "vacature") == 0) { echo 'class="active"';}?>><a href="applications.php">Vacatures <span class="badge text-white"><?php echo vacature::CountAllVacatures($mysqli); ?></span></a></li>
                <li <?php if(strcmp($active, "plugins") == 0) { echo 'class="active"';}?>><a href="plugins.php">Plugins <span class="badge text-white"><?php echo plugins::plugincount($mysqli); ?></span></a></li>
            </ul>
            <form class="navbar-form navbar-right" action="search.php" method="get">
                <div class="form-group">
                    <input type="hidden" name="articles">
                    <input type="text" class="form-control col-md-8" name="keywords" placeholder="Zoeken" value="<?php if(isset($_GET['keywords'])) { echo $_GET['keywords']; }?>">
                </div>
            </form>
            <ul class="nav navbar-nav navbar-right">
                <li <?php if(strcmp($active, "messenger") == 0) { echo 'class="active"';}?>><a href="messenger.php">Berichten <span class="badge text-white" id="chatcounts"><?php echo chats::countNotReadedMessages($mysqli, $_SESSION['UUID']);?></span></a></li>
                <?php if(staff::canUseStaffPanel($mysqli, $_SESSION['UUID'])) {?>
                    <li <?php if(strcmp($active, "staff") == 0) { echo 'class="active"';}?>><a href="staff.php?home">Staf paneel</a></li>
                <?php }?>
                <li class="dropdown">
                    <a href="#" data-target="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo user::getName($mysqli) ?>
                        <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="settings.php">Instellingen</a></li>
                        <li><a href="settings.php?followed">Volgend</a></li>
                        <li><a href="help.php">Help</a></li>
                        <li><a href="user.php?id=<?php echo $_SESSION['user'];?>">Profiel</a></li>
                        <li class="divider"></li>
                        <li><a href="logout.php">Uitloggen</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <?php } else if($cvwl) {?>
        <div class="navbar-collapse collapse navbar-inverse-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="index.php">Login</a></li>
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
    <script>
        function countmessages() {
            $('#chatcounts').load('/chat-api.php?chatcount');
        }
        setInterval(countmessages, 5000);
    </script>
</nav>
