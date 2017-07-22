<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-1-2017
 * Time: 22:16
 */
$cvwl = false;
include 'includes/phpimports.php';
$active = 'help';
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
                            <h3 class="panel-title">Help</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <?php
                                        if(isset($_GET['plugin'])) {
                                            ?>
                                            <h3>Hoe beheer ik de statussen via mijn server?</h3>
                                            <a href="resources/ParkCraftRideUpdate1.8.jar" class="btn btn-success" download>Download hier de plugin (1.8)</a>
											<a href="resources/ParkCraftRideUpdate1.11.2.jar" class="btn btn-success" download>Download hier de plugin (1.11.2)</a>
                                            <h4>Commando's attractie updater</h4>
                                            <code>
                                                <span>Permissie: pcru.execute</span><br />
                                                <span>/PCRU {APIKEY} {Ride Code} {status}</span><br /><br />
                                                <span>{APIKEY} Deze key kun je vinden bij jouw park hun instellingen en dan bij snelkoppeling "Statussen beheren".</span><br /><br />
                                                <span>{Ride Code} Deze key kun je vinden bij jouw park hun instellingen en dan bij snelkoppeling "Statussen beheren". Dan zie je jouw attracties en hun attracties codes (Code naam).</span><br /><br />
                                                <span>{status} Dit is een cijfer van 0 tot 5.</span><br />
                                                <span>0 is Gesloten</span><br />
                                                <span>1 is Geopend</span><br />
                                                <span>2 is Onderhoud</span><br />
                                                <span>3 is Winter</span><br />
                                                <span>4 is In Bouw</span><br />
                                                <span>5 is Storing</span><br /><br />
                                            </code>
                                            <h4>Commando's show updater</h4>
                                            <code>
                                                <span>Permissie: pcsu.execute</span><br />
                                                <span>/PCSU {APIKEY} {Show Code} {status} [tijd]</span><br /><br />
                                                <span>{APIKEY} Deze key kun je vinden bij jouw park hun instellingen en dan bij snelkoppeling "Statussen beheren".</span><br /><br />
                                                <span>{Ride Code} Deze key kun je vinden bij jouw park hun instellingen en dan bij snelkoppeling "Statussen beheren". Dan zie je jouw statussen en hun show codes (Code naam).</span><br /><br />
                                                <span>{status} Dit is een cijfer van 0 tot 3.</span><br />
                                                <span>0 is Gesloten</span><br />
                                                <span>1 is Bezig</span><br />
                                                <span>2 is Begint om (Hier bij de tijd in het commando zetten bijv. /PCSU APIKEY SHOWCODE 2 01:00)</span><br />
                                                <span>3 is Onderhoud</span><br />
                                            </code><br/>
                                            <h4>Voor ontwikkelaars</h4>
                                            <p>Je kunt de API gebruiken in je eigen plugin door de plugin in te porten en dan deze code te gebruiken.</p>
                                            <pre id="code">
                                                <span>Voor attracties</span>
                                                <span></span>
                                                <span>RideAPI rapi = new RideAPI(APIKey);</span>
                                                <span>rapi.updateRideStatus(Ride_Code, Status);</span>
                                                <span></span>
                                                <span></span>
                                                <span>Voor shows</span>
                                                <span></span>
                                                <span>ShowAPI sapi = new ShowAPI(APIKey);</span>
                                                <span>sapi.updateShowStatus(Ride_Code, Status, Time);</span>
                                                <span>Als de status niet 2 is Time leeglaten</span>
                                            </pre><br/>
                                            <?php
                                        } else {
                                            help::loadHelp($mysqli);?>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 right-container well">
                    <h4 class="text-danger">Snelkoppelingen</h4>
                    <p><a href="?plugin" class="shortcut"><i class="material-icons">airline_seat_recline_normal</i><span>Plugin</span></a></p>
                    <p><a href="?" class="shortcut"><i class="material-icons">help</i><span>Help</span></a></p>
                   <!-- col-3 !-->
                    <?php ads::skycraper();?>
                </div>
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