<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 19-1-2017
 * Time: 10:21
 */
error_reporting(0);
class user {
    static function loginWithCookie($mysqli, $email, $sessionID, $redirect) {
        if(!empty($email) && !empty($sessionID)) {

            $sql="SELECT * FROM pco_users WHERE email='$email' AND sessionID='$sessionID'";
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);
            if($count > 0) {
                if(!user::hasAccess($mysqli, $row['UUID'])) {
                    header("Location: index.php?warning=Je hebt geen toegang tot ParkCraft Online.");
                    exit;
                }
                session_start();
                $_SESSION['user'] = $row['ID'];
                $_SESSION['UUID'] = $row['UUID'];
                header("Location: ".$redirect);
                exit;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    static function createSessionID($mysqli) {
        $key = '';
        $keys = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
        for ($i = 0; $i < 20; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        $sql="SELECT * FROM pco_users WHERE sessionID='$key'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return user::createSessionID($mysqli);
        } else {
            return $key;
        }
    }
    static function login($mysqli, $email, $password, $redirect, $remeberme) {
        if(!empty($email) && !empty($password)) {
            $salt = 'fe98yh7834bd2s';
            $hashed = hash('sha256', $salt.$password);

            $sql="SELECT * FROM pco_users WHERE email='$email' AND password='$hashed'";
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);
            if($count > 0) {
                if(!user::hasAccess($mysqli, $row['UUID'])) {
                    header("Location: index.php?warning=Je hebt geen toegang tot ParkCraft Online.");
                    exit;
                }
                session_start();
                $_SESSION['user'] = $row['ID'];
                $_SESSION['UUID'] = $row['UUID'];
                $sessionID = user::createSessionID($mysqli);
                $sql1="UPDATE pco_users SET sessionID='$sessionID' WHERE email='$email' AND password='$hashed'";
                $result1 = mysqli_query($mysqli, $sql1);
                if($remeberme) {
                    setcookie("pcoemail", $email, time() + (86400 * 30), '/');
                    setcookie("pcosessionid", $sessionID, time() + (86400 * 30), '/');
                }
                header("Location: ".$redirect);
                exit;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    static function hasAccess($mysqli, $userid) {
        $sql="SELECT * FROM pco_users WHERE UUID='$userid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($row['access'] == 1) {
            return true;
        } else {
            return false;
        }
    }
    static function isActivated($mysqli, $userid) {
        $sql="SELECT * FROM pco_users WHERE UUID='$userid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if(strcmp($row['activated'], '1') == 0) {
            return true;
        } else {
            return false;
        }
        return false;
    }
    static function register($mysqli, $name, $email, $password) {
        if(!empty($name) && !empty($email) && !empty($password)) {
            $name = strip_tags($name);
            $email = strip_tags($email);
            $salt = 'fe98yh7834bd2s';
            $hashed = hash('sha256', $salt.$password);
            $activationcode = common::random(40);
            $sql = "INSERT INTO pco_users (UUID, name, email, password, activated, profile_about) VALUES (UUID(), '$name', '$email', '$hashed', '$activationcode', '')";
            $result = mysqli_query($mysqli, $sql);
            user::sendActivationMail($mysqli, $email, $activationcode);
            return true;
        } else {
            return false;
        }
    }
    static function getActivationCode($mysqli, $uuid) {
        $sql="SELECT * FROM pco_users WHERE UUID='$uuid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['activated'];
        } else {
            return user::getActivationCode($mysqli, $uuid);
        }
    }
    static function exist($mysqli, $email) {
        $sql="SELECT * FROM pco_users WHERE email='$email'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
        return false;
    }
    static function update($mysqli, $name, $email, $rank, $access, $activated, $uuid) {
        $name = strip_tags($name);
        $email = strip_tags($email);
        $sql="UPDATE pco_users SET name='$name', email='$email', rank='$rank', access='$access', activated='$activated' WHERE UUID='$uuid'";
        $result = mysqli_query($mysqli, $sql);

        $sql="SELECT * FROM pco_staff WHERE UUID='$uuid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            if($rank > 0) {
                $sql="UPDATE pco_staff SET rank='$rank' WHERE UUID='$uuid'";
                $result = mysqli_query($mysqli, $sql);
            } else {
                $sql = "DELETE FROM pco_staff WHERE UUID = '$uuid'";
                $result = mysqli_query($mysqli, $sql);
            }
        } else {
            if($rank > 0) {
                $sql = "INSERT INTO pco_staff (UUID, rank) VALUES ('$uuid', '$rank');";
                $result = mysqli_query($mysqli, $sql);
            }
        }
    }
    static function getUUIDFromEmail($mysqli, $email) {
        $sql="SELECT * FROM pco_users WHERE email='$email'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['UUID'];
        }
        return '0';
    }
    static function existUUID($mysqli, $uuid) {
        $sql="SELECT * FROM pco_users WHERE UUID='$uuid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
        return false;
    }
    static function logout() {
        session_start();
        if(isset($_COOKIE["pcoemail"]) && isset($_COOKIE["pcosessionid"])) {
            unset($_COOKIE["pcoemail"]);
            unset($_COOKIE["pcosessionid"]);
            setcookie('pcoemail', null, -1, '/');
            setcookie('pcosessionid', null, -1, '/');
        }
        session_unset();
        session_destroy();
        session_write_close();
    }
    static function getUUIDFromID($mysqli, $id) {
        $sql="SELECT * FROM pco_users WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['UUID'];
        }
        return '0';
    }
    static function getIDFromUUID($mysqli, $uuid) {
        $sql="SELECT * FROM pco_users WHERE UUID='$uuid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['ID'];
        }
        return '0';
    }
    static function getName($mysqli) {
        $id = $_SESSION['user'];
        $sql="SELECT * FROM pco_users WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['name'];
        }
    }
    static function getEmail($mysqli, $uuid) {
        $sql="SELECT * FROM pco_users WHERE UUID='$uuid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['email'];
        }
    }
    static function getPrefix($mysqli, $rank) {
        $sql="SELECT * FROM pco_ranks WHERE rank='$rank'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['prefix'];
        }
    }
    static function getLabel($mysqli, $rank) {
        $sql="SELECT * FROM pco_ranks WHERE rank='$rank'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['color'];
        }
    }
    static function getNameByUUID($mysqli, $uuid) {
        $sql="SELECT * FROM pco_users WHERE UUID='$uuid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['name'];
        }
    }
    static function getRank($mysqli) {
        $id = $_SESSION['UUID'];
        $sql="SELECT * FROM pco_staff WHERE UUID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['rank'];
        } else {
            return 0;
        }
    }
    static function getRankByUUID($mysqli, $UUID) {
        $sql="SELECT * FROM pco_staff WHERE UUID='$UUID'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['rank'];
        } else {
            return 0;
        }
    }
    static function getCurrentPassword($mysqli, $UUID) {
        $sql="SELECT * FROM pco_users WHERE uuid='$UUID'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['password'];
        }
    }
    static function IsFollowingPark($mysqli, $parkid, $uuid) {
        $sql="SELECT * FROM pco_parks WHERE ID='$parkid' and followers LIKE '%{$uuid}%'";
        $result=mysqli_query($mysqli,$sql);
        $count=mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0){
            return 1;
        }
        return 0;
    }
    static function IsActivationCodeValid($mysqli, $code) {
        $sql="SELECT * FROM pco_users WHERE activated='$code'";
        $result=mysqli_query($mysqli,$sql);
        $count=mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0){
            if($code == 1) {
                return false;
            }
            return true;
        }
        return false;
    }
    static function IsPasswordCodeValid($mysqli, $code) {
        $sql="SELECT * FROM pco_users WHERE changepassword='$code'";
        $result=mysqli_query($mysqli,$sql);
        $count=mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0){
            if(strcmp($code, '0') == 0) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
    static function activateAccount($mysqli, $code) {
        $sql = "UPDATE pco_users SET activated='1' WHERE activated='$code'";
        $result = mysqli_query($mysqli, $sql);
    }
    static function changePassword($mysqli, $newpassword, $oldpassword, $userid) {
        $salt = 'fe98yh7834bd2s';
        $hashedold = hash('sha256', $salt.$oldpassword);
        $hashednew = hash('sha256', $salt.$newpassword);
        if(user::getCurrentPassword($mysqli, $userid) == $hashedold) {
            $sql = "UPDATE pco_users SET password='$hashednew' WHERE UUID='$userid'";
            $result = mysqli_query($mysqli, $sql);
            return true;
        } else {
            return false;
        }
    }
    static function changeForgotPassword($mysqli, $newpassword, $code) {
        $salt = 'fe98yh7834bd2s';
        $hashednew = hash('sha256', $salt.$newpassword);
        $sql = "UPDATE pco_users SET password='$hashednew', changepassword='0' WHERE changepassword='$code'";
        $result = mysqli_query($mysqli, $sql);
    }
    static function delete($mysqli, $code) {
        $sql = "DELETE FROM pco_users WHERE activated='$code'";
        $result = mysqli_query($mysqli, $sql);
    }
    static function sendActivationMail($mysqli, $email, $code) {

        $uuid = user::getUUIDFromEmail($mysqli, $email);
        $name = user::getNameByUUID($mysqli, $uuid);

        $to = $email;
        $subject = "Feedback";
        $htmlContent = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta name="viewport" content="width=device-width" />

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Parkcraft Online</title>

        </head>

        <body bgcolor="#FFFFFF">
        <style>
        /* -------------------------------------
                GLOBAL
        ------------------------------------- */
        * {
            margin:0;
            padding:0;
        }
        * { font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; }

        img {
            max-width: 100%;
        }
        .collapse {
            margin:0;
            padding:0;
        }
        body {
            -webkit-font-smoothing:antialiased;
            -webkit-text-size-adjust:none;
            width: 100%!important;
            height: 100%;
        }


        /* -------------------------------------
                ELEMENTS
        ------------------------------------- */
        a { color: #2BA6CB;}

        .btn {
            text-decoration:none;
            color: #FFF;
            background-color: #666;
            padding:10px 16px;
            font-weight:bold;
            margin-right:10px;
            text-align:center;
            cursor:pointer;
            display: inline-block;
        }

        p.callout {
            padding:15px;
            background-color:#ECF8FF;
            margin-bottom: 15px;
        }
        .callout a {
            font-weight:bold;
            color: #2BA6CB;
        }

        table.social {
        /* 	padding:15px; */
            background-color: #ebebeb;

        }
        .social .soc-btn {
            padding: 3px 7px;
            font-size:12px;
            margin-bottom:10px;
            text-decoration:none;
            color: #FFF;font-weight:bold;
            display:block;
            text-align:center;
        }
        a.fb { background-color: #3B5998!important; }
        a.tw { background-color: #1daced!important; }
        a.gp { background-color: #DB4A39!important; }
        a.ms { background-color: #000!important; }

        .sidebar .soc-btn {
            display:block;
            width:100%;
        }

        /* -------------------------------------
                HEADER
        ------------------------------------- */
        table.head-wrap { width: 100%;}

        .header.container table td.logo { padding: 15px; }
        .header.container table td.label { padding: 15px; padding-left:0px;}


        /* -------------------------------------
                BODY
        ------------------------------------- */
        table.body-wrap { width: 100%;}


        /* -------------------------------------
                FOOTER
        ------------------------------------- */
        table.footer-wrap { width: 100%;	clear:both!important;
        }
        .footer-wrap .container td.content  p { border-top: 1px solid rgb(215,215,215); padding-top:15px;}
        .footer-wrap .container td.content p {
            font-size:10px;
            font-weight: bold;

        }


        /* -------------------------------------
                TYPOGRAPHY
        ------------------------------------- */
        h1,h2,h3,h4,h5,h6 {
        font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; line-height: 1.1; margin-bottom:15px; color:#000;
        }
        h1 small, h2 small, h3 small, h4 small, h5 small, h6 small { font-size: 60%; color: #6f6f6f; line-height: 0; text-transform: none; }

        h1 { font-weight:200; font-size: 44px;}
        h2 { font-weight:200; font-size: 37px;}
        h3 { font-weight:500; font-size: 27px;}
        h4 { font-weight:500; font-size: 23px;}
        h5 { font-weight:900; font-size: 17px;}
        h6 { font-weight:900; font-size: 14px; text-transform: uppercase; color:#444;}

        .collapse { margin:0!important;}

        p, ul {
            margin-bottom: 10px;
            font-weight: normal;
            font-size:14px;
            line-height:1.6;
        }
        p.lead { font-size:17px; }
        p.last { margin-bottom:0px;}

        ul li {
            margin-left:5px;
            list-style-position: inside;
        }

        /* -------------------------------------
                SIDEBAR
        ------------------------------------- */
        ul.sidebar {
            background:#ebebeb;
            display:block;
            list-style-type: none;
        }
        ul.sidebar li { display: block; margin:0;}
        ul.sidebar li a {
            text-decoration:none;
            color: #666;
            padding:10px 16px;
        /* 	font-weight:bold; */
            margin-right:10px;
        /* 	text-align:center; */
            cursor:pointer;
            border-bottom: 1px solid #777777;
            border-top: 1px solid #FFFFFF;
            display:block;
            margin:0;
        }
        ul.sidebar li a.last { border-bottom-width:0px;}
        ul.sidebar li a h1,ul.sidebar li a h2,ul.sidebar li a h3,ul.sidebar li a h4,ul.sidebar li a h5,ul.sidebar li a h6,ul.sidebar li a p { margin-bottom:0!important;}



        /* ---------------------------------------------------
                RESPONSIVENESS
                Nuke it from orbit. It\'s the only way to be sure.
        ------------------------------------------------------ */

        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            display:block!important;
            max-width:600px!important;
            margin:0 auto!important; /* makes it centered */
            clear:both!important;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            padding:15px;
            max-width:600px;
            margin:0 auto;
            display:block;
        }

        /* Let\'s make sure tables in the content area are 100% wide */
        .content table { width: 100%; }


        /* Odds and ends */
        .column {
            width: 300px;
            float:left;
        }
        .column tr td { padding: 15px; }
        .column-wrap {
            padding:0!important;
            margin:0 auto;
            max-width:600px!important;
        }
        .column table { width:100%;}
        .social .column {
            width: 280px;
            min-width: 279px;
            float:left;
        }

        /* Be sure to place a .clear element after each set of columns, just to be safe */
        .clear { display: block; clear: both; }


        /* -------------------------------------------
                PHONE
                For clients that support media queries.
                Nothing fancy.
        -------------------------------------------- */
        @media only screen and (max-width: 600px) {

            a[class="btn"] { display:block!important; margin-bottom:10px!important; background-image:none!important; margin-right:0!important;}

            div[class="column"] { width: auto!important; float:none!important;}

            table.social div[class="column"] {
                width:auto!important;
            }

        }
        </style>
        <!-- HEADER -->
        <table class="head-wrap" bgcolor="#f44242">
            <tr>
                <td></td>
                <td class="header container" >

                        <div class="content">
                            <h3><span style="color: white;">ParkCraft Online</span></h3>
                        </div>

                </td>
                <td></td>
            </tr>
        </table><!-- /HEADER -->


        <!-- BODY -->
        <table class="body-wrap">
            <tr>
                <td></td>
                <td class="container" bgcolor="#FFFFFF">

                    <div class="content">
                    <table>
                        <tr>
                            <td>
                                <h3>Beste '.$name.',</h3>
                                <p class="lead">Wat leuk dat je hebt geregistreerd op ParkCraft Online!.</p>
                                <p>Je bent van plan om bij een van de grootste Minecraft pretpark community aan te sluiten.</p>
                                <p>ParkCraft Online heeft de volgende functies:</p>
                                <ul>
                                    <li>Je favoriete parken te volgen</i>
                                    <li>Je eigen park aanmelden</i>
                                    <li>Artikelen schrijven over en voor jouw park</i>
                                    <li>En nog veel meer...</i>
                                </ul>
                                <!-- Callout Panel -->
                                <p class="callout">
                                    Je kunt je account activeren door <a href="https://www.parkcraft.nl/activate.php?code='.$code.'">hier</a> te klikken.
                                </p><!-- /Callout Panel -->
                                <table class="social" width="100%">
                                    <tbody><tr>
                                        <td>

                                            <!-- column 1 -->
                                            <table align="left" class="column">
                                                <tbody><tr>
                                                    <td>

                                                        <h5 class="">heb je nog vragen? Je kunt ons hier bereiken:</h5>
                                                        <p class=""><a href="https://www.facebook.com/ParkCraft-370915049752819/" class="soc-btn fb">Facebook</a> <a href="https://twitter.com/ParkenCraft" class="soc-btn tw">Twitter</a> <a href="https://www.youtube.com/ParkCraft" class="soc-btn gp">YouTube</a></p>


                                                    </td>
                                                </tr>
                                            </tbody></table><!-- /column 1 -->

                                            <!-- column 2 -->
                                            <table align="left" class="column">
                                                <tbody><tr>
                                                    <td>

                                                        <h5 class="">Heb je fouten gevonden?</h5>
                                                        <p>Email: <strong><a href="emailto:dani@parkcraft.nl">dani@parkcraft.nl</a></strong></p>

                                                    </td>
                                                </tr>
                                            </tbody></table><!-- /column 2 -->

                                            <span class="clear"></span>

                                        </td>
                                    </tr>
                                </tbody></table>
                            </td>
                        </tr>
                    </table>
                    </div>
                </td>
                <td></td>
            </tr>
        </table>

        </body>
        </html>';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: noreply@parkcraft.nl' . "\r\n";
        mail($to,$subject,$htmlContent,$headers);
    }
    static function sendChangePassword($mysqli, $email) {

        $uuid = user::getUUIDFromEmail($mysqli, $email);
        $name = user::getNameByUUID($mysqli, $uuid);
        $code = common::random(30);

        $sql = "UPDATE pco_users SET changepassword='$code' WHERE email='$email'";
        $result = mysqli_query($mysqli, $sql);


        $to = $email;
        $subject = "Wachtwoord vergeten";
        $htmlContent = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta name="viewport" content="width=device-width" />

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Parkcraft Online</title>

        </head>

        <body bgcolor="#FFFFFF">
        <style>
        /* -------------------------------------
                GLOBAL
        ------------------------------------- */
        * {
            margin:0;
            padding:0;
        }
        * { font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; }

        img {
            max-width: 100%;
        }
        .collapse {
            margin:0;
            padding:0;
        }
        body {
            -webkit-font-smoothing:antialiased;
            -webkit-text-size-adjust:none;
            width: 100%!important;
            height: 100%;
        }


        /* -------------------------------------
                ELEMENTS
        ------------------------------------- */
        a { color: #2BA6CB;}

        .btn {
            text-decoration:none;
            color: #FFF;
            background-color: #666;
            padding:10px 16px;
            font-weight:bold;
            margin-right:10px;
            text-align:center;
            cursor:pointer;
            display: inline-block;
        }

        p.callout {
            padding:15px;
            background-color:#ECF8FF;
            margin-bottom: 15px;
        }
        .callout a {
            font-weight:bold;
            color: #2BA6CB;
        }

        table.social {
        /* 	padding:15px; */
            background-color: #ebebeb;

        }
        .social .soc-btn {
            padding: 3px 7px;
            font-size:12px;
            margin-bottom:10px;
            text-decoration:none;
            color: #FFF;font-weight:bold;
            display:block;
            text-align:center;
        }
        a.fb { background-color: #3B5998!important; }
        a.tw { background-color: #1daced!important; }
        a.gp { background-color: #DB4A39!important; }
        a.ms { background-color: #000!important; }

        .sidebar .soc-btn {
            display:block;
            width:100%;
        }

        /* -------------------------------------
                HEADER
        ------------------------------------- */
        table.head-wrap { width: 100%;}

        .header.container table td.logo { padding: 15px; }
        .header.container table td.label { padding: 15px; padding-left:0px;}


        /* -------------------------------------
                BODY
        ------------------------------------- */
        table.body-wrap { width: 100%;}


        /* -------------------------------------
                FOOTER
        ------------------------------------- */
        table.footer-wrap { width: 100%;	clear:both!important;
        }
        .footer-wrap .container td.content  p { border-top: 1px solid rgb(215,215,215); padding-top:15px;}
        .footer-wrap .container td.content p {
            font-size:10px;
            font-weight: bold;

        }


        /* -------------------------------------
                TYPOGRAPHY
        ------------------------------------- */
        h1,h2,h3,h4,h5,h6 {
        font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; line-height: 1.1; margin-bottom:15px; color:#000;
        }
        h1 small, h2 small, h3 small, h4 small, h5 small, h6 small { font-size: 60%; color: #6f6f6f; line-height: 0; text-transform: none; }

        h1 { font-weight:200; font-size: 44px;}
        h2 { font-weight:200; font-size: 37px;}
        h3 { font-weight:500; font-size: 27px;}
        h4 { font-weight:500; font-size: 23px;}
        h5 { font-weight:900; font-size: 17px;}
        h6 { font-weight:900; font-size: 14px; text-transform: uppercase; color:#444;}

        .collapse { margin:0!important;}

        p, ul {
            margin-bottom: 10px;
            font-weight: normal;
            font-size:14px;
            line-height:1.6;
        }
        p.lead { font-size:17px; }
        p.last { margin-bottom:0px;}

        ul li {
            margin-left:5px;
            list-style-position: inside;
        }

        /* -------------------------------------
                SIDEBAR
        ------------------------------------- */
        ul.sidebar {
            background:#ebebeb;
            display:block;
            list-style-type: none;
        }
        ul.sidebar li { display: block; margin:0;}
        ul.sidebar li a {
            text-decoration:none;
            color: #666;
            padding:10px 16px;
        /* 	font-weight:bold; */
            margin-right:10px;
        /* 	text-align:center; */
            cursor:pointer;
            border-bottom: 1px solid #777777;
            border-top: 1px solid #FFFFFF;
            display:block;
            margin:0;
        }
        ul.sidebar li a.last { border-bottom-width:0px;}
        ul.sidebar li a h1,ul.sidebar li a h2,ul.sidebar li a h3,ul.sidebar li a h4,ul.sidebar li a h5,ul.sidebar li a h6,ul.sidebar li a p { margin-bottom:0!important;}



        /* ---------------------------------------------------
                RESPONSIVENESS
                Nuke it from orbit. It\'s the only way to be sure.
        ------------------------------------------------------ */

        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            display:block!important;
            max-width:600px!important;
            margin:0 auto!important; /* makes it centered */
            clear:both!important;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            padding:15px;
            max-width:600px;
            margin:0 auto;
            display:block;
        }

        /* Let\'s make sure tables in the content area are 100% wide */
        .content table { width: 100%; }


        /* Odds and ends */
        .column {
            width: 300px;
            float:left;
        }
        .column tr td { padding: 15px; }
        .column-wrap {
            padding:0!important;
            margin:0 auto;
            max-width:600px!important;
        }
        .column table { width:100%;}
        .social .column {
            width: 280px;
            min-width: 279px;
            float:left;
        }

        /* Be sure to place a .clear element after each set of columns, just to be safe */
        .clear { display: block; clear: both; }


        /* -------------------------------------------
                PHONE
                For clients that support media queries.
                Nothing fancy.
        -------------------------------------------- */
        @media only screen and (max-width: 600px) {

            a[class="btn"] { display:block!important; margin-bottom:10px!important; background-image:none!important; margin-right:0!important;}

            div[class="column"] { width: auto!important; float:none!important;}

            table.social div[class="column"] {
                width:auto!important;
            }

        }
        </style>
        <!-- HEADER -->
        <table class="head-wrap" bgcolor="#f44242">
            <tr>
                <td></td>
                <td class="header container" >

                        <div class="content">
                            <h3><span style="color: white;">ParkCraft Online</span></h3>
                        </div>

                </td>
                <td></td>
            </tr>
        </table><!-- /HEADER -->


        <!-- BODY -->
        <table class="body-wrap">
            <tr>
                <td></td>
                <td class="container" bgcolor="#FFFFFF">

                    <div class="content">
                    <table>
                        <tr>
                            <td>
                                <h3>Beste '.$name.',</h3>
                                <p class="lead">Je bent je wachtwoord vergeten. Je kunt hieronder je wachtwoord verander.</p>
                                <!-- Callout Panel -->
                                <p class="callout">
                                    Je kunt je wachtwoord veranderen door <a href="https://www.parkcraft.nl/index.php?changepassword=&code='.$code.'">hier</a> te klikken.
                                </p><!-- /Callout Panel -->
                                <table class="social" width="100%">
                                    <tbody><tr>
                                        <td>

                                            <!-- column 1 -->
                                            <table align="left" class="column">
                                                <tbody><tr>
                                                    <td>

                                                        <h5 class="">heb je nog vragen? Je kunt ons hier bereiken:</h5>
                                                        <p class=""><a href="https://www.facebook.com/ParkCraft-370915049752819/" class="soc-btn fb">Facebook</a> <a href="https://twitter.com/ParkenCraft" class="soc-btn tw">Twitter</a> <a href="https://www.youtube.com/ParkCraft" class="soc-btn gp">YouTube</a></p>


                                                    </td>
                                                </tr>
                                            </tbody></table><!-- /column 1 -->

                                            <!-- column 2 -->
                                            <table align="left" class="column">
                                                <tbody><tr>
                                                    <td>

                                                        <h5 class="">Heb je fouten gevonden?</h5>
                                                        <p>Email: <strong><a href="emailto:dani@parkcraft.nl">dani@parkcraft.nl</a></strong></p>

                                                    </td>
                                                </tr>
                                            </tbody></table><!-- /column 2 -->

                                            <span class="clear"></span>

                                        </td>
                                    </tr>
                                </tbody></table>
                            </td>
                        </tr>
                    </table>
                    </div>
                </td>
                <td></td>
            </tr>
        </table>

        </body>
        </html>';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: noreply@parkcraft.nl' . "\r\n";
        mail($to,$subject,$htmlContent,$headers);
    }
    static function sendEmail($mysqli, $email, $subject, $body) {

        $uuid = user::getUUIDFromEmail($mysqli, $email);
        $name = user::getNameByUUID($mysqli, $uuid);


        $to = $email;
        $htmlContent = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta name="viewport" content="width=device-width" />

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Parkcraft Online</title>

        </head>

        <body bgcolor="#FFFFFF">
        <style>
        /* -------------------------------------
                GLOBAL
        ------------------------------------- */
        * {
            margin:0;
            padding:0;
        }
        * { font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; }

        img {
            max-width: 100%;
        }
        .collapse {
            margin:0;
            padding:0;
        }
        body {
            -webkit-font-smoothing:antialiased;
            -webkit-text-size-adjust:none;
            width: 100%!important;
            height: 100%;
        }


        /* -------------------------------------
                ELEMENTS
        ------------------------------------- */
        a { color: #2BA6CB;}

        .btn {
            text-decoration:none;
            color: #FFF;
            background-color: #666;
            padding:10px 16px;
            font-weight:bold;
            margin-right:10px;
            text-align:center;
            cursor:pointer;
            display: inline-block;
        }

        p.callout {
            padding:15px;
            background-color:#ECF8FF;
            margin-bottom: 15px;
        }
        .callout a {
            font-weight:bold;
            color: #2BA6CB;
        }

        table.social {
        /* 	padding:15px; */
            background-color: #ebebeb;

        }
        .social .soc-btn {
            padding: 3px 7px;
            font-size:12px;
            margin-bottom:10px;
            text-decoration:none;
            color: #FFF;font-weight:bold;
            display:block;
            text-align:center;
        }
        a.fb { background-color: #3B5998!important; }
        a.tw { background-color: #1daced!important; }
        a.gp { background-color: #DB4A39!important; }
        a.ms { background-color: #000!important; }

        .sidebar .soc-btn {
            display:block;
            width:100%;
        }

        /* -------------------------------------
                HEADER
        ------------------------------------- */
        table.head-wrap { width: 100%;}

        .header.container table td.logo { padding: 15px; }
        .header.container table td.label { padding: 15px; padding-left:0px;}


        /* -------------------------------------
                BODY
        ------------------------------------- */
        table.body-wrap { width: 100%;}


        /* -------------------------------------
                FOOTER
        ------------------------------------- */
        table.footer-wrap { width: 100%;	clear:both!important;
        }
        .footer-wrap .container td.content  p { border-top: 1px solid rgb(215,215,215); padding-top:15px;}
        .footer-wrap .container td.content p {
            font-size:10px;
            font-weight: bold;

        }


        /* -------------------------------------
                TYPOGRAPHY
        ------------------------------------- */
        h1,h2,h3,h4,h5,h6 {
        font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; line-height: 1.1; margin-bottom:15px; color:#000;
        }
        h1 small, h2 small, h3 small, h4 small, h5 small, h6 small { font-size: 60%; color: #6f6f6f; line-height: 0; text-transform: none; }

        h1 { font-weight:200; font-size: 44px;}
        h2 { font-weight:200; font-size: 37px;}
        h3 { font-weight:500; font-size: 27px;}
        h4 { font-weight:500; font-size: 23px;}
        h5 { font-weight:900; font-size: 17px;}
        h6 { font-weight:900; font-size: 14px; text-transform: uppercase; color:#444;}

        .collapse { margin:0!important;}

        p, ul {
            margin-bottom: 10px;
            font-weight: normal;
            font-size:14px;
            line-height:1.6;
        }
        p.lead { font-size:17px; }
        p.last { margin-bottom:0px;}

        ul li {
            margin-left:5px;
            list-style-position: inside;
        }

        /* -------------------------------------
                SIDEBAR
        ------------------------------------- */
        ul.sidebar {
            background:#ebebeb;
            display:block;
            list-style-type: none;
        }
        ul.sidebar li { display: block; margin:0;}
        ul.sidebar li a {
            text-decoration:none;
            color: #666;
            padding:10px 16px;
        /* 	font-weight:bold; */
            margin-right:10px;
        /* 	text-align:center; */
            cursor:pointer;
            border-bottom: 1px solid #777777;
            border-top: 1px solid #FFFFFF;
            display:block;
            margin:0;
        }
        ul.sidebar li a.last { border-bottom-width:0px;}
        ul.sidebar li a h1,ul.sidebar li a h2,ul.sidebar li a h3,ul.sidebar li a h4,ul.sidebar li a h5,ul.sidebar li a h6,ul.sidebar li a p { margin-bottom:0!important;}



        /* ---------------------------------------------------
                RESPONSIVENESS
                Nuke it from orbit. It\'s the only way to be sure.
        ------------------------------------------------------ */

        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            display:block!important;
            max-width:600px!important;
            margin:0 auto!important; /* makes it centered */
            clear:both!important;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            padding:15px;
            max-width:600px;
            margin:0 auto;
            display:block;
        }

        /* Let\'s make sure tables in the content area are 100% wide */
        .content table { width: 100%; }


        /* Odds and ends */
        .column {
            width: 300px;
            float:left;
        }
        .column tr td { padding: 15px; }
        .column-wrap {
            padding:0!important;
            margin:0 auto;
            max-width:600px!important;
        }
        .column table { width:100%;}
        .social .column {
            width: 280px;
            min-width: 279px;
            float:left;
        }

        /* Be sure to place a .clear element after each set of columns, just to be safe */
        .clear { display: block; clear: both; }


        /* -------------------------------------------
                PHONE
                For clients that support media queries.
                Nothing fancy.
        -------------------------------------------- */
        @media only screen and (max-width: 600px) {

            a[class="btn"] { display:block!important; margin-bottom:10px!important; background-image:none!important; margin-right:0!important;}

            div[class="column"] { width: auto!important; float:none!important;}

            table.social div[class="column"] {
                width:auto!important;
            }

        }
        </style>
        <!-- HEADER -->
        <table class="head-wrap" bgcolor="#f44242">
            <tr>
                <td></td>
                <td class="header container" >

                        <div class="content">
                            <h3><span style="color: white;">ParkCraft Online</span></h3>
                        </div>

                </td>
                <td></td>
            </tr>
        </table><!-- /HEADER -->


        <!-- BODY -->
        <table class="body-wrap">
            <tr>
                <td></td>
                <td class="container" bgcolor="#FFFFFF">

                    <div class="content">
                    <table>
                        <tr>
                            <td>
                                <h3>Beste '.$name.',</h3>
                                <p class="lead">'.$body.'</p>
                                <!-- Callout Panel -->
                                <table class="social" width="100%">
                                    <tbody><tr>
                                        <td>

                                            <!-- column 1 -->
                                            <table align="left" class="column">
                                                <tbody><tr>
                                                    <td>

                                                        <h5 class="">heb je nog vragen? Je kunt ons hier bereiken:</h5>
                                                        <p class=""><a href="https://www.facebook.com/ParkCraft-370915049752819/" class="soc-btn fb">Facebook</a> <a href="https://twitter.com/ParkenCraft" class="soc-btn tw">Twitter</a> <a href="https://www.youtube.com/ParkCraft" class="soc-btn gp">YouTube</a></p>


                                                    </td>
                                                </tr>
                                            </tbody></table><!-- /column 1 -->

                                            <!-- column 2 -->
                                            <table align="left" class="column">
                                                <tbody><tr>
                                                    <td>

                                                        <h5 class="">Heb je fouten gevonden?</h5>
                                                        <p>Email: <strong><a href="emailto:dani@parkcraft.nl">dani@parkcraft.nl</a></strong></p>

                                                    </td>
                                                </tr>
                                            </tbody></table><!-- /column 2 -->

                                            <span class="clear"></span>

                                        </td>
                                    </tr>
                                </tbody></table>
                            </td>
                        </tr>
                    </table>
                    </div>
                </td>
                <td></td>
            </tr>
        </table>

        </body>
        </html>';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: noreply@parkcraft.nl' . "\r\n";
        mail($to,$subject,$htmlContent,$headers);
    }
    static function loadFollowedParks($mysqli, $uuid)
    {
        $sql = "SELECT * FROM pco_parks WHERE followers LIKE '%{$uuid}%'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            echo '
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Naam</th>
                            <th>Opties</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                if(!park::isDeleted($mysqli, $row['ID'])){
                    $logo = $row['logo'];
                    if(empty($row['logo'])) {
                        $logo = 'resources/defaultavatar.png';
                    }
                    $name = $row['name'];
                    $parkid = $row['ID'];
                    echo '<tr>';
                    echo '<td><img src="' . $logo . '" alt="" class="avatar"/></td>';
                    echo '<td><a href="park.php?id=' . $parkid . '" class="">'.$name.'</a></td>';
                    echo '<td><a href="park.php?id=' . $parkid . '&unfollow=&bts=" class="btn btn-danger btn-sm">Ontvolgen</a></td>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>Je volgt geen parken.</p>';
        }
    }
    static function loadAllUsers($mysqli, $pageid) {
        $pageusers = $pageid*50;
        $sql="SELECT * FROM pco_users LIMIT $pageusers, 50";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            echo '
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Email</th>
                            <th>Rank</th>
                            <th>Toegang</th>
                            <th>Geactiveerd</th>
                            <th>Laatst Online</th>
                            <th>Opties</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['ID'];
                $uuid = $row['UUID'];
                $name = $row['name'];
                $email = $row['email'];
                $rank = user::getRankByUUID($mysqli, $uuid);
                $access = $row['access'];
                $activated = $row['activated'];
                $lastonline = $row['last_execution'];
                if($activated == 1) {
                    $activated = 'Ja';
                } else {
                    $activated = 'Nee';
                }
                if($access == 1) {
                    $access = 'Ja';
                } else {
                    $access = 'Nee';
                }
                echo '<tr>';
                echo '<td><a href="user.php?id='.$id.'">'.$name.'</a></td>';
                echo '<td>'.$email.'</td>';
                echo '<td>'.$rank.'</td>';
                echo '<td>'.$access.'</td>';
                echo '<td>'.$activated.'</td>';
                echo '<td>'.$lastonline.'</td>';
                echo '<td><a href="staff.php?users=&id=' . $uuid . '&pi=' . ($pageid + 1) . '" class="btn btn-danger btn-sm">Bekijken</a></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            $sql1="SELECT * FROM pco_users";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            if($count1 > ($pageid+1)*50) {
                if($pageid > 0) {
                    echo '<a href="staff.php?users=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                }
                echo '<a href="staff.php?users=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
            } else {
                if ($pageid > 0) {
                    echo '<a href="staff.php?users=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                    if ($count1 > ($pageid + 1) * 50) {
                        echo '<a href="staff.php?users=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
                    }
                }
            }
        } else {
            echo '<p>Geen gebruikers gevonden op deze pagina.</p>';
        }
    }
    static function loadAllUsersSearch($mysqli, $keyword) {
        $sql="SELECT * FROM pco_users WHERE name LIKE '%{$keyword}%' OR email LIKE '%{$keyword}%' OR UUID LIKE '%{$keyword}%'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            if($count > 50) {
                echo '<p>Geef een specefiekere zoekopdracht!</p>';
                exit;
            }
            echo '
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Email</th>
                            <th>Rank</th>
                            <th>Toegang</th>
                            <th>Geactiveerd</th>
                            <th>Laatst Online</th>
                            <th>Opties</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['ID'];
                $uuid = $row['UUID'];
                $name = $row['name'];
                $email = $row['email'];
                $rank = user::getRankByUUID($mysqli, $uuid);
                $access = $row['access'];
                $activated = $row['activated'];
                $lastonline = $row['last_execution'];
                if($activated == 1) {
                    $activated = 'Ja';
                } else {
                    $activated = 'Nee';
                }
                if($access == 1) {
                    $access = 'Ja';
                } else {
                    $access = 'Nee';
                }
                echo '<tr>';
                echo '<td><a href="user.php?id='.$id.'">'.$name.'</a></td>';
                echo '<td>'.$email.'</td>';
                echo '<td>'.$rank.'</td>';
                echo '<td>'.$access.'</td>';
                echo '<td>'.$activated.'</td>';
                echo '<td>'.$lastonline.'</td>';
                echo '<td><a href="staff.php?users=&id=' . $uuid . '&pi=1" class="btn btn-danger btn-sm">Bekijken</a></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>Geen gebruikers gevonden op deze pagina.</p>';
        }
    }
    static function loadUserIn($mysqli, $uuid) {
        $sql="SELECT * FROM pco_users WHERE UUID='$uuid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            $ip = $row['ip'];
            $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
            $city =  $details->city;

            $file = htmlspecialchars($_SERVER["PHP_SELF"]);
            echo '                    <form name="edituser" id="edituser" action="'.$file.'" enctype="multipart/form-data" method="post" autocomplete="off" class="form-horizontal">';
            echo '                              <div class="form-group">
                                                    <label for="title" class="col-md-2 control-label"><span class="text-info">Naam</span></label>
                                                    <div class="col-md-10" id="naamdiv">
                                                        <input type="text" class="form-control" value="'.$row["name"].'" name="name" id="name"/>
                                                        <input type="hidden" class="form-control" value="'.$_GET['pi'].'" name="pi" id="pi"/>
                                                        <input type="hidden" class="form-control" value="'.$_GET['id'].'" name="uuid" id="uuid"/>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="title" class="col-md-2 control-label"><span class="text-info">Email</span></label>
                                                    <div class="col-md-10" id="naamdiv">
                                                        <input type="email" class="form-control" value="'.$row["email"].'" name="email" id="email"/>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="title" class="col-md-2 control-label"><span class="text-info">Account geactiveerd</span></label>
                                                    <div class="col-md-10" id="naamdiv">
                                                        <select name="ag" class="form-control">
                                                            <option value="1" '; if($row["activated"] == 1) { echo "selected"; } echo '>Ja</option>
                                                            <option value="0" '; if($row["activated"] != 1) { echo "selected"; } echo '>Nee</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="title" class="col-md-2 control-label"><span class="text-info">Toegang tot ParkCraft Online</span></label>
                                                    <div class="col-md-10" id="naamdiv">
                                                        <select name="ttpo" class="form-control">
                                                            <option value="1" '; if($row["access"] == 1) { echo "selected"; } echo '>Ja</option>
                                                            <option value="0" '; if($row["access"] != 1) { echo "selected"; } echo '>Nee</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="title" class="col-md-2 control-label"><span class="text-info">Rank</span></label>
                                                    <div class="col-md-10" id="naamdiv">
                                                        <select name="rank" class="form-control">
                                                            <option value="7" '; if(user::getRankByUUID($mysqli, $row["UUID"]) == 7) { echo "selected"; } echo '>Senior Developer</option>
                                                            <option value="6" '; if(user::getRankByUUID($mysqli, $row["UUID"]) == 6) { echo "selected"; } echo '>Beta-Tester</option>
                                                            <option value="5" '; if(user::getRankByUUID($mysqli, $row["UUID"]) == 5) { echo "selected"; } echo '>Auteur</option>
                                                            <option value="4" '; if(user::getRankByUUID($mysqli, $row["UUID"]) == 4) { echo "selected"; } echo '>Junior Developer</option>
                                                            <option value="3" '; if(user::getRankByUUID($mysqli, $row["UUID"]) == 3) { echo "selected"; } echo '>Beheerder</option>
                                                            <option value="2" '; if(user::getRankByUUID($mysqli, $row["UUID"]) == 2) { echo "selected"; } echo '>Video creator</option>
                                                            <option value="1" '; if(user::getRankByUUID($mysqli, $row["UUID"]) == 1) { echo "selected"; } echo '>Moderator</option>
                                                            <option value="0" '; if(user::getRankByUUID($mysqli, $row["UUID"]) == 0) { echo "selected"; } echo '>Gebruiker</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <p class="col-md-2 control-label">Laatste online:</p>
                                                    <p class="form-control">'.$row["last_execution"].'</p>
                                                </div>
                                                <div class="form-group">
                                                    <p class="col-md-2 control-label">IP:</p>
                                                    <p class="form-control">'.(user::getRank($mysqli) == 7 ? (empty(!$row["ip"]) ? $row["ip"].' / '.$city : 'n.v.t.') : 'n.v.t.').'</p>
                                                </div>
                                                <div class="text-center"">
                                                    <button type="submit" class="btn btn-raised btn-success" name="edituserbutton" id="edituserbutton">Opslaan
                                                    </button>
                                                    <p class="form-control">'.(user::getRank($mysqli) == 7 ? '<a href="staff.php?remote='.$row["UUID"].'" class="btn btn-raised btn-warning">Overnemen</a>' : '').'</p>
                                                </div>
                                </form>';
        }
    }
    static function sendEmailToEveryone($mysqli, $subject, $body) {
        if(!user::getRank($mysqli) > 2) {
            header("Location: staff.php?warning=Geen toegang tot dit gedeelte.");
            exit;
        }
        $sql="SELECT * FROM pco_users";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while ($row = mysqli_fetch_assoc($result)) {
            if(strcmp($row['news_email'], '1') == 0) {
                $uuid = $row['UUID'];
                $name = $row['name'];
                $email = $row['email'];

                $to = $email;
                $htmlContent = '
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                <meta name="viewport" content="width=device-width" />

                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>Parkcraft Online</title>

                </head>

                <body bgcolor="#FFFFFF">
                <style>
                /* -------------------------------------
                        GLOBAL
                ------------------------------------- */
                * {
                    margin:0;
                    padding:0;
                }
                * { font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; }

                img {
                    max-width: 100%;
                }
                .collapse {
                    margin:0;
                    padding:0;
                }
                body {
                    -webkit-font-smoothing:antialiased;
                    -webkit-text-size-adjust:none;
                    width: 100%!important;
                    height: 100%;
                }


                /* -------------------------------------
                        ELEMENTS
                ------------------------------------- */
                a { color: #2BA6CB;}

                .btn {
                    text-decoration:none;
                    color: #FFF;
                    background-color: #666;
                    padding:10px 16px;
                    font-weight:bold;
                    margin-right:10px;
                    text-align:center;
                    cursor:pointer;
                    display: inline-block;
                }

                p.callout {
                    padding:15px;
                    background-color:#ECF8FF;
                    margin-bottom: 15px;
                }
                .callout a {
                    font-weight:bold;
                    color: #2BA6CB;
                }

                table.social {
                /* 	padding:15px; */
                    background-color: #ebebeb;

                }
                .social .soc-btn {
                    padding: 3px 7px;
                    font-size:12px;
                    margin-bottom:10px;
                    text-decoration:none;
                    color: #FFF;font-weight:bold;
                    display:block;
                    text-align:center;
                }
                a.fb { background-color: #3B5998!important; }
                a.tw { background-color: #1daced!important; }
                a.gp { background-color: #DB4A39!important; }
                a.ms { background-color: #000!important; }

                .sidebar .soc-btn {
                    display:block;
                    width:100%;
                }

                /* -------------------------------------
                        HEADER
                ------------------------------------- */
                table.head-wrap { width: 100%;}

                .header.container table td.logo { padding: 15px; }
                .header.container table td.label { padding: 15px; padding-left:0px;}


                /* -------------------------------------
                        BODY
                ------------------------------------- */
                table.body-wrap { width: 100%;}


                /* -------------------------------------
                        FOOTER
                ------------------------------------- */
                table.footer-wrap { width: 100%;	clear:both!important;
                }
                .footer-wrap .container td.content  p { border-top: 1px solid rgb(215,215,215); padding-top:15px;}
                .footer-wrap .container td.content p {
                    font-size:10px;
                    font-weight: bold;

                }


                /* -------------------------------------
                        TYPOGRAPHY
                ------------------------------------- */
                h1,h2,h3,h4,h5,h6 {
                font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; line-height: 1.1; margin-bottom:15px; color:#000;
                }
                h1 small, h2 small, h3 small, h4 small, h5 small, h6 small { font-size: 60%; color: #6f6f6f; line-height: 0; text-transform: none; }

                h1 { font-weight:200; font-size: 44px;}
                h2 { font-weight:200; font-size: 37px;}
                h3 { font-weight:500; font-size: 27px;}
                h4 { font-weight:500; font-size: 23px;}
                h5 { font-weight:900; font-size: 17px;}
                h6 { font-weight:900; font-size: 14px; text-transform: uppercase; color:#444;}

                .collapse { margin:0!important;}

                p, ul {
                    margin-bottom: 10px;
                    font-weight: normal;
                    font-size:14px;
                    line-height:1.6;
                }
                p.lead { font-size:17px; }
                p.last { margin-bottom:0px;}

                ul li {
                    margin-left:5px;
                    list-style-position: inside;
                }

                /* -------------------------------------
                        SIDEBAR
                ------------------------------------- */
                ul.sidebar {
                    background:#ebebeb;
                    display:block;
                    list-style-type: none;
                }
                ul.sidebar li { display: block; margin:0;}
                ul.sidebar li a {
                    text-decoration:none;
                    color: #666;
                    padding:10px 16px;
                /* 	font-weight:bold; */
                    margin-right:10px;
                /* 	text-align:center; */
                    cursor:pointer;
                    border-bottom: 1px solid #777777;
                    border-top: 1px solid #FFFFFF;
                    display:block;
                    margin:0;
                }
                ul.sidebar li a.last { border-bottom-width:0px;}
                ul.sidebar li a h1,ul.sidebar li a h2,ul.sidebar li a h3,ul.sidebar li a h4,ul.sidebar li a h5,ul.sidebar li a h6,ul.sidebar li a p { margin-bottom:0!important;}



                /* ---------------------------------------------------
                        RESPONSIVENESS
                        Nuke it from orbit. It\'s the only way to be sure.
                ------------------------------------------------------ */

                /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
                .container {
                    display:block!important;
                    max-width:600px!important;
                    margin:0 auto!important; /* makes it centered */
                    clear:both!important;
                }

                /* This should also be a block element, so that it will fill 100% of the .container */
                .content {
                    padding:15px;
                    max-width:600px;
                    margin:0 auto;
                    display:block;
                }

                /* Let\'s make sure tables in the content area are 100% wide */
                .content table { width: 100%; }


                /* Odds and ends */
                .column {
                    width: 300px;
                    float:left;
                }
                .column tr td { padding: 15px; }
                .column-wrap {
                    padding:0!important;
                    margin:0 auto;
                    max-width:600px!important;
                }
                .column table { width:100%;}
                .social .column {
                    width: 280px;
                    min-width: 279px;
                    float:left;
                }

                /* Be sure to place a .clear element after each set of columns, just to be safe */
                .clear { display: block; clear: both; }


                /* -------------------------------------------
                        PHONE
                        For clients that support media queries.
                        Nothing fancy.
                -------------------------------------------- */
                @media only screen and (max-width: 600px) {

                    a[class="btn"] { display:block!important; margin-bottom:10px!important; background-image:none!important; margin-right:0!important;}

                    div[class="column"] { width: auto!important; float:none!important;}

                    table.social div[class="column"] {
                        width:auto!important;
                    }

                }
                </style>
                <!-- HEADER -->
                <table class="head-wrap" bgcolor="#f44242">
                    <tr>
                        <td></td>
                        <td class="header container" >

                                <div class="content">
                                    <h3><span style="color: white;">ParkCraft Online</span></h3>
                                </div>

                        </td>
                        <td></td>
                    </tr>
                </table><!-- /HEADER -->


                <!-- BODY -->
                <table class="body-wrap">
                    <tr>
                        <td></td>
                        <td class="container" bgcolor="#FFFFFF">

                            <div class="content">
                            <table>
                                <tr>
                                    <td>
                                        <h3>Beste ' . $name . ',</h3>
                                        <p class="lead">' . $body . '</p>
                                        <!-- Callout Panel -->
                                        <table class="social" width="100%">
                                            <tbody><tr>
                                                <td>

                                                    <!-- column 1 -->
                                                    <table align="left" class="column">
                                                        <tbody><tr>
                                                            <td>

                                                                <h5 class="">heb je nog vragen? Je kunt ons hier bereiken:</h5>
                                                                <p class=""><a href="https://www.facebook.com/ParkCraft-370915049752819/" class="soc-btn fb">Facebook</a> <a href="https://twitter.com/ParkenCraft" class="soc-btn tw">Twitter</a> <a href="https://www.youtube.com/ParkCraft" class="soc-btn gp">YouTube</a></p>


                                                            </td>
                                                        </tr>
                                                    </tbody></table><!-- /column 1 -->

                                                    <!-- column 2 -->
                                                    <table align="left" class="column">
                                                        <tbody><tr>
                                                            <td>

                                                                <h5 class="">Heb je fouten gevonden?</h5>
                                                                <p>Email: <strong><a href="emailto:dani@parkcraft.nl">dani@parkcraft.nl</a></strong></p>

                                                            </td>
                                                        </tr>
                                                    </tbody></table><!-- /column 2 -->

                                                    <span class="clear"></span>

                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </table>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                </table>

                </body>
                </html>';
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: noreply@parkcraft.nl' . "\r\n";
                mail($to, $subject, $htmlContent, $headers);
            }
        }
    }
    static function isPlayerFollowingAnyPark($mysqli, $uuid) {
        $sql = "SELECT * FROM pco_parks WHERE followers LIKE '%{$uuid}%' AND deleted='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function getTimeNow() {
        setlocale(LC_TIME, 'NL_nl');
        $time = strftime('%H:%M %e-%m-%Y',time());
        return $time;
    }
    static function setLastExcecution($mysqli) {
        $sesuuid = $_SESSION['UUID'];
        $sql = "UPDATE pco_users SET last_execution='".user::getTimeNow()."' WHERE UUID='$sesuuid'";
        $result = mysqli_query($mysqli, $sql);
    }
    static function getLastExcecution($mysqli) {
        $sesuuid = $_SESSION['UUID'];
        $sql = "SELECT * FROM pco_users WHERE UUID='$sesuuid';";
        $result = mysqli_query($mysqli, $sql);
        $row = mysqli_fetch_assoc($result);
        if(empty($row['last_execution'])) {
            return 'n.v.t.';
        }
        return $row['last_execution'];
    }
    static function setReceiveNewsEmails($mysqli, $value) {
        $sesuuid = $_SESSION['UUID'];
        $sql = "UPDATE pco_users SET news_email='$value' WHERE UUID='$sesuuid'";
        $result = mysqli_query($mysqli, $sql);
    }
    static function getReceiveNewsEmails($mysqli, $sesuuid) {
        $sql = "SELECT * FROM pco_users WHERE UUID='$sesuuid'";
        $result = mysqli_query($mysqli, $sql);
        $row = mysqli_fetch_assoc($result);
        if($row['news_email'] == 0) {
            return false;
        } else {
            return true;
        }
        return false;
    }
    static function sendActivationMailToAll($mysqli) {
        $sql="SELECT * FROM pco_users WHERE activated NOT IN ('1', 1);";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while ($row = mysqli_fetch_assoc($result)) {
            user::sendActivationMail($mysqli, $row['email'], user::getActivationCode($mysqli, $row['UUID']));
            echo $row['email'].'<br />';
        }
    }

    static function setReceiveReactionEmails($mysqli, $value) {
        $sesuuid = $_SESSION['UUID'];
        $sql = "UPDATE pco_users SET reaction_mail='$value' WHERE UUID='$sesuuid'";
        $result = mysqli_query($mysqli, $sql);
    }
    static function getReceiveReactionEmails($mysqli, $sesuuid) {
        $sql = "SELECT * FROM pco_users WHERE UUID='$sesuuid'";
        $result = mysqli_query($mysqli, $sql);
        $row = mysqli_fetch_assoc($result);
        if($row['reaction_mail'] == 0) {
            return false;
        } else {
            return true;
        }
        return false;
    }
}
class article {
    static function getParkID($mysqli, $id) {
        $sql = "SELECT * FROM pco_posts WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        return $row['park_id'];
    }
    static function isDeleted($mysqli, $postid) {
        $sql = "SELECT * FROM pco_posts WHERE ID='$postid' AND deleted='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        }
        return false;
    }
    static function deletepost($mysqli, $parkid, $postid, $userid) {
        if(park::CanEditSettings($mysqli, $parkid, $userid) || staff::canManagePosts($mysqli, $userid)) {
            $sql = "UPDATE pco_posts SET deleted='1' WHERE park_id='$parkid' AND ID='$postid'";
            $result = mysqli_query($mysqli, $sql);
        }
    }
    static function undeletepost($mysqli, $parkid, $postid, $userid) {
        if(park::CanEditSettings($mysqli, $parkid, $userid) || staff::canManagePosts($mysqli, $userid)) {
            $sql = "UPDATE pco_posts SET deleted='0' WHERE park_id='$parkid' AND ID='$postid'";
            $result = mysqli_query($mysqli, $sql);
        }
    }
    static function deletepoststaff($mysqli, $postid, $userid) {
        if(staff::canManagePosts($mysqli, $userid)) {
            $sql = "UPDATE pco_posts SET deleted='2' WHERE ID='$postid'";
            $result = mysqli_query($mysqli, $sql);
        }
    }
    static function undeletepoststaff($mysqli, $postid, $userid) {
        if(staff::canManagePosts($mysqli, $userid)) {
            $sql = "UPDATE pco_posts SET deleted='0' WHERE ID='$postid'";
            $result = mysqli_query($mysqli, $sql);
        }
    }
    static function post($mysqli, $parkid, $title, $body, $headerimg, $bodyimg)
    {
        setlocale(LC_TIME, 'NL_nl');
        if(strpos($headerimg, 'Invalid URL') !== false || strpos($headerimg, '') !== false) {
            $headerimg = park::getHeader($mysqli, $parkid);
        }
        if(strpos($bodyimg, 'Invalid URL') !== false || strpos($bodyimg, '') !== false) {
            $bodyimg = park::getHeader($mysqli, $parkid);
        }
        $title = strip_tags($title);
        $sql = "INSERT INTO pco_posts (park_id, post_title, post_body, post_header, post_images, post_poster, posted_on, post_likes) VALUES ('$parkid', '$title', '', '$headerimg', '', '" . $_SESSION['UUID'] . "', '" . strftime('%e-%m-%Y om %H:%M', time()) . "', '')";
        $result = mysqli_query($mysqli, $sql);

        $sql1 = "SELECT * FROM pco_posts WHERE park_id='$parkid' AND post_title='$title' order by ID desc";
        $result1 = mysqli_query($mysqli, $sql1);
        $row1 = mysqli_fetch_assoc($result1);
        $splitted = str_split($body, 1);
        for($i = 0; $i < count($splitted); $i++) {
            $sql3 = "SELECT * FROM pco_posts WHERE park_id='$parkid' AND post_title='$title' order by ID desc";
            $result3 = mysqli_query($mysqli, $sql3);
            $row3 = mysqli_fetch_assoc($result3);
            $oldbody = $row3['post_body'];

            $id = $row1['ID'];
            $newbody = $oldbody.$splitted[$i];
            $sql2 = "UPDATE pco_posts SET post_body = '$newbody' WHERE ID='$id'";
            $result2 = mysqli_query($mysqli, $sql2);
        }

        $sql1 = "SELECT * FROM pco_posts WHERE park_id='$parkid' AND post_title='$title' order by ID desc";
        $result1 = mysqli_query($mysqli, $sql1);
        $row1 = mysqli_fetch_assoc($result1);
        $splitted = str_split($bodyimg, 1);
        for($i = 0; $i < count($splitted); $i++) {
            $sql3 = "SELECT * FROM pco_posts WHERE park_id='$parkid' AND post_title='$title' order by ID desc";
            $result3 = mysqli_query($mysqli, $sql3);
            $row3 = mysqli_fetch_assoc($result3);
            $oldimgs = $row3['post_images'];

            $id = $row1['ID'];
            $newimgs = $oldimgs.$splitted[$i];
            $sql2 = "UPDATE pco_posts SET post_images = '$newimgs' WHERE ID='$id'";
            $result2 = mysqli_query($mysqli, $sql2);
        }
    }
    static function exist($mysqli, $id) {
        $sql = "SELECT * FROM pco_posts WHERE ID=$id AND deleted='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            $sql1 = "SELECT * FROM pco_posts WHERE ID=$id AND deleted='1' OR deleted='2'";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            if($count1 > 0) {
                if (staff::canManagePosts($mysqli, $_SESSION['UUID'])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
    static function loadArticlesPark($mysqli, $parkid) {
        $sql = "SELECT * FROM pco_posts WHERE park_id='$parkid' AND deleted='0' order by ID desc";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count == 0) {
            echo '<p>Dit park heeft nog geen artikelen gepost.</p>';
        }
        while($row = mysqli_fetch_assoc($result)) {
            $postid = $row['ID'];
            $title = $row['post_title'];
            if(!($row['post_poster'] == $_SESSION['UUID'])) {
                if(article::statusArticle($mysqli, $postid) == 0 || article::statusArticle($mysqli, $postid) == 2) {
                    continue;
                }
            } else {
                if(article::statusArticle($mysqli, $postid) == 0 || article::statusArticle($mysqli, $postid) == 2) {
                    $title = '<span class="label label-info">Wacht op bevestiging</span> ' . $title;
                }
            }

            $parkname = park::getName($mysqli, $row['park_id']);
            $logo = park::getLogo($mysqli, $row['park_id']);
            $post = common::random(20);
            $postheader = $row['post_header'];
            if(strpos($postheader, 'Invalid URL') !== false) {
                $postheader = park::getHeader($mysqli, $parkid);
            }
            $icon = '';
            if(article::isLiking($mysqli, $postid, $_SESSION['UUID'])) {
                $icon = 'favorite';
            } else {
                $icon = 'favorite_border';
            }
            $like = '';
            if(article::isLiking($mysqli, $postid, $_SESSION['UUID'])) {
                $like = 'unlike';
            } else {
                $like = 'like';
            }
            echo '
                        <div class="hover" id="' . $post . '">
                            <div>
                                <img class="avatar" src="' . $logo . '" alt=""/>
                                <a href="park.php?id=' . $row['park_id'] . '" style="color: black; font-weight: bold;"><span>' . $parkname . '</span></a>
                            </div>
                            <div>
                                <div>
                                    <img src="' . $postheader . '" alt="header" class="img-responsive center-block" style="max-height: 300px;"/>
                                </div>
                                <h3>' . $title . '</h3>
                            </div>
                            <script>
                                var id' . $post . ' = document.getElementById("' . $post . '");

                                id' . $post . '.onclick = function() {
                                    window.location.href = "article.php?id=' . $postid . '";
                                };
                            </script>
                            <span class="shortcut"><i class="material-icons heart"><a href="article.php?id='.$postid.'&'.$like.'" style="text-decoration: none;">'.$icon.'</a></i><span><a href="article.php?id='.$postid.'&likes" style="color: #000000; text-decoration: none;">'.article::countLikes($mysqli, $postid).'</a>
                            <span class="shortcut"><i class="material-icons">mode_comment</i><span>'.article::getReactionCount($mysqli, $postid).'</span></span>
                            <span class="shortcut"><i class="material-icons">remove_red_eye</i><span>'.statistics::getCountsArticles($mysqli, $postid).'</span></span>
                            <i style="float: right;">Geplaatst op: '.$row["posted_on"].'</i>
                        </div>
                        <hr />

                        ';
        }
    }
    static function loadArticles($mysqli) {
        $sql = "SELECT * FROM pco_posts WHERE deleted='0' AND reviewed='1' order by ID desc";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if(user::isPlayerFollowingAnyPark($mysqli, $_SESSION['UUID'])) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (user::IsFollowingPark($mysqli, $row['park_id'], $_SESSION['UUID']) || $row['park_id'] == 18 && !park::isDeleted($mysqli, $row['park_id'])) {
                    $title = $row['post_title'];
                    $postid = $row['ID'];
                    $parkname = park::getName($mysqli, $row['park_id']);
                    $logo = park::getLogo($mysqli, $row['park_id']);
                    $post = common::random(20);
                    $postheader = $row['post_header'];
                    if(strpos($postheader, 'Invalid URL') !== false) {
                        $postheader = park::getHeader($mysqli, $row['park_id']);
                    }
                    $icon = '';
                    if(article::isLiking($mysqli, $postid, $_SESSION['UUID'])) {
                        $icon = 'favorite';
                    } else {
                        $icon = 'favorite_border';
                    }
                    $like = '';
                    if(article::isLiking($mysqli, $postid, $_SESSION['UUID'])) {
                        $like = 'unlike';
                    } else {
                        $like = 'like';
                    }
                    echo '
                        <div class="hover" id="' . $post . '">
                            <div>
                                <img class="avatar" src="' . $logo . '" alt=""/>
                                <a href="park.php?id=' . $row['park_id'] . '" style="color: black; font-weight: bold;"><span>' . $parkname . '</span></a>
                            </div>
                            <div>
                                <div>
                                    <img src="' . $postheader . '" alt="header" class="img-responsive center-block" style="max-height: 300px;"/>
                                </div>
                                <h3>' . $title . '</h3>
                            </div>
                            <script>
                                var id' . $post . ' = document.getElementById("' . $post . '");

                                id' . $post . '.onclick = function() {
                                    window.location.href = "article.php?id=' . $postid . '";
                                };
                            </script>
                            <span class="shortcut"><i class="material-icons heart"><a href="article.php?id='.$postid.'&'.$like.'" style="text-decoration: none;">'.$icon.'</a></i><span><a href="article.php?id='.$postid.'&likes" style="color: #000000; text-decoration: none;">'.article::countLikes($mysqli, $postid).'</a>
                            <span class="shortcut"><i class="material-icons">mode_comment</i><span>'.article::getReactionCount($mysqli, $postid).'</span></span>
                            <span class="shortcut"><i class="material-icons">remove_red_eye</i><span>'.statistics::getCountsArticles($mysqli, $postid).'</span></span>
                            <i style="float: right;">Geplaatst op: '.$row["posted_on"].'</i>
                        </div>
                        <hr />

                        ';
                }
            }
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                if (!park::isDeleted($mysqli, $row['park_id'])) {
                    $title = $row['post_title'];
                    $postid = $row['ID'];
                    $parkname = park::getName($mysqli, $row['park_id']);
                    $logo = park::getLogo($mysqli, $row['park_id']);
                    $post = common::random(20);
                    $postheader = $row['post_header'];
                    if(strpos($postheader, 'Invalid URL') !== false) {
                        $postheader = park::getHeader($mysqli, $row['park_id']);
                    }
                    $icon = '';
                    if(article::isLiking($mysqli, $postid, $_SESSION['UUID'])) {
                        $icon = 'favorite';
                    } else {
                        $icon = 'favorite_border';
                    }
                    $like = '';
                    if(article::isLiking($mysqli, $postid, $_SESSION['UUID'])) {
                        $like = 'unlike';
                    } else {
                        $like = 'like';
                    }
                    echo '
                        <div class="hover">
                            <div>
                                <img class="avatar" src="' . $logo . '" alt=""/>
                                <a href="park.php?id=' . $row['park_id'] . '" style="color: black; font-weight: bold;"><span>' . $parkname . '</span></a>
                            </div>
                            <div class="hover" id="' . $post . '">
                                <div>
                                    <img src="' . $postheader . '" alt="header" class="img-responsive center-block" style="max-height: 300px;"/>
                                </div>
                                <h3>' . $title . '</h3>
                            </div>
                            <script>
                                var id' . $post . ' = document.getElementById("' . $post . '");

                                id' . $post . '.onclick = function() {
                                    window.location.href = "article.php?id=' . $postid . '";
                                };
                            </script>
                            <span class="shortcut"><i class="material-icons heart"><a href="article.php?id='.$postid.'&'.$like.'" style="text-decoration: none;">'.$icon.'</a></i><span><a href="article.php?id='.$postid.'&likes" style="color: #000000; text-decoration: none;">'.article::countLikes($mysqli, $postid).'</a>
                            <span class="shortcut"><i class="material-icons">mode_comment</i><span>'.article::getReactionCount($mysqli, $postid).'</span></span>
                            <span class="shortcut"><i class="material-icons">remove_red_eye</i><span>'.statistics::getCountsArticles($mysqli, $postid).'</span></span>
                            <i style="float: right;">Geplaatst op: '.$row["posted_on"].'</i>
                        </div>
                        <hr />

                        ';
                }
            }
        }
    }
    static function loadArticle($mysqli, $id) {
        $sql = "SELECT * FROM pco_posts WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        $title = $row['post_title'];
        $parkname = park::getName($mysqli, $row['park_id']);
        $logo = park::getLogo($mysqli, $row['park_id']);
        $body = $row['post_body'];
        $body = common::closetags(common::makeUrls($body));
        $body = str_replace("[enter]", "<br />", $body);
        $postimages = $row['post_images'];
        if(strpos($postimages, 'Invalid URL') !== false) {
            $postimages = park::getHeader($mysqli, $row['park_id']);
        }
        $url = "https://www.parkcraft.nl/article.php?id=$id";
        $icon = '';
        if(article::isLiking($mysqli, $id, $_SESSION['UUID'])) {
            $icon = 'favorite';
        } else {
            $icon = 'favorite_border';
        }
        $like = '';
        if(article::isLiking($mysqli, $id, $_SESSION['UUID'])) {
            $like = 'unlike';
        } else {
            $like = 'like';
        }

        echo '
            <div>
                <div>
                    <img class="avatar" src="'.$logo.'" alt=""/>
                    <a href="park.php?id='.$row['park_id'].'"><span style="style="color: black; font-weight: bold;">'.$parkname.'</span></a>
                </div>
                <div>';
        $imgs = explode(",", $postimages);
        $count = count($imgs)-1;
        if($count == 0 && strpos($postimages, ',') !== true) {
            echo '<img src="' . str_replace(',', '', $imgs[0]) . '" alt="' .$title. '" class="img-responsive center-block hover" id="headImg" style="max-height: 300px;">
	          <!-- The Modal -->
                     <div id="headModal" class="modal">
  			<span class="close">&times;</span>
 			<img class="modal-content" id="img01">
 			<div id="caption"></div>
                     </div>
		    
		    <script>
                    // Get the modal
                      var modal = document.getElementById(\'headModal\');

                   // Get the image and insert it inside the modal - use its "alt" text as a caption
                      var img = document.getElementById(\'headImg\');
                      var modalImg = document.getElementById("img01");
                      var captionText = document.getElementById("caption");
                      img.onclick = function(){
                          modal.style.display = "block";
                          modalImg.src = this.src;
                          captionText.innerHTML = this.alt;
                      }

                   // Get the <span> element that closes the modal
                      var span = document.getElementsByClassName("close")[0];

                   // When the user clicks on <span> (x), close the modal
                      span.onclick = function() { 
                           modal.style.display = "none";
                      }
                   </script>';
        } else {
            echo '<img src="' . str_replace(',', '', $postimages) . '" alt="' .$title. '" class="img-responsive center-block hover" id="headImg" style="max-height: 300px;">
	          <!-- The Modal -->
                     <div id="headModal" class="modal">
  			<span class="close">&times;</span>
 			<img class="modal-content" id="img01">
 			<div id="caption"></div>
                     </div>
		    
		    <script>
                    // Get the modal
                      var modal = document.getElementById(\'headModal\');

                   // Get the image and insert it inside the modal - use its "alt" text as a caption
                      var img = document.getElementById(\'headImg\');
                      var modalImg = document.getElementById("img01");
                      var captionText = document.getElementById("caption");
                      img.onclick = function(){
                          modal.style.display = "block";
                          modalImg.src = this.src;
                          captionText.innerHTML = this.alt;
                      }

                   // Get the <span> element that closes the modal
                      var span = document.getElementsByClassName("close")[0];

                   // When the user clicks on <span> (x), close the modal
                      span.onclick = function() { 
                           modal.style.display = "none";
                      }
                   </script>';
        }
        echo'</div>
                <h3>'.$title.'</h3>
                <span>'.$body.'</span>
                <hr />
                <i style="float: right;">Geplaatst op: '.$row["posted_on"].'</i>
                <span style="float: left;" class="shortcut"><i class="material-icons heart"><a href="?id='.$id.'&'.$like.'" style="text-decoration: none">'.$icon.'</a></i><span><a href="?id='.$id.'&likes" style="color: #000000; text-decoration: none;">'.article::countLikes($mysqli, $id).'</a></span></span>
                <span class="shortcut"><i class="material-icons">remove_red_eye</i><span>'.statistics::getCountsArticles($mysqli, $id).'</span></span><br /><br />
                <ul class="share-buttons">
                  <li><a href="https://www.facebook.com/sharer/sharer.php?u='.$url.'&t='.$title.'" title="Share on Facebook" target="_blank"><img alt="Share on Facebook" src="resources/svg/Facebook.svg"></a></li>
                  <li><a href="https://twitter.com/intent/tweet?source='.$url.'&text='.$title.' '.$url.'&via=parkencraft" target="_blank" title="Tweet"><img alt="Tweet" src="resources/svg/Twitter.svg"></a></li>
                  <li><a href="http://www.reddit.com/submit?url='.$url.'&title='.$title.'" target="_blank" title="Submit to Reddit"><img alt="Submit to Reddit" src="resources/svg/Reddit.svg"></a></li>

                </ul>
            </div>
        ';
    }
    static function acceptArticle($mysqli, $id) {
        $sql = "UPDATE pco_posts SET reviewed='1' WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $sql1 = "SELECT * FROM pco_posts WHERE ID='$id' AND reviewed='1'";
        $result1 = mysqli_query($mysqli, $sql1);
        $count1 = mysqli_num_rows($result1);
        if($count1 > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function rejectArticle($mysqli, $id) {
        $sql = "UPDATE pco_posts SET reviewed='2' WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $sql1 = "SELECT * FROM pco_posts WHERE ID='$id' AND reviewed='2'";
        $result1 = mysqli_query($mysqli, $sql1);
        $count1 = mysqli_num_rows($result1);
        if($count1 > 0) {
            return true;
        } else {
            return false;
        }
    }

    static function statusArticle($mysqli, $id) {
        $sql="SELECT * FROM pco_posts WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row["reviewed"];
        } else {
            return false;
        }
    }
    static function authorID($mysqli, $id) {
        $sql="SELECT * FROM pco_posts WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['post_poster'];
        } else {
            return false;
        }
    }
    static function loadReactions($mysqli, $id) {
        $sql = "SELECT * FROM pco_reaction WHERE article_id='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while($row = mysqli_fetch_assoc($result)) {
            $rowid = $row['ID'];
            if(park::IsUserOwner($mysqli, article::getParkID($mysqli, $id), $row['uuid'])) {
                echo '
                <img onclick="openUserPage('.user::getIDFromUUID($mysqli, $row['uuid']).')" class="avatar pull-left hover" src="'.userpage::getProfilePicture($mysqli, user::getIDFromUUID($mysqli, $row['uuid'])).'" alt="" style="display: block; margin: 0 auto; margin-right:5px;"/>
                <span class="label ' . user::getLabel($mysqli, user::getRankByUUID($mysqli, $row['uuid'])) . '">' . user::getPrefix($mysqli, user::getRankByUUID($mysqli, $row['uuid'])) . '</span>
                <span class="label label-primary">Eigenaar</span><span> <strong><a href="user.php?id='.user::getIDFromUUID($mysqli, $row["uuid"]).'" style="text-decoration: none; color: black;">' . user::getNameByUUID($mysqli, $row['uuid']) . '</a></strong></span>
                <p>' . $row['reaction'] . '</p>';
                if ($row['uuid'] == $_SESSION['UUID'] || staff::canManageComments($mysqli, $_SESSION['UUID'])) {
                    echo '<a href="?remove=' . $rowid . '&id=' . $id . '">Verwijder</a>';
                }
                echo '<hr />';
            } else if(park::IsUserStaff($mysqli, article::getParkID($mysqli, $id), $row['uuid'])) {
                echo '
                <img onclick="openUserPage('.user::getIDFromUUID($mysqli, $row['uuid']).')" class="avatar pull-left hover" src="'.userpage::getProfilePicture($mysqli, user::getIDFromUUID($mysqli, $row['uuid'])).'" alt="" style="display: block; margin: 0 auto; margin-right:5px;"/>
                <span class="label ' . user::getLabel($mysqli, user::getRankByUUID($mysqli, $row['uuid'])) . '">' . user::getPrefix($mysqli, user::getRankByUUID($mysqli, $row['uuid'])) . '</span>
                <span class="label label-primary">' . park::getStaffPrefix($mysqli, article::getParkID($mysqli, $id), $row['uuid']) . '</span><span> <strong><a href="user.php?id='.user::getIDFromUUID($mysqli, $row["uuid"]).'" style="text-decoration: none; color: black;">' . user::getNameByUUID($mysqli, $row['uuid']) . '</a></strong></span>
                <p>' . $row['reaction'] . '</p>';
                if ($row['uuid'] == $_SESSION['UUID'] || staff::canManageComments($mysqli, $_SESSION['UUID'])) {
                    echo '<a href="?remove=' . $rowid . '&id=' . $id . '">Verwijder</a>';
                }
                echo '<hr />';
            } else {
                echo '
                <img onclick="openUserPage('.user::getIDFromUUID($mysqli, $row['uuid']).')" class="avatar pull-left hover" src="'.userpage::getProfilePicture($mysqli, user::getIDFromUUID($mysqli, $row['uuid'])).'" alt="" style="display: block; margin: 0 auto; margin-right:5px;"/>
                <span class="label ' . user::getLabel($mysqli, user::getRankByUUID($mysqli, $row['uuid'])) . '">' . user::getPrefix($mysqli, user::getRankByUUID($mysqli, $row['uuid'])) . '</span><span> <strong><a href="user.php?id='.user::getIDFromUUID($mysqli, $row["uuid"]).'" style="text-decoration: none; color: black;">' . user::getNameByUUID($mysqli, $row['uuid']) . '</a></strong></span>
                <p>' . $row['reaction'] . '</p>';
                if ($row['uuid'] == $_SESSION['UUID'] || staff::canManageComments($mysqli, $_SESSION['UUID'])) {
                    echo '<a href="?remove=' . $rowid . '&id=' . $id . '">Verwijder</a>';
                }
                echo '<hr />';
            }
        }
        echo '<script>function openUserPage(user) {
                 window.open("https://parkcraft.nl/user.php?id="  + user,""_self")
             }</script>';
    }
    static function getReactionCount($mysqli, $id) {
        $sql = "SELECT * FROM pco_reaction WHERE article_id='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
    static function editArticle($mysqli, $parkid, $article_id, $title, $body, $userid) {
        if(park::CanWriteArticle($mysqli, $parkid, $userid) || staff::canManageParks($mysqli,  $userid)) {
            $sql = "UPDATE pco_posts SET post_title='$title', post_body='' WHERE park_id='$parkid' AND ID='$article_id'";
            $result = mysqli_query($mysqli, $sql);
            $body = preg_replace("/\r\n|\r/", "[enter]", $body);
            $splitted = str_split($body, 1);
            for($i = 0; $i < count($splitted); $i++) {
                $sql3 = "SELECT * FROM pco_posts WHERE park_id='$parkid' AND ID='$article_id' order by ID desc";
                $result3 = mysqli_query($mysqli, $sql3);
                $row3 = mysqli_fetch_assoc($result3);
                $oldbody = $row3['post_body'];

                $newbody = $oldbody.$splitted[$i];
                $sql2 = "UPDATE pco_posts SET post_body = '$newbody' WHERE ID='$article_id'";
                $result2 = mysqli_query($mysqli, $sql2);
            }
            return true;
        }
        return false;
    }
    static function PlaceReaction($mysqli, $id, $reaction) {
        $user = $_SESSION['UUID'];
        $reaction = strip_tags($reaction);
        $sql = "INSERT INTO pco_reaction (article_id, uuid, reaction) VALUES ('$id', '$user', '$reaction')";
        $result = mysqli_query($mysqli, $sql);
    }
    static function sendReactionEmail($mysqli, $id, $user, $reaction) {
        $email1 = '';

        $sql1 = "SELECT * FROM pco_posts WHERE ID='$id'";
        $result1 = mysqli_query($mysqli, $sql1);
        $count1 = mysqli_num_rows($result1);
        $row1 = mysqli_fetch_assoc($result1);
        $postuser = $row1['post_poster'];
        $postuseremail = user::getEmail($mysqli, $postuser);
        if (user::getReceiveReactionEmails($mysqli, $postuser) && strcmp($_SESSION['UUID'], $postuser) != 0 && strpos($email1, $postuseremail) === false) {
            $subject = 'Reactie op ' . article::getTitle($mysqli, $id);
            $body = 'Er is een reactie geplaatst op een artikel waar jij op hebt gereageerd:<br /><hr /><br />
                    <strong>' . user::getNameByUUID($mysqli, $postuser) . '</strong> <br /><br />' . $reaction . '<br /><hr/>';
            user::sendEmail($mysqli, $postuseremail, $subject, $body);
            $email1 .= $postuseremail;
        }


        $sql = "SELECT * FROM pco_reaction WHERE article_id='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while ($row = mysqli_fetch_assoc($result)) {
            $uuid = $row['uuid'];
            $email = user::getEmail($mysqli, $uuid);
            if (user::getReceiveReactionEmails($mysqli, $uuid) && strcmp($_SESSION['UUID'], $uuid) != 0 && strpos($email1, $email) === false) {
                $subject = 'Reactie op ' . article::getTitle($mysqli, $id);
                $body = 'Er is een reactie geplaatst op een artikel waar jij op hebt gereageerd:<br /><hr /><br />
                    <strong>' . user::getNameByUUID($mysqli, $user) . '</strong> <br /><br />' . $reaction . '<br /><hr/>';
                user::sendEmail($mysqli, $email, $subject, $body);
                $email1 .= $email;
            }
        }
        return $email1;
    }
    static function RemoveReaction($mysqli, $id) {
        $user = $_SESSION['UUID'];
        $sql = "SELECT * FROM pco_reaction WHERE ID='$id' AND uuid='$user'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0 || user::getRank($mysqli) > 2 ) {
            $sql = "DELETE FROM pco_reaction WHERE ID = $id";
            $result = mysqli_query($mysqli, $sql);
        }
    }
    static function getTitle($mysqli, $id) {
        $sql="SELECT * FROM pco_posts WHERE ID='$id' AND deleted NOT IN ('1', '2')";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['post_title'];
        } else {
            return 'Artikel verwijderd';
        }
    }
    static function loadAllReactions($mysqli, $pageid) {
        $pageusers = $pageid*50;
        $sql="SELECT * FROM pco_reaction ORDER BY ID DESC LIMIT $pageusers, 50";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            echo '
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Gebruiker</th>
                            <th>Artikel</th>
                            <th>Reactie</th>
                            <th>Opties</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                $name = user::getNameByUUID($mysqli, $row['uuid']);
                $artikel = $row['article_id'];
                $reactie = $row['reaction'];
                echo '<tr>';
                echo '<td>'.$name.'</td>';
                echo '<td><a href="article.php?id=' . $artikel . '">'.article::getTitle($mysqli, $row["article_id"]).'</a></td>';
                echo '<td><p style="word-wrap: break-word;">'.$reactie.'</p></td>';
                echo '<td><a href="staff.php?reactions=&id=' . $row['ID'] . '&pi=' . ($pageid + 1) . '&removereaction=" class="btn btn-danger btn-sm">Verwijder</a></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            $sql1="SELECT * FROM pco_reaction";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            if($count1 > ($pageid+1)*50) {
                if($pageid > 0) {
                    echo '<a href="staff.php?reactions=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                }
                echo '<a href="staff.php?reactions=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
            } else {
                if ($pageid > 0) {
                    echo '<a href="staff.php?reactions=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                    if ($count1 > ($pageid + 1) * 50) {
                        echo '<a href="staff.php?reactions=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
                    }
                }
            }
        } else {
            echo '<p>Geen reacties gevonden op deze pagina.</p>';
        }
    }
    static function loadAllPosts($mysqli, $pageid) {
        $pageposts = $pageid*50;
        $sql="SELECT * FROM pco_posts WHERE deleted NOT IN ('1', '2') AND reviewed NOT IN ('0', '2') ORDER BY ID DESC LIMIT $pageposts, 50";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            echo '
            <ul class="nav nav-pills nav-justified">
              <li class="active"><a href="staff.php?posts=&page=1">Actieve Posts</a></li>
              <li><a href="staff.php?reviewposts=&page=1">Review</a></li>
              <li><a href="staff.php?rejectedposts=&page=1">Afgewezen</a></li>
              <li><a href="staff.php?deletedposts=&page=1">Verwijderd</a></li>
            </ul>
            <hr>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Artikel</th>
                            <th>Park</th>
                            <th>Reacties</th>
                            <th>Opties</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['ID'];
                $parkid = $row['park_id'];
                $title = $row['post_title'];
                $deleted = $row['deleted'];
                echo '<tr>';
                echo '<td><a href="article.php?id=' . $id . '">'.$title.'</a></td>';
                echo '<td><a href="park.php?id='.$parkid.'">'.park::getName($mysqli, $parkid).'</a></td>';
                echo '<td><p style="word-wrap: break-word;">'.article::getReactionCount($mysqli, $id).'</p></td>';
                echo '<td><a href="staff.php?posts=' . $id . '&removepost=&pi='.($pageid + 1).'" class="btn btn-danger btn-sm">Verwijderen</a></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            $sql1="SELECT * FROM pco_reaction";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            if($count1 > ($pageid+1)*50) {
                if($pageid > 0) {
                    echo '<a href="staff.php?posts=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                }
                echo '<a href="staff.php?posts=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
            } else {
                if ($pageid > 0) {
                    echo '<a href="staff.php?posts=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                    if ($count1 > ($pageid + 1) * 50) {
                        echo '<a href="staff.php?posts=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
                    }
                }
            }
        } else {

            echo '          <ul class="nav nav-pills nav-justified">
                        <li class="active"><a href="staff.php?posts=&page=1">Actieve Posts</a></li>
                        <li><a href="staff.php?reviewposts=&page=1">Review</a></li>
                        <li><a href="staff.php?rejectedposts=&page=1">Afgewezen</a></li>
                        <li><a href="staff.php?deletedposts=&page=1">Verwijderd</a></li>
                      </ul>
                      <hr>
                      <p>Geen artikelen gevonden op deze pagina.</p>';
        }
    }
    static function loadReviewPosts($mysqli, $pageid) {
        $pageposts = $pageid*50;
        $sql="SELECT * FROM pco_posts WHERE deleted NOT IN ('1', '2') AND reviewed NOT IN ('1' , '2') ORDER BY ID DESC LIMIT $pageposts, 50";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            echo '
            <ul class="nav nav-pills nav-justified">
              <li><a href="staff.php?posts=&page=1">Actieve Posts</a></li>
              <li class="active"><a href="staff.php?reviewposts=&page=1">Review</a></li>
              <li><a href="staff.php?rejectedposts=&page=1">Afgewezen</a></li>
              <li><a href="staff.php?deletedposts=&page=1">Verwijderd</a></li>
            </ul>
            <hr>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Artikel</th>
                            <th>Park</th>
                            <th>Opties</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['ID'];
                $parkid = $row['park_id'];
                $title = $row['post_title'];
                echo '<tr>';
                echo '<td><a href="article.php?id=' . $id . '">'.$title.'</a></td>';
                echo '<td><a href="park.php?id='.$parkid.'">'.park::getName($mysqli, $parkid).'</a></td>';
                echo '<td><a href="article.php?id=' . $id . '" class="btn btn-info btn-sm">Bekijken</a></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            $sql1="SELECT * FROM pco_reaction";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            if($count1 > ($pageid+1)*50) {
                if($pageid > 0) {
                    echo '<a href="staff.php?reviewposts=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                }
                echo '<a href="staff.php?reviewposts=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
            } else {
                if ($pageid > 0) {
                    echo '<a href="staff.php?reviewposts=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                    if ($count1 > ($pageid + 1) * 50) {
                        echo '<a href="staff.php?reviewposts=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
                    }
                }
            }
        } else {
            echo '<ul class="nav nav-pills nav-justified">
              <li><a href="staff.php?posts=&page=1">Actieve Posts</a></li>
              <li class="active"><a href="staff.php?reviewposts=&page=1">Review</a></li>
              <li><a href="staff.php?rejectedposts=&page=1">Afgewezen</a></li>
              <li><a href="staff.php?deletedposts=&page=1">Verwijderd</a></li>
            </ul>
            <hr>
            <p>Geen artikelen gevonden op deze pagina.</p>';
        }
    }
    static function loadRejectedPosts($mysqli, $pageid) {
        $pageposts = $pageid*50;
        $sql="SELECT * FROM pco_posts WHERE deleted NOT IN ('1', '2') AND reviewed NOT IN ('0', '1') ORDER BY ID DESC LIMIT $pageposts, 50";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            echo '
            <ul class="nav nav-pills nav-justified">
              <li><a href="staff.php?posts=&page=1">Actieve Posts</a></li>
              <li><a href="staff.php?reviewposts=&page=1">Review</a></li>
              <li class="active"><a href="staff.php?rejectedposts=&page=1">Afgewezen</a></li>
              <li><a href="staff.php?deletedposts=&page=1">Verwijderd</a></li>
            </ul>
            <hr>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Artikel</th>
                            <th>Park</th>
                            <th>Opties</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['ID'];
                $parkid = $row['park_id'];
                $title = $row['post_title'];
                echo '<tr>';
                echo '<td><a href="article.php?id=' . $id . '">'.$title.'</a></td>';
                echo '<td><a href="park.php?id='.$parkid.'">'.park::getName($mysqli, $parkid).'</a></td>';
                echo '<td><a href="article.php?id=' . $id . '" class="btn btn-info btn-sm">Bekijken</a></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            $sql1="SELECT * FROM pco_reaction";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            if($count1 > ($pageid+1)*50) {
                if($pageid > 0) {
                    echo '<a href="staff.php?rejectedposts=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                }
                echo '<a href="staff.php?rejectedposts=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
            } else {
                if ($pageid > 0) {
                    echo '<a href="staff.php?rejectedposts=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                    if ($count1 > ($pageid + 1) * 50) {
                        echo '<a href="staff.php?rejectedposts=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
                    }
                }
            }
        } else {
            echo '<ul class="nav nav-pills nav-justified">
              <li><a href="/staff.php?posts=&page=1">Actieve Posts</a></li>
              <li><a href="/staff.php?reviewposts=&page=1">Review</a></li>
              <li class="active"><a href="/staff.php?rejectedposts=&page=1">Afgewezen</a></li>
              <li><a href="/staff.php?deletedposts=&page=1" class="active">Verwijderd</a></li>
            </ul>
            <hr>
            <p>Geen artikelen gevonden op deze pagina.</p>';
        }
    }
    static function loadDeletedPosts($mysqli, $pageid) {
        $pageposts = $pageid*50;
        $sql="SELECT * FROM pco_posts WHERE deleted='2' ORDER BY ID DESC LIMIT $pageposts, 50";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            echo '
            <ul class="nav nav-pills nav-justified">
              <li><a href="staff.php?posts=&page=1">Actieve Posts</a></li>
              <li><a href="staff.php?reviewposts=&page=1">Review</a></li>
              <li><a href="staff.php?rejectedposts=&page=1">Afgewezen</a></li>
              <li class="active"><a href="staff.php?deletedposts=&page=1">Verwijderd</a></li>
            </ul>
            <hr>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Artikel</th>
                            <th>Park</th>
							<th>Reacties</th>
                            <th>Opties</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['ID'];
                $parkid = $row['park_id'];
                $title = $row['post_title'];
                $deleted = $row['deleted'];
                echo '<tr>';
                echo '<td><a href="article.php?id=' . $id . '">'.$title.'</a></td>';
                echo '<td><a href="park.php?id='.$parkid.'">'.park::getName($mysqli, $parkid).'</a></td>';
                echo '<td><p style="word-wrap: break-word;">'.article::getReactionCount($mysqli, $id).'</p></td>';
                echo '<td><a href="staff.php?posts=' . $id . '&undoremovepost=&pi='.($pageid + 1).'" class="btn btn-info btn-sm">Verwijderen ongedaan maken</a></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            $sql1="SELECT * FROM pco_reaction";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            if($count1 > ($pageid+1)*50) {
                if($pageid > 0) {
                    echo '<a href="staff.php?deletedposts=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                }
                echo '<a href="staff.php?deletedposts=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
            } else {
                if ($pageid > 0) {
                    echo '<a href="staff.php?deletedposts=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                    if ($count1 > ($pageid + 1) * 50) {
                        echo '<a href="staff.php?deletedposts=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
                    }
                }
            }
        } else {
            echo '<ul class="nav nav-pills nav-justified">
              <li><a href="staff.php?posts=&page=1">Actieve Posts</a></li>
              <li><a href="staff.php?reviewposts=&page=1">Review</a></li>
              <li><a href="staff.php?rejectedposts=&page=1">Afgewezen</a></li>
              <li class="active"><a href="staff.php?deletedposts=&page=1" class="active">Verwijderd</a></li>
            </ul>
            <hr>
            <p>Geen artikelen gevonden op deze pagina.</p>';
        }
    }
    static function like($mysqli, $articleid, $uuid) {
        if(!article::isLiking($mysqli, $articleid, $uuid)) {
            $sql="UPDATE pco_posts SET post_likes = CONCAT(post_likes,'".$uuid.",') WHERE ID='$articleid';";
            $result=mysqli_query($mysqli, $sql);
        }
    }
    static function unlike($mysqli, $articleid, $uuid) {
        if(article::isLiking($mysqli, $articleid, $uuid)) {
            $sql = "UPDATE pco_posts SET post_likes = REPLACE(post_likes,'" . $uuid . ",','') WHERE ID='$articleid';";
            $result = mysqli_query($mysqli, $sql);
        }
    }
    static function isLiking($mysqli, $articleid, $uuid) {
        $sql = "SELECT * FROM pco_posts WHERE ID='$articleid' AND post_likes LIKE '%{$uuid}%'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function countLikes($mysqli, $articleid) {
        $sql="SELECT * FROM pco_posts WHERE ID='$articleid'";
        $result=mysqli_query($mysqli,$sql);
        $count=mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        $likes = explode(",", $row['post_likes']);
        return (count($likes)-1);
    }
    static function loadReview($mysqli, $articleid) {
        $sql="SELECT * FROM pco_posts WHERE ID='$articleid'";
        $result=mysqli_query($mysqli,$sql);
        $count=mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        $reviewed = explode(",", $row['reviewed']);
        return $reviewed;
    }
    static function LoadLikes($mysqli, $postid) {
        $sql="SELECT * FROM pco_posts WHERE ID='$postid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            $row = mysqli_fetch_assoc($result);
            $followers = explode(",", $row['post_likes']);
            for($i = 0; $i < (count($followers)-1); $i++) {
                echo '
            <div>
                <span><span class="label '.user::getLabel($mysqli, user::getRankByUUID($mysqli, str_replace(",", "", $followers[$i]))).'">'.user::getPrefix($mysqli, user::getRankByUUID($mysqli, str_replace(",", "", $followers[$i]))).'</span> <strong><a href="user.php?id='.user::getIDFromUUID($mysqli, str_replace(",", "", $followers[$i])).'" style="text-decoration: none; color: black;"><span>'.user::getNameByUUID($mysqli, str_replace(",", "", $followers[$i])).'</strong></span></a></span>
                <hr />
            </div>
        ';
            }
        } else {
            echo '<p>Geen likes.</p>';
        }
    }
}
class park {
    static function getOwnerEmail($mysqli, $id) {
        $sql = "SELECT owner FROM pco_parks WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $row = mysqli_fetch_assoc($result);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            $uuid = $row['owner'];
            $sql1 = "SELECT email FROM pco_users WHERE UUID='$uuid'";
            $result1 = mysqli_query($mysqli, $sql1);
            $row1 = mysqli_fetch_assoc($result1);
            $count1 = mysqli_num_rows($result1);
            if($count1 > 0) {
                return $row1['email'];
            }
            return 'error';
        }
        return 'error';
    }
    static function getUUIDFromStaff($mysqli, $id) {
        $sql="SELECT * FROM pco_parks_staff WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['uuid'];
        }
    }
    static function removestaff($mysqli, $parkid, $id) {
        $sql = "DELETE FROM pco_parks_staff WHERE ID='$id' AND park_id='$parkid'";
        $result = mysqli_query($mysqli, $sql);
    }
    static function addstaff($mysqli, $parkid, $email) {
        $email = strip_tags($email);
        if(!user::exist($mysqli, $email)) {
            return false;
            exit;
        }
        if(park::IsUserStaff($mysqli, $parkid, user::getUUIDFromEmail($mysqli, $email)) || park::IsUserOwner($mysqli, $parkid, user::getUUIDFromEmail($mysqli, $email))) {
            return true;
        }
        $useremail = user::getUUIDFromEmail($mysqli,$email);
        $sql = "INSERT INTO pco_parks_staff (park_id, uuid) VALUES ('$parkid', '$useremail');";
        $result = mysqli_query($mysqli, $sql);
        return true;
    }
    static function changeOwner($mysqli, $parkid, $email) {
        $email = strip_tags($email);
        if(!user::exist($mysqli, $email)) {
            return false;
        }
        if(!park::IsUserOwner($mysqli, $parkid, $_SESSION['UUID']) && !staff::canManageParks($mysqli, $_SESSION['UUID'])) {
            return false;
        }
        if(park::IsUserOwner($mysqli, $parkid, user::getUUIDFromEmail($mysqli, $email))) {
            return false;
        }
        $useremail = user::getUUIDFromEmail($mysqli, $email);
        $sql = "UPDATE pco_parks SET owner='$useremail' WHERE ID='$parkid';";
        $result = mysqli_query($mysqli, $sql);

        $sql = "DELETE FROM pco_parks_staff WHERE UUID='$useremail' AND park_id='$parkid'";
        $result = mysqli_query($mysqli, $sql);
        return true;
    }
    static function editstaff($mysqli, $parkid, $useruuid, $cw, $ces, $cms, $cmr, $cmj, $prefix) {
        $prefix = strip_tags($prefix);
        $sql = "UPDATE pco_parks_staff SET prefix='$prefix', can_write='$cw', can_edit_settings='$ces', can_manage_staff='$cms', can_manage_rides='$cmr', can_manage_jobs='$cmj' WHERE uuid='$useruuid' AND park_id='$parkid'";
        $result = mysqli_query($mysqli, $sql);
    }
    static function getStaffPrefix($mysqli, $parkid, $uuid) {
        $sql="SELECT * FROM pco_parks_staff WHERE park_id='$parkid' AND uuid='$uuid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        return $row['prefix'];
    }
    static function loadstaff($mysqli, $parkid) {
        $sql="SELECT * FROM pco_parks_staff WHERE park_id='$parkid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            echo '
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Prefix</th>
                            <th>Opties</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>'.user::getNameByUUID($mysqli, $row["uuid"]).'</td>';
                echo '<td>'.$row["prefix"].'</td>';
                echo '<td><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#'.$row["ID"].'">Edit</button><a href="parksettings.php?id='.$parkid.'&removestaff='.$row["ID"].'&editstaff" class="btn btn-danger btn-sm">Verwijderen</a></td>';
                echo '
                        <div id="'.$row["ID"].'" class="modal fade" role="dialog">
                          <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Staflid wijzigen</h4>
                              </div>
                              <div class="modal-body">
                                <form name="edit" id="register" action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="post" autocomplete="off" class="form-horizontal">
                                    <p>'.user::getNameByUUID($mysqli, $row["uuid"]).'</p>
                                    <span>Mag deze persoon artikelen schrijven?</span>
                                    <select name="cw" class="form-control">
                                      <option value="1" '; if($row["can_write"] == 1) { echo "selected"; } echo '>Ja</option>
                                      <option value="0" '; if($row["can_write"] == 0) { echo "selected"; } echo '>Nee</option>
                                    </select><br />
                                    <span>Mag deze persoon instellingen veranderen?</span>
                                    <select name="ces" class="form-control">
                                      <option value="1" '; if($row["can_edit_settings"] == 1) { echo "selected"; } echo '>Ja</option>
                                      <option value="0" '; if($row["can_edit_settings"] == 0) { echo "selected"; } echo '>Nee</option>
                                    </select><br />
                                    <span>Mag deze persoon staf beheren?</span>
                                    <select name="cms" class="form-control">
                                      <option value="1" '; if($row["can_manage_staff"] == 1) { echo "selected"; } echo '>Ja</option>
                                      <option value="0" '; if($row["can_manage_staff"] == 0) { echo "selected"; } echo '>Nee</option>
                                    </select><br />
                                    <span>Mag deze persoon attracties beheren?</span>
                                    <select name="cmr" class="form-control">
                                      <option value="1" '; if($row["can_manage_rides"] == 1) { echo "selected"; } echo '>Ja</option>
                                      <option value="0" '; if($row["can_manage_rides"] == 0) { echo "selected"; } echo '>Nee</option>
                                    </select><br />
                                    <span>Mag deze persoon vacatures beheren?</span>
                                    <select name="cmj" class="form-control">
                                      <option value="1" '; if($row["can_manage_jobs"] == 1) { echo "selected"; } echo '>Ja</option>
                                      <option value="0" '; if($row["can_manage_jobs"] == 0) { echo "selected"; } echo '>Nee</option>
                                    </select><br />
                                    <input type="hidden" value="'.$parkid.'" name="id"/>
                                    <input type="hidden" value="'.$row["uuid"].'" name="useruuid"/>
                                    <br />
                                    <span>Welke rank heeft deze persoon?</span><input type="text" name="prefix" value="'.$row["prefix"].'" class="form-control" maxlength="20"/><br />
                                    <button type="submit" class="btn btn-raised btn-success" name="edit" id="postbutton">Opslaan
                                                </button>
                                </form>
                              </div>
                            </div>

                          </div>
                        </div>
                        ';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>Er zijn momenteel geen staffleden</p>';
        }
        echo '
                      <button type="button" class="btn-info" data-toggle="modal" data-target="#addstaff">Stafflid toevoegen</button>
                       <div id="addstaff" class="modal fade" role="dialog">
                          <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Stafflid toevoegen</h4>
                              </div>
                              <div class="modal-body">
                                <form name="edit" id="addstaff" action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="post" autocomplete="off" class="form-horizontal">
                                    <span>Wat is het email van deze persoon?<br /> <span class="text-danger">(Let op: deze persoon moet geregistreerd zijn op ParkCraft!)</span></span><input type="email" name="email" value=""  class="form-control"/><br />
                                    <input type="hidden" value="'.$parkid.'" name="id"/>
                                    <button type="submit" class="btn btn-raised btn-success" name="addstaff" id="postbutton">Toevoegen
                                  </button>
                                </form>
                              </div>
                            </div>

                          </div>
                        </div>
                ';
    }
    static function loadarticles($mysqli, $parkid) {
        $sql="SELECT * FROM pco_posts WHERE park_id='$parkid' AND deleted <> 2";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            echo '
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Gepost door</th>
                        <th>Gepost op</th>
                        <th>Opties</th>
                    </tr>
                </thead>';
            echo '<tbody>';
            while($row = mysqli_fetch_assoc($result)) {
                $deleted = $row['deleted'];
                echo '<tr>';
                echo '<td>'.$row["post_title"].'</td>';
                echo '<td>'.user::getNameByUUID($mysqli, $row["post_poster"]).'</td>';
                echo '<td>'.$row["posted_on"].'</td>';
                echo '<td><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#'.$row["ID"].'">Edit</button>';
                if($deleted == 0) {
                    echo '<a href="parksettings.php?id='.$parkid.'&removearticle='.$row["ID"].'&postedit" class="btn btn-danger btn-sm">Verwijderen</a></td>';
                } else {
                    echo '<a href="parksettings.php?id='.$parkid.'&undoremovearticle='.$row["ID"].'&postedit" class="btn btn-danger btn-sm">Verwijderen ongedaan maken</a></td>';
                }
                echo '
                    <div id="'.$row["ID"].'" class="modal fade" role="dialog">
                      <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Artikel wijzigen</h4>
                          </div>
                          <div class="modal-body">
                            <form name="editpost" id="editpost" action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="post" autocomplete="off" class="form-horizontal">
                                <span>Titel</span><br /><input type="text" name="titel" id="titel" class="form-control" value="'.$row["post_title"].'"/><br/>
                                <input type="hidden" name="postid" id="postid" value="'.$row["ID"].'"/>
                                <input type="hidden" name="id" id="id" value="'.$row["park_id"].'"/>
                                <span>Artikel</span><br /><textarea type="text" class="form-control" name="article" id="article" placeholder="Typ hier het artikel" value="" rows="10" required>'.str_replace("[enter]", "\n", $row["post_body"]).'</textarea>
                                <button type="submit" class="btn btn-raised btn-success" name="editpostbutton" id="editpostbutton">Opslaan
                                            </button>
                            </form>
                          </div>
                        </div>

                      </div>
                    </div>
                    ';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>Er zijn momenteel geen artikelen</p>';
        }
        echo '
                  <a type="button" class="btn-info" href="writearticle.php?id='.$parkid.'">Artikel maken</a>
            ';
    }
    static function updatesettings($mysqli, $id, $logo, $header, $naam, $desc, $ip, $email, $background) {
        $logosql = '';
        if(!$logo == '') {
            $logosql = "logo='$logo',";
        }
        $headersql = '';
        if(!$header == '') {
            $headersql = "header='$header',";
        }
        $backgroundsql = '';
        if(!$background == '') {
            $backgroundsql = ",background='$background'";
        }
        $ip = strip_tags($ip);
        $ip = trim($ip);
        $ip = mysqli_real_escape_string($mysqli, $ip);

        $email = strip_tags($email);
        $email = trim($email);
        $email = mysqli_real_escape_string($mysqli, $email);

        $desc = strip_tags($desc);
        $desc = trim($desc);
        $desc = mysqli_real_escape_string($mysqli, $desc);

        $naam = strip_tags($naam);
        $naam = trim($naam);
        $naam = mysqli_real_escape_string($mysqli, $naam);
        $sql = "UPDATE pco_parks SET ".$logosql." ".$headersql." name='$naam', description='$desc', ip='$ip', email='$email'".$backgroundsql." WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        return $result;
    }
    static function getParkRequestRequester($mysqli, $id) {
        $sql = "SELECT * FROM pco_parkrequest order by ID desc";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            $parkowner = $row['requester'];
            return $parkowner;
        }
    }
    static function getParkRequestName($mysqli, $id) {
        $sql = "SELECT * FROM pco_parkrequest order by ID desc";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            $parkname = $row['name'];
            return $parkname;
        }
    }
    static function refuserequest($mysqli, $id) {
        $body = '
            Uw parkaanvraag voor het park <strong>'.park::getParkRequestName($mysqli, $id).'</strong> is gewijgerd. U kunt een park opnieuw aanvragen onder "Parken > Park Aanvragen".';
        user::sendEmail($mysqli, user::getEmail($mysqli, park::getParkRequestRequester($mysqli, $id)), "Park aanvraag", $body);

        $sql = "UPDATE pco_parkrequest SET rejected='1' WHERE ID = $id";
        $result = mysqli_query($mysqli, $sql);
    }
    static function acceptrequest($mysqli, $id) {
        $sql = "SELECT * FROM pco_parkrequest WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            $parkname = $row['name'];
            $parkip = $row['ip'];
            $parkemail = $row['email'];
            $parkowner = $row['requester'];
            $apikey = API::generateAPIKey($mysqli);
            $sql1 = "INSERT INTO pco_parks (name, description, ip, email, owner, followers, APIKey) VALUES ('$parkname', '', '$parkip', '$parkemail', '$parkowner', '$parkowner,', '$apikey')";
            $result1 = mysqli_query($mysqli, $sql1);

            $sql = "UPDATE pco_parkrequest SET rejected='2' WHERE ID = $id";
            $result = mysqli_query($mysqli, $sql);
            $body = '
            Uw parkaanvraag voor het park <strong>'.$parkname.'</strong> is geaccepteerd. U kunt dit park nu beheren via de navigatiebalk onder "Parken".';
            user::sendEmail($mysqli, user::getEmail($mysqli, $parkowner), "Park aanvraag", $body);
        }

    }
    static function loadrequests($mysqli) {
        $sql="SELECT * FROM pco_parkrequest WHERE rejected='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            echo '
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Ip</th>
                        <th>Twitter</th>
                        <th>Email</th>
                        <th>Aanvrager</th>
                        <th>Aanvraag</th>
                    </tr>
                </thead>
                <tbody>';
            while($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>'.$row["name"].'</td>';
                echo '<td>'.$row["ip"].'</td>';
                echo '<td>'.$row["twitter"].'</td>';
                echo '<td>'.$row["email"].'</td>';
                echo '<td><a href="user.php?id='.user::getIDFromUUID($mysqli, $row["requester"]).'">'.user::getNameByUUID($mysqli, $row["requester"]).'</a></td>';
                echo '<td><a href="staff.php?parkrequest=&refuse='.$row['ID'].'" class="btn-danger">Weigeren</a><br/><a href="staff.php?parkrequest=&accept='.$row['ID'].'" class="btn-success">Accepteren</a></td>';
                echo '</tr>';
            }
            echo '</tbody>
            </table>';
            $sql1="SELECT * FROM pco_parks";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
        } else {
            echo '<p>Er zijn momenteel geen aanvragen</p>';
        }
    }
    static function request($mysqli, $name, $ip, $twitter, $email) {
        $name = strip_tags($name);
        $ip = strip_tags($ip);
        $twitter = strip_tags($twitter);
        $email = strip_tags($email);
        $sql = "INSERT INTO pco_parkrequest (name, ip, twitter, email, requester) VALUES ('$name', '$ip', '$twitter', '$email', '".$_SESSION['UUID']."');";
        $result = mysqli_query($mysqli, $sql);
    }
    static function getName($mysqli, $id) {
        $sql="SELECT * FROM pco_parks WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['name'];
        }
    }
    static function getBackrgound($mysqli, $id) {
        $sql="SELECT * FROM pco_parks WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0 && strcmp($row['background'], "") != 0) {
            return $row['background'];
        } else {
            $tijd = date("G");
            if ($tijd < 6) {
                $sql="SELECT * FROM pco_backgrounds WHERE tijd='0' ORDER BY RAND() LIMIT 1";
                $result = mysqli_query($mysqli, $sql);
                $count = mysqli_num_rows($result);
                $row = mysqli_fetch_assoc($result);
                echo '/resources/backgrounds/'.$row["background"];
            } elseif ($tijd < 12) {
                $sql="SELECT * FROM pco_backgrounds WHERE tijd='1' ORDER BY RAND() LIMIT 1";
                $result = mysqli_query($mysqli, $sql);
                $count = mysqli_num_rows($result);
                $row = mysqli_fetch_assoc($result);
                echo '/resources/backgrounds/'.$row["background"];
            } elseif ($tijd < 18) {
                $sql="SELECT * FROM pco_backgrounds WHERE tijd='2' ORDER BY RAND() LIMIT 1";
                $result = mysqli_query($mysqli, $sql);
                $count = mysqli_num_rows($result);
                $row = mysqli_fetch_assoc($result);
                echo '/resources/backgrounds/'.$row["background"];
            } else {
                $sql="SELECT * FROM pco_backgrounds WHERE tijd='3' ORDER BY RAND() LIMIT 1";
                $result = mysqli_query($mysqli, $sql);
                $count = mysqli_num_rows($result);
                $row = mysqli_fetch_assoc($result);
                echo '/resources/backgrounds/'.$row["background"];
            }
        }

    }
    static function getLogo($mysqli, $id) {
        $sql="SELECT * FROM pco_parks WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            if(empty($row['logo'])) {
                return 'resources/defaultavatar.png';
            }
            return $row['logo'];
        }
    }
    static function getHeader($mysqli, $id) {
        $sql="SELECT * FROM pco_parks WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            if(empty($row['header'])) {
                return 'resources/header.jpg';
            }
            return $row['header'];
        }
    }
    static function getDescription($mysqli, $id) {
        $sql="SELECT * FROM pco_parks WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['description'];
        }
    }
    static function getIp($mysqli, $id) {
        $sql="SELECT * FROM pco_parks WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['ip'];
        }
    }
    static function isDeleted($mysqli, $id) {
        $sql="SELECT * FROM pco_parks WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($row['deleted'] == 0) {
            return false;
        } else {
            return true;
        }
    }
    static function getEmail($mysqli, $id) {
        $sql="SELECT * FROM pco_parks WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['email'];
        }
    }
    static function IsUserStaff($mysqli, $parkid, $userid) {
        $sql="SELECT * FROM pco_parks WHERE ID='$parkid' AND owner LIKE '%{$userid}%'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return true;
        } else {
            $sql1="SELECT * FROM pco_parks_staff WHERE park_id='$parkid' AND uuid='$userid'";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            $row1 = mysqli_fetch_assoc($result1);
            if($count1 > 0) {
                return true;
            } else {
                return false;
            }
            return false;
        }
    }
    static function IsUserOwner($mysqli, $parkid, $userid) {
        $sql="SELECT * FROM pco_parks WHERE ID='$parkid' AND owner LIKE '%{$userid}%'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function CanEditSettings($mysqli, $parkid, $userid) {
        $sql="SELECT * FROM pco_parks WHERE ID='$parkid' AND owner LIKE '%{$userid}%'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return true;
        } else {
            $sql1="SELECT * FROM pco_parks_staff WHERE park_id='$parkid' AND uuid='$userid'";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            $row1 = mysqli_fetch_assoc($result1);
            if ($count1 > 0) {
                if ($row1['can_edit_settings'] == 0) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }
    static function CanWriteArticle($mysqli, $parkid, $userid) {
        $sql="SELECT * FROM pco_parks WHERE ID='$parkid' AND owner LIKE '%{$userid}%'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return true;
        } else {
            $sql1="SELECT * FROM pco_parks_staff WHERE park_id='$parkid' AND uuid='$userid'";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            $row1 = mysqli_fetch_assoc($result1);
            if ($count1 > 0) {
                if ($row1['can_write'] == 0) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }
    static function CanManageStaff($mysqli, $parkid, $userid) {
        $sql="SELECT * FROM pco_parks WHERE ID='$parkid' AND owner LIKE '%{$userid}%'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return true;
        } else {
            $sql1="SELECT * FROM pco_parks_staff WHERE park_id='$parkid' AND uuid='$userid'";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            $row1 = mysqli_fetch_assoc($result1);
            if ($count1 > 0) {
                if ($row1['can_manage_staff'] == 0) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }
    static function CanManageRides($mysqli, $parkid, $userid) {
        $sql="SELECT * FROM pco_parks WHERE ID='$parkid' AND owner LIKE '%{$userid}%'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return true;
        } else {
            $sql1="SELECT * FROM pco_parks_staff WHERE park_id='$parkid' AND uuid='$userid'";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            $row1 = mysqli_fetch_assoc($result1);
            if ($count1 > 0) {
                if ($row1['can_manage_rides'] == 0) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }
    static function CanManageJobs($mysqli, $parkid, $userid) {
        $sql="SELECT * FROM pco_parks WHERE ID='$parkid' AND owner LIKE '%{$userid}%'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return true;
        } else {
            $sql1="SELECT * FROM pco_parks_staff WHERE park_id='$parkid' AND uuid='$userid'";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            $row1 = mysqli_fetch_assoc($result1);
            if ($count1 > 0) {
                if ($row1['can_manage_jobs'] == 0) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }
    static function unfollow($mysqli, $parkid, $userid) {
        if(!park::IsUserStaff($mysqli, $parkid, $userid) || !park::IsUserOwner($mysqli, $parkid, $userid)) {
            $sql = "UPDATE pco_parks SET followers = REPLACE(followers,'" . $userid . ",','') WHERE ID='$parkid';";
            $result = mysqli_query($mysqli, $sql);
        }
    }
    static function follow($mysqli, $parkid, $userid) {
        $sql="UPDATE pco_parks SET followers = CONCAT(followers,'".$userid.",') WHERE ID='$parkid';";
        $result=mysqli_query($mysqli, $sql);
    }
    static function loadWhoToFollow($mysqli, $userid) {
        $sql="SELECT * FROM pco_parks WHERE followers NOT LIKE '%{$userid}%' AND deleted='0' ORDER BY RAND() LIMIT 5 ";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while($row = mysqli_fetch_assoc($result)) {
            $logo = $row['logo'];
            if(empty($row['logo'])) {
                $logo = 'resources/defaultavatar.png';
            }
            if(!user::IsFollowingPark($mysqli, $row['ID'], $userid) && !park::isDeleted($mysqli, $row['ID'])) {
                echo '<img class="avatar" src="'.$logo.'" alt=""/><a href="park.php?id='.$row['ID'].'" style="color: black; font-weight: bold;"><span>'.$row['name'].'</span></a><hr />';
            }
        }
    }
    static function exist($mysqli, $parkid) {
        $sql="SELECT * FROM pco_parks WHERE ID='$parkid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function getFollowers($mysqli, $id) {
        if ($id == 18) {
            $sql = "SELECT * FROM pco_users";
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);
            return ($count);
        } else {
            $sql = "SELECT * FROM pco_parks WHERE ID='$id'";
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);
            $followers = explode(",", $row['followers']);
            return (count($followers) - 1);
        }
    }
    static function LoadParksSearch($mysqli, $pageid, $keyword)
    {
        $pageparks = $pageid*50;
        $sql="SELECT * FROM pco_parks WHERE name LIKE '%{$keyword}%' LIMIT $pageparks, 50";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            if($count > 50) {
                echo '<p>Geef een specefiekere zoekopdracht!</p>';
                exit;
            }
            echo '
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Naam</th>
                            <th>Eigenaar</th>
                            <th>Opties</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                $logo = $row['logo'];
                if(empty($row['logo'])) {
                    $logo = 'resources/defaultavatar.png';
                }
                $name = $row['name'];
                $owner = user::getNameByUUID($mysqli, $row['owner']);
                $parkid = $row['ID'];
                echo '<tr>';
                echo '<td><img src="'.$logo.'" alt="" class="avatar"/></td>';
                echo '<td><a href="park.php?id=' . $parkid . '" class="">'.$name.'</a></td>';
                echo '<td>'.$owner.'</td>';
                if(park::isDeleted($mysqli, $parkid)) {
                    echo '<td><a href="staff.php?parks=' . $parkid . '&undoremove=&pi='.($pageid + 1).'" class="btn-info">Verwijderen ongedaan maken</a></td>';
                } else {
                    echo '<td><a href="staff.php?parks=' . $parkid . '&remove=&pi='.($pageid + 1).'" class="btn-danger">Verwijderen</a><br /><a href="parksettings.php?id='.$parkid.'" class="btn-info">Beheren</a></td>';
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>Geen parken op deze pagina.</p>';
        }
    }
    static function LoadParks($mysqli, $pageid)
    {
        $pageparks = $pageid*50;
        $sql="SELECT * FROM pco_parks LIMIT $pageparks, 50";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            echo '
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Naam</th>
                            <th>Eigenaar</th>
                            <th>Opties</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                $logo = $row['logo'];
                if(empty($row['logo'])) {
                    $logo = 'resources/defaultavatar.png';
                }
                $name = $row['name'];
                $owner = user::getNameByUUID($mysqli, $row['owner']);
                $parkid = $row['ID'];
                echo '<tr>';
                echo '<td><img src="'.$logo.'" alt="" class="avatar"/></td>';
                echo '<td><a href="park.php?id=' . $parkid . '" class="">'.$name.'</a></td>';
                echo '<td>'.$owner.'</td>';
                if(park::isDeleted($mysqli, $parkid)) {
                    echo '<td><a href="staff.php?parks=' . $parkid . '&undoremove=&pi='.($pageid + 1).'" class="btn-info">Verwijderen ongedaan maken</a></td>';
                } else {
                    echo '<td><a href="staff.php?parks=' . $parkid . '&remove=&pi='.($pageid + 1).'" class="btn-danger">Verwijderen</a><br /><a href="parksettings.php?id='.$parkid.'" class="btn-info">Beheren</a></td>';
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            $sql1="SELECT * FROM pco_parks";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            if($count1 > ($pageid+1)*50) {
                if($pageid > 0) {
                    echo '<a href="staff.php?parks=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                }
                echo '<a href="staff.php?parks=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
            } else {
                if ($pageid > 0) {
                    echo '<a href="staff.php?parks=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                    if ($count1 > ($pageid + 1) * 50) {
                        echo '<a href="staff.php?parks=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
                    }
                }
            }
        } else {
            echo '<p>Geen parken op deze pagina.</p>';
        }
    }
    static function LoadParksList($mysqli) {
        $randomrow = array(
            "ID",
            "name",
            "description",
            "ip",
            "email",
            "followers",
            "logo",
            "header",
            "owner",
            "background"
        );
        $randomorder = array(
            "asc",
            "desc"
        );
        $ranrow = $randomrow[array_rand($randomrow,1)];
        $ranorder = $randomorder[array_rand($randomorder,1)];
        $sql="SELECT * FROM pco_parks WHERE NOT ID='18' AND deleted='0' order by ".$ranrow." ".$ranorder;
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            echo '
                <table class="table table-hover sortable" id="parklist">
                    <thead>
                        <tr>
                            <th class="hover">Park</th>
                            <th class="hover">IP</th>
                            <th class="hover">Volgers</th>
                            <th class="hover">Status</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                $logo = $row['logo'];
                if(empty($row['logo'])) {
                    $logo = 'resources/defaultavatar.png';
                }
                $name = $row['name'];
                $ip = $row['ip'];
                $parkid = $row['ID'];
                $followers = park::getFollowers($mysqli, $parkid);
                $id = common::random(10);


                echo '<tr>';
                echo '<td><img src="'.$logo.'" alt="" class="avatar"/> <a href="park.php?id=' . $parkid . '" class="">'.$name.'</a></td>';
                echo '<td>'.$ip.'</td>';
                echo '<td>'.$followers.'</td>';
                echo '<td id="'.$id.'">?</td>';
                echo '</tr><script>
        playersOnline("'.$id.'", "'.$ip.'");
</script>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>Geen parken op deze pagina.</p>';
        }
    }
    static function delete($mysqli, $parkid, $deleted) {
        $sql = "UPDATE pco_parks SET deleted='$deleted' WHERE ID='$parkid'";
        $result = mysqli_query($mysqli, $sql);
    }
    static function getParkIdFromAPIKey($mysqli, $key) {
        $sql="SELECT * FROM pco_parks WHERE APIKey='$key'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['ID'];
        } else {
            return '0';
        }
    }
    static function LoadFollowers($mysqli, $parkid)
    {
        if ($parkid == 18) {
            $sql = "SELECT * FROM pco_staff";
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);
            echo '<hr />';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '
                            <div>
                                <span> </span><span class="label ' . user::getLabel($mysqli, user::getRankByUUID($mysqli, $row["UUID"])) . '">' . user::getPrefix($mysqli, user::getRankByUUID($mysqli, $row["UUID"])) . '</span> <strong><a href="user.php?id='.user::getIDFromUUID($mysqli, $row["UUID"]).'" style="text-decoration: none; color: black;"><span>' . user::getNameByUUID($mysqli, $row["UUID"]) . '</a></strong></span>
                                <hr />
                            </div>
                        ';
            }
        } else {
            $sql = "SELECT * FROM pco_parks WHERE ID='$parkid'";
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);
            if ($count > 0) {
                $row = mysqli_fetch_assoc($result);
                $followers = explode(",", $row['followers']);
                echo '<hr />';
                for ($i = 0; $i < (count($followers) - 1); $i++) {
                    echo '
                            <div>
                                <span> </span><span class="label ' . user::getLabel($mysqli, user::getRankByUUID($mysqli, str_replace(",", "", $followers[$i]))) . '">' . user::getPrefix($mysqli, user::getRankByUUID($mysqli, str_replace(",", "", $followers[$i]))) . '</span> <span><strong><a href="user.php?id='.user::getIDFromUUID($mysqli, str_replace(",", "", $followers[$i])).'" style="text-decoration: none; color: black;">' . user::getNameByUUID($mysqli, str_replace(",", "", $followers[$i])) . '</a></strong></span>
                                <hr />
                            </div>
                        ';
                }
            } else {
                echo '<p>Geen volgers.</p>';
            }
        }
    }
}
class common {
    static function random($length) {
        $key = '';
        $keys = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        return $key;
    }
    static function uploadimagenotmp($img) {
        $filename = $img['tmp_name'];
        $client_id="c8a5d9459a7fca3";
        $handle = fopen($filename, "r");
        $data = fread($handle, filesize($filename));
        $pvars   = array('image' => base64_encode($data));
        $timeout = 30;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
        $out = curl_exec($curl);
        curl_close ($curl);
        $pms = json_decode($out,true);
        $url=$pms['data']['link'];
        if($url!=""){
            return str_replace("http","https",$url);;
        }else{
            return $pms['data']['error'];
        }
    }
    static function uploadimage($img) {
        $filename = $img;
        $client_id="c8a5d9459a7fca3";
        $handle = fopen($filename, "r");
        $data = fread($handle, filesize($filename));
        $pvars   = array('image' => base64_encode($data));
        $timeout = 30;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
        $out = curl_exec($curl);
        curl_close ($curl);
        $pms = json_decode($out,true);
        $url=$pms['data']['link'];
        if($url!=""){
            return str_replace("http","https",$url);;
        }else{
            return $pms['data']['error'];
        }
    }
    static function makeUrls($value, $protocols = array('http', 'mail'), array $attributes = array())
    {
        // Link attributes
        $attr = '';
        foreach ($attributes as $key => $val) {
            $attr = ' ' . $key . '="' . htmlentities($val) . '"';
        }

        $links = array();

        // Extract existing links and tags
        $value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) { return '<' . array_push($links, $match[1]) . '>'; }, $value);

        // Extract text links for each protocol
        foreach ((array)$protocols as $protocol) {
            switch ($protocol) {
                case 'http':
                case 'https':   $value = preg_replace_callback('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) { if ($match[1]) $protocol = $match[1]; $link = $match[2] ?: $match[3]; return '<' . array_push($links, "<a $attr href=\"$protocol://$link\">" . $link . "</a>") . '>'; }, $value); break;
                default:        $value = preg_replace_callback('~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) { return '<' . array_push($links, "<a $attr href=\"$protocol://{$match[1]}\">{$match[1]}</a>") . '>'; }, $value); break;
            }
        }

        // Insert all link
        return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) { return $links[$match[1] - 1]; }, $value);
    }
    static function getIpOfClient()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if(getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if(getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if(getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if(getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }
    static function closetags($html)
    {
        #put all opened tags into an array
        preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );
        $openedtags = $result[1];
        #put all closed tags into an array
        preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
        $closedtags = $result[1];
        $len_opened = count ( $openedtags );
        # all tags are closed
        if( count ( $closedtags ) == $len_opened )
        {
            return $html;
        }
        $openedtags = array_reverse ( $openedtags );
        # close tags
        for( $i = 0; $i < $len_opened; $i++ )
        {
            if ( !in_array ( $openedtags[$i], $closedtags ) )
            {
                $html .= "</" . $openedtags[$i] . ">";
            }
            else
            {
                unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
            }
        }
        return $html;
    }
}
class nav {
    static function parks($mysqli, $userid) {
        $sql = "SELECT * FROM pco_parks WHERE owner LIKE '%{$userid}%' AND deleted='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while($row = mysqli_fetch_assoc($result)) {
            if(!park::isDeleted($mysqli, $row['ID'])) {
                echo '
                    <a href="./park.php?id=' . $row['ID'] . '" class="dropdown-header"><li class="dropdown-header">' . $row['name'] . '</li></a>
                    <li><a href="./parksettings.php?id=' . $row['ID'] . '">Beheren</a></li>
                    <li><a href="./writearticle.php?id=' . $row['ID'] . '">Schrijf een artikel</a></li>
                    <li class="divider"></li>
                ';
            }
        }

        $sql = "SELECT * FROM pco_parks_staff WHERE uuid='$userid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while($row = mysqli_fetch_assoc($result)) {
            echo '
                    <a href="./park.php?id=' . $row['park_id'] . '" class="dropdown-header"><li class="dropdown-header">' . park::getName($mysqli, $row['park_id']) . '</li></a>
                    <li><a href="./parksettings.php?id=' . $row['park_id'] . '">Beheren</a></li>';
            if ($row['can_write'] == 1) {
                echo '<li><a href="./writearticle.php?id=' . $row['park_id'] . '">Schrijf een artikel</a></li>';
            } else {
                echo '<li><a href="" style="cursor:not-allowed">Schrijf een artikel</a></li>';
            }
            echo '<li class="divider"></li>';
        }
    }
}
class search {
    static function loadParks($mysqli, $keywords) {
        $sql="SELECT * FROM pco_parks WHERE name LIKE '%{$keywords}%' AND deleted='0' LIMIT 40";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while($row = mysqli_fetch_assoc($result)) {
            echo '
            <div class="col-md-12">
                <img class="avatar pull-left" src="' . park::getLogo($mysqli, $row["ID"]) . '" alt="" style="display: block; margin: 0 auto;"/><br />
                <a href="park.php?id=' . $row['ID'] . '" style="color: black; font-weight: bold; display: block; margin: 0 auto;"><span>' . $row['name'] . '</span></a>
            </div><br /><br /><hr />
            ';
        }
        if($count == 0) {
            echo '<p><strong>Geen overeenkomende parken</strong></p>';
        }
    }
    static function users($mysqli, $keywords) {
        $sql="SELECT * FROM pco_users WHERE access='1'  AND name LIKE '%{$keywords}%' LIMIT 40";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while($row = mysqli_fetch_assoc($result)) {
            echo '
            <div class="col-md-12">
                <img class="avatar pull-left" src="'.userpage::getProfilePicture($mysqli, $row["ID"]).'" alt="" style="display: block; margin: 0 auto;"/><br />
                <a href="user.php?id='.$row['ID'].'" style="color: black; font-weight: bold; display: block; margin: 0 auto;"><span>'.$row['name'].'</span></a>
            </div><br /><br /><hr />
            ';
        }
        if($count == 0) {
            echo '<p><strong>Geen overeenkomende gebruikers</strong></p>';
        }
    }
    static function load($mysqli, $keywords) {
        $sql = "SELECT * FROM pco_posts WHERE post_title LIKE '%{$keywords}%' AND reviewed='1' AND deleted='0' OR post_body LIKE '%{$keywords}%' AND deleted='0' AND reviewed='1' order by ID desc LIMIT 40";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while($row = mysqli_fetch_assoc($result)) {
            if(!park::isDeleted($mysqli, $row['park_id'])) {
                $title = $row['post_title'];
                $postid = $row['ID'];
                $parkname = park::getName($mysqli, $row['park_id']);
                $logo = park::getLogo($mysqli, $row['park_id']);
                $post = common::random(20);
                $postheader = $row['post_header'];
                if(strpos($postheader, 'Invalid URL') !== false) {
                    $postheader = park::getHeader($mysqli, $row['park_id']);
                }
                $icon = '';
                if(article::isLiking($mysqli, $postid, $_SESSION['UUID'])) {
                    $icon = 'favorite';
                } else {
                    $icon = 'favorite_border';
                }
                $like = '';
                if(article::isLiking($mysqli, $postid, $_SESSION['UUID'])) {
                    $like = 'unlike';
                } else {
                    $like = 'like';
                }
                echo '
                            <div class="hover" id="' . $post . '">
                                <div>
                                    <img class="avatar" src="' . $logo . '" alt=""/>
                                    <a href="park.php?id=' . $row['park_id'] . '" style="color: black; font-weight: bold;"><span>' . $parkname . '</span></a>
                                </div>
                                <div>
                                    <div>
                                        <img src="' . $postheader . '" alt="header" class="img-responsive center-block" style="max-height: 300px;"/>
                                    </div>
                                    <h3>' . $title . '</h3>
                                </div>
                                <script>
                                    var id' . $post . ' = document.getElementById("' . $post . '");

                                    id' . $post . '.onclick = function() {
                                        window.location.href = "article.php?id=' . $postid . '";
                                    };
                                </script>
                                <span class="shortcut"><i class="material-icons heart"><a href="article.php?id='.$postid.'&'.$like.'" style="text-decoration: none;">'.$icon.'</a></i><span><a href="article.php?id='.$postid.'&likes" style="color: #000000; text-decoration: none;">'.article::countLikes($mysqli, $postid).'</a>
                                <span class="shortcut"><i class="material-icons">mode_comment</i><span>'.article::getReactionCount($mysqli, $postid).'</span></span>
                                <span class="shortcut"><i class="material-icons">remove_red_eye</i><span>'.statistics::getCountsArticles($mysqli, $postid).'</span></span>
                                <i style="float: right;">Geplaatst op: '.$row["posted_on"].'</i>
                            </div>
                            <hr />

                            ';
            }
        }
        if($count == 0) {
            echo '<p><strong>Geen artikelen gevonden</strong></p>';
        }
    }
}
class system {
    static function isMaintenanceModeOn($mysqli) {
        $sql = "SELECT * FROM pco_settings WHERE variable='MAINTENANCE_MODE'";
        $result = mysqli_query($mysqli, $sql);
        $count=mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if(strcmp($row['data'], '0') == 0) {
            return false;
        }
        if(strcmp($row['data'], '1') == 0) {
            if(user::getRank($mysqli) > 0) {
                return false;
            }
            return true;
        }
        return false;
    }
    static function copyRightSign() {
        echo '<!--Copyright (c) 2017 Daníque de Jong-->';
    }
    static function addPageVisit($mysqli) {
        if(user::getRank($mysqli) > 0) {

        } else {
            setlocale(LC_TIME, 'NL_nl');
            $datenow = system::getDateNow();
            $sql = "SELECT * FROM pco_pageview WHERE date='$datenow'";
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);
            if ($count > 0) {
                $oldcount = $row['count'];
                $newcount = $oldcount + 1;
                $sql1 = "UPDATE pco_pageview SET count='$newcount' WHERE date='$datenow'";
                $result1 = mysqli_query($mysqli, $sql1);
            } else {
                $oldcount = 0;
                $newcount = $oldcount + 1;
                $sql1 = "INSERT INTO pco_pageview (date, count) VALUES ('$datenow', '$newcount')";
                $result1 = mysqli_query($mysqli, $sql1);
            }
        }
    }
    static function getDateNow() {
        setlocale(LC_TIME, 'NL_nl');
        $time = strftime('%Y-%m-%e',time());
        return $time;
    }
    static function getBackground($mysqli) {
        $tijd = date("G");
        if ($tijd < 6) {
            $sql="SELECT * FROM pco_backgrounds WHERE tijd='3' ORDER BY RAND() LIMIT 1";
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);
            echo '<style>
                body {
                    background: url(/resources/backgrounds/'.$row["background"].');
                }
              </style>';
        } elseif ($tijd < 12) {
            $sql="SELECT * FROM pco_backgrounds WHERE tijd='0' ORDER BY RAND() LIMIT 1";
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);
            echo '<style>
                body {
                    background: url(/resources/backgrounds/'.$row["background"].');
                }
              </style>';
        } elseif ($tijd < 18) {
            $sql="SELECT * FROM pco_backgrounds WHERE tijd='1' ORDER BY RAND() LIMIT 1";
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);
            echo '<style>
                body {
                    background: url(/resources/backgrounds/'.$row["background"].');
                }
              </style>';
        } else {
            $sql="SELECT * FROM pco_backgrounds WHERE tijd='2' ORDER BY RAND() LIMIT 1";
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);
            echo '<style>
                body {
                    background: url(/resources/backgrounds/'.$row["background"].');
                }
              </style>';
        }
    }
}
class ads {
    static function skycraper($mysqli, $page) {
        $page = explode(".", $page);

        $randomrow = array(
            "ID",
            "ad_code",
            "ad_pages"
        );
        $randomorder = array(
            "asc",
            "desc"
        );
        $ranrow = $randomrow[array_rand($randomrow,1)];
        $ranorder = $randomorder[array_rand($randomorder,1)];


        $sql = "SELECT * FROM pco_ads WHERE ad_pages='$page[0]' AND ad_sort='0' order by ".$ranrow." ".$ranorder;
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);

        $row = mysqli_fetch_assoc($result);

        if($count != 0) {
            echo $row['ad_code'];
        } else {

            $sql = "SELECT * FROM pco_ads WHERE ad_pages='*' AND ad_sort='0' order by ".$ranrow." ".$ranorder;
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);

            $row = mysqli_fetch_assoc($result);
            echo $row['ad_code'];
        }
    }
    static function vierkant($mysqli, $page) {
        $page = explode(".", $page);

        $randomrow = array(
            "ID",
            "ad_code",
            "ad_pages"
        );
        $randomorder = array(
            "asc",
            "desc"
        );
        $ranrow = $randomrow[array_rand($randomrow,1)];
        $ranorder = $randomorder[array_rand($randomorder,1)];


        $sql = "SELECT * FROM pco_ads WHERE ad_pages='$page[0]' AND ad_sort='1' order by ".$ranrow." ".$ranorder;
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);

        $row = mysqli_fetch_assoc($result);

        if($count != 0) {
            echo $row['ad_code'];
        } else {

            $sql = "SELECT * FROM pco_ads WHERE ad_pages='*' AND ad_sort='1' order by ".$ranrow." ".$ranorder;
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);

            $row = mysqli_fetch_assoc($result);
            echo $row['ad_code'];
        }
    }
}
class staff {
    static function canUseStaffPanel($mysqli, $useruuid) {
        $sql = "SELECT * FROM pco_staff WHERE UUID='$useruuid' AND can_use_staffpanel='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function canManageParkRequests($mysqli, $useruuid) {
        $sql = "SELECT * FROM pco_staff WHERE UUID='$useruuid' AND can_manage_parkrequests='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function canManageUsers($mysqli, $useruuid) {
        $sql = "SELECT * FROM pco_staff WHERE UUID='$useruuid' AND can_manage_users='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function canManageParks($mysqli, $useruuid) {
        $sql = "SELECT * FROM pco_staff WHERE UUID='$useruuid' AND can_manage_parks='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function canManageComments($mysqli, $useruuid) {
        $sql = "SELECT * FROM pco_staff WHERE UUID='$useruuid' AND can_manage_comments='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function canSendMail($mysqli, $useruuid) {
        $sql = "SELECT * FROM pco_staff WHERE UUID='$useruuid' AND can_send_mail='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function canManagePosts($mysqli, $useruuid) {
        $sql = "SELECT * FROM pco_staff WHERE UUID='$useruuid' AND can_manage_posts='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function canManageApplications($mysqli, $useruuid) {
        $sql = "SELECT * FROM pco_staff WHERE UUID='$useruuid' AND can_manage_applications='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function canManageJobs($mysqli, $useruuid) {
        $sql = "SELECT * FROM pco_staff WHERE UUID='$useruuid' AND can_manage_jobs='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function canManageChats($mysqli, $useruuid) {
        $sql = "SELECT * FROM pco_staff WHERE UUID='$useruuid' AND can_manage_chats='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    static function remoteAccount($mysqli, $uuid) {
        $sql="SELECT * FROM pco_users WHERE UUID='$uuid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            if(!user::hasAccess($mysqli, $row['UUID'])) {
                header("Location: index.php?warning=Je hebt geen toegang tot ParkCraft Online.");
                exit;
            }
            session_start();
            $_SESSION['remoteuuid'] = $_SESSION['UUID'];
            $_SESSION['remoteuser'] = $_SESSION['user'];
            $_SESSION['user'] = $row['ID'];
            $_SESSION['UUID'] = $row['UUID'];
            header("Location: home.php");
            exit;
        }
    }
}
class help {
    static function loadHelp($mysqli) {
        $sql="SELECT * FROM pco_help ORDER BY ID";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $title = $row['title'];
                $content = $row['content'];
                echo '<strong>'.$title.'</strong>';
                echo '<p>'.$content.'</p>';
                echo '<hr />';
            }
        }
    }
}
class rides {
    static function getRideStatus($mysqli, $ride_code) {
        $sql="SELECT * FROM pco_parks_rides WHERE ride_code='$ride_code' AND type='0';";
        $result=mysqli_query($mysqli, $sql);
        $row=mysqli_fetch_assoc($result);
        return $row['status'];
    }
    static function getShowStatus($mysqli, $show_code) {
        $sql="SELECT * FROM pco_parks_rides WHERE ride_code='$show_code' AND type='1';";
        $result=mysqli_query($mysqli, $sql);
        $row=mysqli_fetch_assoc($result);
        return $row['status'].' '.$row['time'];
    }
    static function getRides($mysqli, $park_id) {
        $sql="SELECT ride_name, ride_code FROM pco_parks_rides WHERE park_id='$park_id' AND type='0';";
        $result=mysqli_query($mysqli, $sql);
        $rides = array();
        while($row =mysqli_fetch_assoc($result))
        {
            $row_array['ride_name'] = $row['ride_name'];
            $row_array['ride_name'] = $row['ride_code'];
            array_push($shows,$row_array);
        }
        echo json_encode($rides);
    }
    static function getShows($mysqli, $park_id) {
        $sql="SELECT ride_name, ride_code FROM pco_parks_rides WHERE park_id='$park_id' AND type='1';";
        $result=mysqli_query($mysqli, $sql);
        $shows = array();
        while($row =mysqli_fetch_assoc($result))
        {
            $row_array['show_name'] = $row['ride_name'];
            $row_array['show_code'] = $row['ride_code'];
            $row_array['time'] = $row['timew'];
            array_push($shows,$row_array);
        }
        echo json_encode($shows);
    }
    static function loadrides($mysqli, $parkid) {
        $sql="SELECT * FROM pco_parks_rides WHERE park_id='$parkid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            echo '
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Code naam</th>
                            <th>Type</th>
                            <th>Opties</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>'.$row['ride_name'].'</td>';
                echo '<td>'.$row["ride_code"].'</td>';
                if($row['type'] == 0) {
                    echo '<td>Attractie</td>';
                } else if($row['type'] == 1) {
                    echo '<td>Show</td>';
                }
                echo '<td><a href="parksettings.php?id='.$parkid.'&removeride='.$row["ride_code"].'&rides" class="btn btn-danger btn-sm">Verwijderen</a></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>Er zijn momenteel geen attracties</p>';
        }
        echo '
                      <button type="button" class="btn-info" data-toggle="modal" data-target="#addride">Attractie toevoegen</button> <button type="button" class="btn-info" data-toggle="modal" data-target="#addshow">Show toevoegen</button>

                       <div id="addride" class="modal fade" role="dialog">
                          <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Attractie toevoegen</h4>
                              </div>
                              <div class="modal-body">
                                <form name="addride" id="addrides" action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="post" autocomplete="off" class="form-horizontal">
                                    <span>Attractienaam<br /></span><input type="text" name="ridename" value=""/><br />
                                    <input type="hidden" value="'.$parkid.'" name="id"/>
                                    <button type="submit" class="btn btn-raised btn-success" name="addride" id="postbutton">Toevoegen
                                  </button>
                                </form>
                              </div>
                            </div>

                          </div>
                        </div>
                       <div id="addshow" class="modal fade" role="dialog">
                          <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Show toevoegen</h4>
                              </div>
                              <div class="modal-body">
                                <form name="addshow" id="addshows" action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="post" autocomplete="off" class="form-horizontal">
                                    <span>Shownaam<br /></span><input type="text" name="showname" value=""/><br />
                                    <input type="hidden" value="'.$parkid.'" name="id"/>
                                    <button type="submit" class="btn btn-raised btn-success" name="addshow" id="postbutton">Toevoegen
                                  </button>
                                </form>
                              </div>
                            </div>

                          </div>
                        </div>
                ';
    }
    static function addshow($mysqli, $parkid, $showname) {
        $showname = strip_tags($showname);
        $code = rides::generateRideCode($mysqli, 4, preg_replace("/[^a-zA-Z0-9]+/", "",str_replace(' ', '', preg_replace('/[[:^print:]]/', '', strip_tags($showname)))));
        $shownamewq = str_replace("'", "&#39;",$showname);
        $sql = "INSERT INTO pco_parks_rides (type, park_id, ride_name, ride_code) VALUES ('1', '$parkid', '$shownamewq', '$code');";
        $result = mysqli_query($mysqli, $sql);
    }
    static function addride($mysqli, $parkid, $ridename) {
        $ridename = strip_tags($ridename);
        $code = rides::generateRideCode($mysqli, 4, preg_replace("/[^a-zA-Z0-9]+/", "",str_replace(' ', '', preg_replace('/[[:^print:]]/', '', strip_tags($ridename)))));
        $ridenamewq = str_replace("'", "&#39;",$ridename);
        $sql = "INSERT INTO pco_parks_rides (type, park_id, ride_name, ride_code) VALUES ('0', '$parkid', '$ridenamewq', '$code');";
        $result = mysqli_query($mysqli, $sql);
    }
    static function removeride($mysqli, $ridecode) {
        $sql = "DELETE FROM pco_parks_rides WHERE ride_code='$ridecode'";
        $result = mysqli_query($mysqli, $sql);
    }
    static function generateRideCode($mysqli, $length, $ridename) {
        $key = '';
        $keys = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        if(rides::rideCodeExist($mysqli, $key.$ridename)) {
            return rides::generateRideCode($mysqli, $length, $ridename);
        }
        return $key.$ridename;
    }
    static function rideCodeExist($mysqli, $code) {
        $sql = "SELECT * FROM pco_parks_rides WHERE ride_code='$code';";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function loadForParkPage($mysqli, $parkid) {
        $sql="SELECT * FROM pco_parks_rides WHERE park_id='$parkid' AND type='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        echo '
                <table class="table">
                    <thead>
                        <tr>
                            <th>Show</th>
                            <th>Status</th>
                        </tr>
                    </thead>';
        echo '<tbody>';
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['ride_name'];
            $status = $row['status'];
            $time = $row['time'];
            echo '<tr>';
            echo '<td>'.$name.'</td>';
            echo '<td>'.rides::getLabelShow($status, $time).'</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        if (!$count > 0) {
            echo '<p>Geen shows gevonden voor dit park.</p>';
        }

        $sql="SELECT * FROM pco_parks_rides WHERE park_id='$parkid' AND type='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        echo '
                  <table class="table">
                    <thead>
                        <tr>
                            <th>Attractie</th>
                            <th>Status</th>
                        </tr>
                    </thead>';
        echo '<tbody>';
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['ride_name'];
            $status = $row['status'];
            echo '<tr>';
            echo '<td>'.$name.'</td>';
            echo '<td>'.rides::getLabelRide($status).'</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        if (!$count > 0) {
            echo '<p>Geen attracties gevonden voor dit park.</p>';
        }
    }
    static function getLabelRide($int) {
        if($int == 0) {
            return '<span class="label label-danger">Gesloten</span>';
        }
        if($int == 1) {
            return '<span class="label label-success">Geopend</span>';
        }
        if($int == 2) {
            return '<span class="label label-warning">Onderhoud</span>';
        }
        if($int == 3) {
            return '<span class="label label-info">Winter</span>';
        }
        if($int == 4) {
            return '<span class="label label-default">In bouw</span>';
        }
        if($int == 5) {
            return '<span class="label label-warning">Storing</span>';
        }
        return '<span class="label label-danger">Error</span>';
    }
    static function getLabelShow($int, $time) {
        if($int == 0) {
            return '<span class="label label-danger">Gesloten</span>';
        }
        if($int == 1) {
            return '<span class="label label-success">Bezig</span>';
        }
        if($int == 2) {
            return '<span class="label label-info">Begint om '.$time.'</span>';
        }
        if($int == 3) {
            return '<span class="label label-warning">Onderhoud</span>';
        }
        return '<span class="label label-danger">Error</span>';
    }
    static function updateStatus($mysqli, $ride_code, $status, $key) {
        $parkid = park::getParkIdFromAPIKey($mysqli, $key);
        $sql="UPDATE pco_parks_rides SET status = '$status' WHERE ride_code='$ride_code' AND park_id='$parkid';";
        $result=mysqli_query($mysqli, $sql);
    }
    static function rideExist($mysqli, $ride_code) {
        $sql = "SELECT * FROM pco_parks_rides WHERE ride_code='$ride_code'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function isRideOfPark($mysqli, $parkid, $ride_code) {
        $sql = "SELECT * FROM pco_parks_rides WHERE ride_code='$ride_code' AND park_id='$parkid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function countRidesOfPark($mysqli, $parkid) {
        $sql = "SELECT * FROM pco_parks_rides WHERE park_id='$parkid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
    static function updateShowStatus($mysqli, $ride_code, $status, $time, $key) {
        $parkid = park::getParkIdFromAPIKey($mysqli, $key);
        $sql="UPDATE pco_parks_rides SET time = '$time', status = '$status' WHERE ride_code='$ride_code' AND park_id='$parkid';";
        $result=mysqli_query($mysqli, $sql);
    }
}
class API {
    static function generateAPIKey($mysqli) {
        $length = 20;
        $key = '';
        $keys = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        if(API::APIKeyExist($mysqli, $key)) {
            return rides::generateAPIKey($mysqli);
        }
        return $key;
    }
    static function APIKeyExist($mysqli, $code) {
        $sql = "SELECT * FROM pco_parks WHERE APIKey='$code';";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function getKeyOfPark($mysqli, $parkid) {
        $sql="SELECT * FROM pco_parks WHERE ID='$parkid'";
        $result=mysqli_query($mysqli,$sql);
        $row = mysqli_fetch_assoc($result);
        return $row['APIKey'];
    }
    static function keyExist($mysqli, $key) {
        $sql = "SELECT * FROM pco_parks WHERE APIKey='$key'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
}
class parkcraft {
    static function IsAuthor($mysqli, $useruuid) {
        $sql = "SELECT * FROM pco_staff WHERE UUID='$useruuid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            if($row['rank'] > 2) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    static function canWriteTutorials($mysqli, $useruuid) {
        $sql = "SELECT * FROM pco_staff WHERE UUID='$useruuid' AND can_write_tutorials='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            if(user::getRankByUUID($mysqli, $useruuid) > 2) {
                return true;
            } else {
                return false;
            }
        }
    }
    static function canWritePluginVanDeWeek($mysqli, $useruuid) {
        $sql = "SELECT * FROM pco_staff WHERE UUID='$useruuid' AND can_write_pvdw='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            if(user::getRankByUUID($mysqli, $useruuid) > 2) {
                return true;
            } else {
                return false;
            }
        }
    }
    static function canWriteParkVanDeMaand($mysqli, $useruuid) {
        $sql = "SELECT * FROM pco_staff WHERE UUID='$useruuid' AND can_write_pvdm='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            if(user::getRankByUUID($mysqli, $useruuid) > 2) {
                return true;
            } else {
                return false;
            }
        }
    }

    /*Tutorial*/
    static function loadTutorials($mysqli) {
        $sql = "SELECT * FROM pco_tutorials WHERE deleted='0' order by ID desc";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count == 0) {
            echo 'Geen tutorials gevonden.';
        }
        while ($row = mysqli_fetch_assoc($result)) {
            $title = $row['post_title'];
            $postid = $row['ID'];
            $post = common::random(20);
            $postheader = $row['post_images'];
            if(strpos($postheader, 'Invalid URL') !== false) {
                $postheader = park::getHeader($mysqli, $row['park_id']);
            }
            $icon = '';
            if(parkcraft::isLikingTut($mysqli, $postid, $_SESSION['UUID'])) {
                $icon = 'favorite';
            } else {
                $icon = 'favorite_border';
            }
            $like = '';
            if(parkcraft::isLikingTut($mysqli, $postid, $_SESSION['UUID'])) {
                $like = 'unlike';
            } else {
                $like = 'like';
            }
            echo '
                        <div class="jumbotron hover" id="' . $post . '">
                            <div>
                                <div>
                                    <img src="' . $postheader . '" alt="header" class="img-responsive center-block" style="max-height: 300px;"/>
                                </div>
                                <h3>' . $title . '</h3>
                            </div>
                            <script>
                                var id' . $post . ' = document.getElementById("' . $post . '");

                                id' . $post . '.onclick = function() {
                                    window.location.href = "tutorial.php?id=' . $postid . '";
                                };
                            </script>
                            <span class="shortcut"><i class="material-icons heart"><a href="article.php?id='.$postid.'&'.$like.'" style="text-decoration: none;">'.$icon.'</a></i><span><a href="article.php?id='.$postid.'&likes" style="color: #000000; text-decoration: none;">'.parkcraft::countLikesTut($mysqli, $postid).'</a>
                            <span class="shortcut"><i class="material-icons">mode_comment</i><span>'.parkcraft::getReactionCountTut($mysqli, $postid).'</span></span>
                            <i style="float: right;">Geplaats op: '.$row["posted_on"].'</i>
                        </div>


                        ';
        }
    }
    static function postTutorial($mysqli, $title, $body, $bodyimg)
    {
        setlocale(LC_TIME, 'NL_nl');
        if(strpos($bodyimg, 'Invalid URL') !== false) {
            $bodyimg = 'https://parkcraft.nl/resources/header.jpg';
        }
        $title = strip_tags($title);
        $sql = "INSERT INTO pco_tutorials (post_title, post_body, post_images, post_poster, post_likes, posted_on) VALUES ('$title', '', '$bodyimg', '" . $_SESSION['UUID'] . "', '',  '" . strftime('%e-%m-%Y om %H:%M', time()) . "');";
        $result = mysqli_query($mysqli, $sql);
        $sql1 = "SELECT * FROM pco_tutorials WHERE post_images='$bodyimg' AND post_title='$title' order by ID desc";
        $result1 = mysqli_query($mysqli, $sql1);
        $row1 = mysqli_fetch_assoc($result1);
        $id = $row1['ID'];
        $splitted = str_split($body, 1);
        for($i = 0; $i < count($splitted); $i++) {
            $sql3 = "SELECT * FROM pco_tutorials WHERE ID='$id' AND post_title='$title' order by ID desc";
            $result3 = mysqli_query($mysqli, $sql3);
            $row3 = mysqli_fetch_assoc($result3);
            $oldbody = $row3['post_body'];
            $newbody = $oldbody.$splitted[$i];
            $sql2 = "UPDATE pco_tutorials SET post_body = '$newbody' WHERE ID='$id'";
            $result2 = mysqli_query($mysqli, $sql2);
        }
    }
    static function loadTutorial($mysqli, $id) {
        $sql = "SELECT * FROM pco_tutorials WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        $title = $row['post_title'];
        $body = $row['post_body'];
        $postimages = $row['post_images'];
        if(strpos($postimages, 'Invalid URL') !== false) {
            $postimages = '';
        }
        $url = "https://www.parkcraft.nl/tutorial.php?id=$id";
        $icon = '';
        if(parkcraft::isLikingTut($mysqli, $id, $_SESSION['UUID'])) {
            $icon = 'favorite';
        } else {
            $icon = 'favorite_border';
        }
        $like = '';
        if(parkcraft::isLikingTut($mysqli, $id, $_SESSION['UUID'])) {
            $like = 'unlike';
        } else {
            $like = 'like';
        }
        echo '
            <div>
                <div>
                    <img src="'.$postimages.'" alt="header" class="img-responsive center-block" style="max-height: 300px;"/>
                </div>
                <h3>'.$title.'</h3>
                <span>'.$body.'</span>
                <hr />
                <i style="float: right;">Gepost: '.$row["posted_on"].'</i>
                <span style="float: left;" class="shortcut"><i class="material-icons heart"><a href="?id='.$id.'&'.$like.'" style="text-decoration: none">'.$icon.'</a></i><span><a href="?id='.$id.'&likes" style="color: #000000; text-decoration: none;">'.parkcraft::countLikesTut($mysqli, $id).'</a></span></span><br /><br />
                <ul class="share-buttons">
                  <li><a href="https://www.facebook.com/sharer/sharer.php?u='.$url.'&t='.$title.'" title="Share on Facebook" target="_blank"><img alt="Share on Facebook" src="resources/svg/Facebook.svg"></a></li>
                  <li><a href="https://twitter.com/intent/tweet?source='.$url.'&text='.$title.' '.$url.'&via=parkencraft" target="_blank" title="Tweet"><img alt="Tweet" src="resources/svg/Twitter.svg"></a></li>
                  <li><a href="http://www.reddit.com/submit?url='.$url.'&title='.$title.'" target="_blank" title="Submit to Reddit"><img alt="Submit to Reddit" src="resources/svg/Reddit.svg"></a></li>
                </ul>
            </div>
        ';
    }
    static function likeTut($mysqli, $articleid, $uuid) {
        if(!parkcraft::isLikingTut($mysqli, $articleid, $uuid)) {
            $sql="UPDATE pco_tutorials SET post_likes = CONCAT(post_likes,'".$uuid.",') WHERE ID='$articleid';";
            $result=mysqli_query($mysqli, $sql);
        }
    }
    static function unlikeTut($mysqli, $articleid, $uuid) {
        if(parkcraft::isLikingTut($mysqli, $articleid, $uuid)) {
            $sql = "UPDATE pco_tutorials SET post_likes = REPLACE(post_likes,'" . $uuid . ",','') WHERE ID='$articleid';";
            $result = mysqli_query($mysqli, $sql);
        }
    }
    static function isLikingTut($mysqli, $articleid, $uuid) {
        $sql = "SELECT * FROM pco_tutorials WHERE ID='$articleid' AND post_likes LIKE '%{$uuid}%'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function countLikesTut($mysqli, $articleid) {
        $sql="SELECT * FROM pco_tutorials WHERE ID='$articleid'";
        $result=mysqli_query($mysqli,$sql);
        $count=mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        $likes = explode(",", $row['post_likes']);
        return (count($likes)-1);
    }
    static function LoadLikesTut($mysqli, $postid) {
        $sql="SELECT * FROM pco_tutorials WHERE ID='$postid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            $row = mysqli_fetch_assoc($result);
            $followers = explode(",", $row['post_likes']);
            for($i = 0; $i < (count($followers)-1); $i++) {
                echo '
            <div>
                <span> </span><span class="label '.user::getLabel($mysqli, user::getRankByUUID($mysqli, str_replace(",", "", $followers[$i]))).'">'.user::getPrefix($mysqli, user::getRankByUUID($mysqli, str_replace(",", "", $followers[$i]))).'</span> <span>'.user::getNameByUUID($mysqli, str_replace(",", "", $followers[$i])).'</span>
                <hr />
            </div>
        ';
            }
        } else {
            echo '<p>Geen likes.</p>';
        }
    }
    static function existTut($mysqli, $id) {
        $sql = "SELECT * FROM pco_tutorials WHERE ID=$id AND deleted='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            $sql1 = "SELECT * FROM pco_tutorials WHERE ID=$id AND deleted='1' OR deleted='2'";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            if($count1 > 0) {
                if (staff::canManagePosts($mysqli, $_SESSION['UUID'])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
    static function getTitleTut($mysqli, $id) {
        $sql="SELECT * FROM pco_tutorials WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['post_title'];
        } else {
            return 'Artikel verwijderd';
        }
    }
    static function loadReactionsTut($mysqli, $id) {
        $sql = "SELECT * FROM pco_parkcraft_reaction WHERE pc_id='$id' AND type='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while($row = mysqli_fetch_assoc($result)) {
            $rowid = $row['ID'];
            echo '
                <span class="label ' . user::getLabel($mysqli, user::getRankByUUID($mysqli, $row['uuid'])) . '">' . user::getPrefix($mysqli, user::getRankByUUID($mysqli, $row['uuid'])) . '</span><span> ' . user::getNameByUUID($mysqli, $row['uuid']) . '</span>
                <p>' . $row['reaction'] . '</p>';
            if ($row['uuid'] == $_SESSION['UUID'] || staff::canManageComments($mysqli, $_SESSION['UUID'])) {
                echo '<a href="?remove=' . $rowid . '&id=' . $id . '">Verwijder</a>';
            }
            echo '<hr />';
        }
    }
    static function getReactionCountTut($mysqli, $id) {
        $sql = "SELECT * FROM pco_parkcraft_reaction WHERE pc_id='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
    static function PlaceReactionTut($mysqli, $id, $reaction) {
        $user = $_SESSION['UUID'];
        $reaction = strip_tags($reaction);
        $sql = "INSERT INTO pco_parkcraft_reaction (type, pc_id, uuid, reaction) VALUES ('0', '$id', '$user', '$reaction')";
        $result = mysqli_query($mysqli, $sql);
    }
    static function RemoveReactionTut($mysqli, $id) {
        $user = $_SESSION['UUID'];
        $sql = "SELECT * FROM pco_parkcraft_reaction WHERE ID='$id' AND uuid='$user'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0 || user::getRank($mysqli) > 2 ) {
            $sql = "DELETE FROM pco_parkcraft_reaction WHERE ID = $id";
            $result = mysqli_query($mysqli, $sql);
        }
    }
}
class youtube {
    static function getVideos() {
        $channelId = "UCsr2tzTaKOsZc4_f2inW7ww";
        $apiKey = "AIzaSyChLWRX9x370oHr488kZ2biV88HK1z78lI";
        $max = 30;
        $file = "https://www.googleapis.com/youtube/v3/search?key=".$apiKey."&channelId=".$channelId."&part=snippet,id&order=date&maxResults=".$max."";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        $json = curl_exec($ch);
        $result = json_decode($json, true);
        foreach($result['items'] as $items) {
            $title = $items['snippet']['title'];
            $thumbnail = $items['snippet']['thumbnails']['default']['url'];
            $description = $items['snippet']['description'];
            $videoid = $items['id']['videoId'];
            echo "<hr /><div class='hover'data-toggle='modal' data-target='#".$videoid."'>"
                ."<img src='".$thumbnail."' style='border:none;float:left;margin-right:10px;' alt='".$title."' title='".$title."' />"
                ."<h3><span>".$title."</span></h3>".$description
                ."</div>";
            echo '
                        <div id="'.$videoid.'" class="modal fade" role="dialog">
                          <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">'.$title.'</h4>
                              </div>
                              <div class="modal-body">
                              <object width="100%" height="315" id="v'.$videoid.'"
                                data="https://www.youtube.com/embed/'.$videoid.'">
                                </object>
                              </div>
                            </div>

                          </div>
                        </div>';
        }
    }
}
class events {
    static function load($mysqli) {
        $sql = "SELECT * FROM pco_parks_events ORDER BY event_start ASC";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            echo '<style>#panelbody {background:#428baa;}</style>';
            while($row = mysqli_fetch_assoc($result)) {
                if (new DateTime() < new DateTime($row['event_start']) || new DateTime() < new DateTime($row['event_end'])) {
                    $sdate = new DateTime($row['event_start']);
                    $syear = $sdate->format("Y");
                    $smonth = $sdate->format("n");
                    $sday = $sdate->format("d");
                    $sdag = $sdate->format("N");
                    $stime = $sdate->format("H:i");

                    $edate = new DateTime($row['event_end']);
                    $eyear = $edate->format("Y");
                    $emonth = $edate->format("n");
                    $eday = $edate->format("d");
                    $edag = $edate->format("N");
                    $etime = $edate->format("H:i");

                    $maanden=array('Januari','Februari','Maart','April','Mei','Juni','Juli','Augustus','September','Oktober','November','December');
                    $dagen=array('Maandag','Dinsdag','Woensdag','Donderdag','Vrijdag','Zaterdag','Zondag');
                    echo '
                        <div class="col-md-12">
                            <div class="panel panel-info">
                              <div class="panel-heading text-center">
                                <h3 class="panel-title">'.$row['event_title'].'</h3>
                              </div>
                              <div class="panel-body text-center" id="panelbody">
                                <h4>Van '.$dagen[($sdag-1)].', '.$sday.' '.$maanden[($smonth-1)].' '.$syear.', '.$stime.'</h4><br />
                                <h4>Tot '.$dagen[($edag-1)].', '.$eday.' '.$maanden[($emonth-1)].' '.$eyear.', '.$etime.'</h4>
                              </div>
                              <div class="panel-body text-center" id="">
                                <h4>Van '.$dagen[($sdag-1)].', '.$sday.' '.$maanden[($smonth-1)].' '.$syear.', '.$stime.'</h4><br />
                                <h4>Tot '.$dagen[($edag-1)].', '.$eday.' '.$maanden[($emonth-1)].' '.$eyear.', '.$etime.'</h4>
                              </div>
                              <div class="panel-footer text-center">
                                '.$row['event_description'].'
                              </div>
                            </div>
                        </div>
                            ';
                }
            }
        }
    }
}
class statistics {
    static function parkRequestsRejected($mysqli) {
        $sql = "SELECT * FROM pco_parkrequest WHERE rejected='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
    static function parks($mysqli) {
        $sql = "SELECT * FROM pco_parks WHERE deleted='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
    static function parkEvents($mysqli) {
        $sql = "SELECT * FROM pco_parks_events";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
    static function parkRides($mysqli) {
        $sql = "SELECT * FROM pco_parks_rides";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
    static function parkStaff($mysqli) {
        $sql = "SELECT * FROM pco_parks_staff";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
    static function posts($mysqli) {
        $sql = "SELECT * FROM pco_posts WHERE deleted='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
    static function reactions($mysqli) {
        $sql = "SELECT * FROM pco_reaction";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
    static function users($mysqli) {
        $sql = "SELECT * FROM pco_users";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
    static function usersActivated($mysqli) {
        $sql = "SELECT * FROM pco_users WHERE activated='1'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
    static function totalPageVisits($mysqli) {
        $datenow = strftime('%Y-%m-%e', time());
        $sql = "SELECT * FROM pco_pageview WHERE date='$datenow'";
        $result = mysqli_query($mysqli, $sql);
        $count=mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            $idnow = $row['ID'];
            $sql1 = "SELECT * FROM pco_pageview WHERE ID > '".($idnow - 6)."'";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1=mysqli_num_rows($result1);
            while($row1 = mysqli_fetch_assoc($result1)) {
                $datesql = explode("-", $row1['date']);
                $year = $datesql['0'];
                $month = $datesql['1'];
                $day = $datesql['2'];
                echo '{ x: new Date('.$year.','.($month-1).','.$day.'), y: '.$row1["count"].' },
                ';
            }
        }
        return 0;
    }
    static function articleVisit($mysqli, $user, $articleid) {
        $sql="SELECT * FROM pco_posts_view WHERE article='$articleid' and views LIKE '%{$user}%'";
        $result=mysqli_query($mysqli,$sql);
        $count=mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count == 0) {
            $sql = "SELECT * FROM pco_posts_view WHERE article='$articleid'";
            $result = mysqli_query($mysqli, $sql);
            $count = mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);
            if ($count > 0) {
                $sql1 = "UPDATE pco_posts_view SET views = CONCAT(views,'" . $user . ",') WHERE article='$articleid'";
                $result1 = mysqli_query($mysqli, $sql1);
            } else {
                $sql1 = "INSERT INTO pco_posts_view (article, views) VALUES ('$articleid', '" . $user . ",')";
                $result1 = mysqli_query($mysqli, $sql1);
            }
        }
    }
    static function getCountsArticles($mysqli, $articleid) {
        $sql = "SELECT * FROM pco_posts_view WHERE article='$articleid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            $row = mysqli_fetch_assoc($result);
            $followers = explode(",", $row['views']);
            return (count($followers) - 1);
        } else {
            return '0';
        }
    }
    static function getTodayUniqueVisitors($mysqli) {
        setlocale(LC_TIME, 'NL_nl');
        $time = strftime('%e-%m-%Y',time());
        $sql = "SELECT * FROM pco_users WHERE last_execution LIKE '%{$time}%'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
}
class vacature {
    static function loadVacatures($mysqli, $parkid)
    {
        $sql = "SELECT * FROM pco_parks_jobs WHERE park_id='$parkid' AND deleted='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $name = $row['job_name'];
                $description = $row['job_description'];
                $id = $row['ID'];
                echo '
                            <div class="jumbotron">
                                <div class="hover" id="' . $id . '">
                                    <h4>Vacature: ' . $name . '</h4>
                                    <h5>' . $description . '</h5>
                                </div>
                                <script>
                                    var id' . $id . ' = document.getElementById("' . $id . '");

                                    id' . $id . '.onclick = function() {
                                        window.location.href = "vacature.php?id=' . $id . '";
                                    };
                                </script>
                                    <span>' . vacature::countApplications($mysqli, $id) . ' sollicitant(en)</span>
                            </div>


                            ';
            }
        } else {
            echo '
                        <div class="jumbotron">
                            <p>Geen vacatures gevonden!</p>
                        </div>


                        ';
        }
    }
    static function vacaturesCount($mysqli, $parkid)
    {
        $sql = "SELECT * FROM pco_parks_jobs WHERE park_id='$parkid'  AND deleted='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        echo $count;
    }
    static function loadVacaturesSettings($mysqli, $parkid)
    {
        $sql = "SELECT * FROM pco_parks_jobs WHERE park_id='$parkid' AND deleted='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            echo '
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Sollicitaties</th>
                            <th>Opties</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                $opnieuwaanbieden = '';
                $id = $row["ID"];
                $datetime1 = new DateTime();

                $datetime2 = new DateTime($row['expires']);
                $difference = $datetime1->diff($datetime2);
                if($datetime1 > $datetime2) {
                    $opnieuwaanbieden = '<br />
                    <a href="parksettings.php?id=' . $parkid . '&offeragain=' . $row["ID"] . '&jobs" class="btn btn-warning btn-sm">Opnieuw aanbieden</a>';
                } else {
                    $opnieuwaanbieden = '';
                }
                echo '<tr>';
                echo '<td>' . $row['job_name'] . '</td>';
                echo '<td>' . vacature::countApplications($mysqli, $id) . ' sollicitant(en)<br/><a href="parksettings.php?id=' . $parkid . '&jobs=&application=' . $row["ID"] . '">Sollicitaties bekijken</a></td>';
                echo '<td><button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#' . $row["ID"] . '">Edit</button><a href="parksettings.php?id=' . $parkid . '&removevacature=' . $row["ID"] . '&jobs" class="btn btn-danger btn-sm">Verwijderen</a>'.$opnieuwaanbieden.'</td>';
                echo '<div id="' . $id . '" class="modal fade" role="dialog">
                          <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Vacature wijzigen</h4>
                              </div>
                              <div class="modal-body">
                                <form name="vacature" id="vacature" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" method="post" autocomplete="off" class="form-horizontal">
                                    <span>Naam vacature<br /></span><input type="text" name="naam" value="' . $row['job_name'] . '" class="form-control"/><br />
                                    <span>Beschrijving vacature<br /></span><textarea name="beschrijving" value="" class="form-control">'.str_replace("<br />", "\n", $row["job_description"]).  '</textarea><br />
                                    <input type="hidden" value="' . $parkid . '" name="id"/>
                                    <input type="hidden" value="' . $id . '" name="job_id"/>
                                    <button type="submit" class="btn btn-raised btn-success" name="editvacature" id="editvacature">Opslaan
                                  </button>
                                </form>
                              </div>
                            </div>

                          </div>
                        </div>
                        ';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>Er zijn momenteel geen vacatures</p>';
        }
        echo '
                      <button type="button" class="btn-info" data-toggle="modal" data-target="#vacature">Vacature toevoegen</button>
                       <div id="vacature" class="modal fade" role="dialog">
                          <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Vacature toevoegen</h4>
                              </div>
                              <div class="modal-body">
                                <form name="vacature" id="vacature" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" method="post" autocomplete="off" class="form-horizontal">
                                    <span>Naam vacature<br /></span><input type="text" name="naam" value="" class="form-control"/><br />
                                    <span>Beschrijving vacature<br /></span><textarea name="beschrijving" value="" class="form-control"></textarea><br />
                                    <input type="hidden" value="' . $parkid . '" name="id"/>
                                    <button type="submit" class="btn btn-raised btn-success" name="addvacature" id="addvacature">Toevoegen
                                  </button>
                                </form>
                              </div>
                            </div>

                          </div>
                        </div>
                ';
    }
    static function loadVacaturesStaff($mysqli, $pageid)
    {
        $pageposts = $pageid*50;
        $sql = "SELECT * FROM pco_parks_jobs ORDER BY ID DESC LIMIT $pageposts, 50";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            echo '
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Sollicitaties</th>
                            <th>Opties</th>
                        </tr>
                    </thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row["ID"];
                $parkid = $row['park_id'];
                $deleted = $row['deleted'];
                echo '<tr>';
                echo '<td>' . $row['job_name'] . '</td>';
                echo '<td>' . vacature::countApplications($mysqli, $id) . ' sollicitant(en)<br/><a href="staff.php?applications=&id=' . $row["ID"] . '">Sollicitaties bekijken</a></td>';
                if($deleted == 2 || $deleted == 1) {
                    echo '<td><a href="staff.php?applications=&undoremovevacature=' . $row["ID"] . '&parkid='.$row["park_id"].'&pi='.($pageid+1).'" class="btn btn-info btn-sm">Verwijderen ongedaan maken</a></td>';
                } else {
                    echo '<td><a href="staff.php?applications=&removevacature=' . $row["ID"] . '&parkid='.$row["park_id"].'&pi='.($pageid+1).'" class="btn btn-danger btn-sm">Verwijderen</a></td>';
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            $sql1="SELECT * FROM pco_parks_jobs WHERE deleted='0'";
            $result1 = mysqli_query($mysqli, $sql1);
            $count1 = mysqli_num_rows($result1);
            if($count1 > ($pageid+1)*50) {
                if($pageid > 0) {
                    echo '<a href="staff.php?applications=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                }
                echo '<a href="staff.php?applications=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
            } else {
                if ($pageid > 0) {
                    echo '<a href="staff.php?applications=&page=' . ($pageid) . '" class="btn btn-danger btn-sm">Terug</a>';
                    if ($count1 > ($pageid + 1) * 50) {
                        echo '<a href="staff.php?applications=&page=' . ($pageid + 2) . '" class="btn btn-danger btn-sm">Volgende</a>';
                    }
                }
            }
        } else {
            echo '<p>Er zijn momenteel geen vacatures</p>';
        }
    }
    static function addVacature($mysqli, $parkid, $name, $body)
    {
        setlocale(LC_TIME, 'NL_nl');

        $dt = new DateTime();
        $dt->add(new DateInterval('P10D'));
        $expires = $dt->format('d-m-Y');

        $name = strip_tags($name);
        $sql = "INSERT INTO pco_parks_jobs (park_id, job_name, job_description, job_status, expires) VALUES ('$parkid', '$name', '', '0', '$expires');";
        $result = mysqli_query($mysqli, $sql);

        $sql1 = "SELECT * FROM pco_parks_jobs WHERE park_id='$parkid' AND job_name='$name' order by ID desc";
        $result1 = mysqli_query($mysqli, $sql1);
        $row1 = mysqli_fetch_assoc($result1);
        $splitted = str_split($body, 1);
        for ($i = 0; $i < count($splitted); $i++) {
            $sql3 = "SELECT * FROM pco_parks_jobs WHERE park_id='$parkid' AND job_name='$name' order by ID desc";
            $result3 = mysqli_query($mysqli, $sql3);
            $row3 = mysqli_fetch_assoc($result3);
            $oldbody = $row3['job_description'];

            $id = $row1['ID'];
            $newbody = $oldbody . $splitted[$i];
            $sql2 = "UPDATE pco_parks_jobs SET job_description = '$newbody' WHERE ID='$id'";
            $result2 = mysqli_query($mysqli, $sql2);
        }
    }
    static function editVacature($mysqli, $parkid, $name, $body, $jobid)
    {
        if (park::CanManageJobs($mysqli, $parkid, $_SESSION['UUID']) || staff::canManageParks($mysqli, $_SESSION['UUID'])) {
            $sql = "UPDATE pco_parks_jobs SET job_name='$name', job_description='' WHERE park_id='$parkid' AND ID='$jobid' ";
            $result = mysqli_query($mysqli, $sql);

            $splitted = str_split($body, 1);
            for ($i = 0; $i < count($splitted); $i++) {
                $sql3 = "SELECT * FROM pco_parks_jobs WHERE park_id='$parkid' AND ID='$jobid' order by ID desc";
                $result3 = mysqli_query($mysqli, $sql3);
                $row3 = mysqli_fetch_assoc($result3);
                $oldbody = $row3['job_description'];

                $newbody = $oldbody . $splitted[$i];
                $sql2 = "UPDATE pco_parks_jobs SET job_description = '$newbody' WHERE ID='$jobid'";
                $result2 = mysqli_query($mysqli, $sql2);
            }
            return true;
        }
        return false;
    }
    static function removeVacature($mysqli, $parkid, $jobid)
    {
        if (park::CanManageJobs($mysqli, $parkid, $_SESSION['UUID']) || staff::canManageApplications($mysqli, $_SESSION['UUID'])) {
            $sql = "UPDATE pco_parks_jobs SET deleted='1' WHERE park_id='$parkid' AND ID='$jobid' ";
            $result = mysqli_query($mysqli, $sql);
        }
    }
    static function unremoveVacature($mysqli, $parkid, $jobid)
    {
        if (park::CanManageJobs($mysqli, $parkid, $_SESSION['UUID'])  || staff::canManageApplications($mysqli, $_SESSION['UUID'])) {
            $sql = "UPDATE pco_parks_jobs SET deleted='0' WHERE park_id='$parkid' AND ID='$jobid' ";
            $result = mysqli_query($mysqli, $sql);
        }
    }
    static function excist($mysqli, $jobid)
    {
        $sql = "SELECT * FROM pco_parks_jobs WHERE ID='$jobid' AND deleted='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    static function getName($mysqli, $jobid)
    {
        $sql = "SELECT * FROM pco_parks_jobs WHERE ID='$jobid' AND deleted='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if ($count > 0) {
            return $row['job_name'];
        } else {
            return false;
        }
    }
    static function apply($mysqli, $naam, $about, $reden, $email, $skype, $kennis, $extra, $job_id)
    {
        $naam = strip_tags($naam);
        $reden = strip_tags($reden);
        $email = strip_tags($email);
        $skype = strip_tags($skype);
        $kennis = strip_tags($kennis);
        $about = strip_tags($about);
        $extra = strip_tags($extra);
        $uuid = $_SESSION['UUID'];
        $sql = "INSERT INTO pco_parks_jobs_candidates (user, job_id, name, about, email, skype, knowledge, reason, extra) VALUES ('$uuid', '$job_id', '$naam', ' ', '$email', '$skype', ' ', ' ', ' ');";
        $result = mysqli_query($mysqli, $sql);

        $sql1 = "SELECT * FROM pco_parks_jobs_candidates WHERE job_id='$job_id' AND user='$uuid' order by ID desc";
        $result1 = mysqli_query($mysqli, $sql1);
        $row1 = mysqli_fetch_assoc($result1);
        $splitted = str_split($about, 1);
        for ($i = 0; $i < count($splitted); $i++) {
            $sql3 = "SELECT * FROM pco_parks_jobs_candidates WHERE job_id='$job_id' AND user='$uuid' order by ID desc";
            $result3 = mysqli_query($mysqli, $sql3);
            $row3 = mysqli_fetch_assoc($result3);
            $oldabout = $row3['about'];

            $id = $row1['ID'];
            $newabout = $oldabout . $splitted[$i];
            $sql2 = "UPDATE pco_parks_jobs_candidates SET about = '$newabout' WHERE ID='$id'";
            $result2 = mysqli_query($mysqli, $sql2);
        }

        $sql1 = "SELECT * FROM pco_parks_jobs_candidates WHERE job_id='$job_id' AND user='$uuid' order by ID desc";
        $result1 = mysqli_query($mysqli, $sql1);
        $row1 = mysqli_fetch_assoc($result1);
        $splitted = str_split($kennis, 1);
        for ($i = 0; $i < count($splitted); $i++) {
            $sql3 = "SELECT * FROM pco_parks_jobs_candidates WHERE job_id='$job_id' AND user='$uuid' order by ID desc";
            $result3 = mysqli_query($mysqli, $sql3);
            $row3 = mysqli_fetch_assoc($result3);
            $oldknowledge = $row3['knowledge'];

            $id = $row1['ID'];
            $newknowledge = $oldknowledge . $splitted[$i];
            $sql2 = "UPDATE pco_parks_jobs_candidates SET knowledge = '$newknowledge' WHERE ID='$id'";
            $result2 = mysqli_query($mysqli, $sql2);
        }

        $sql1 = "SELECT * FROM pco_parks_jobs_candidates WHERE job_id='$job_id' AND user='$uuid' order by ID desc";
        $result1 = mysqli_query($mysqli, $sql1);
        $row1 = mysqli_fetch_assoc($result1);
        $splitted = str_split($reden, 1);
        for ($i = 0; $i < count($splitted); $i++) {
            $sql3 = "SELECT * FROM pco_parks_jobs_candidates WHERE job_id='$job_id' AND user='$uuid' order by ID desc";
            $result3 = mysqli_query($mysqli, $sql3);
            $row3 = mysqli_fetch_assoc($result3);
            $oldreason = $row3['reason'];

            $id = $row1['ID'];
            $newreason = $oldreason . $splitted[$i];
            $sql2 = "UPDATE pco_parks_jobs_candidates SET reason = '$newreason' WHERE ID='$id'";
            $result2 = mysqli_query($mysqli, $sql2);
        }

        $sql1 = "SELECT * FROM pco_parks_jobs_candidates WHERE job_id='$job_id' AND user='$uuid' order by ID desc";
        $result1 = mysqli_query($mysqli, $sql1);
        $row1 = mysqli_fetch_assoc($result1);
        $splitted = str_split($extra, 1);
        for ($i = 0; $i < count($splitted); $i++) {
            $sql3 = "SELECT * FROM pco_parks_jobs_candidates WHERE job_id='$job_id' AND user='$uuid' order by ID desc";
            $result3 = mysqli_query($mysqli, $sql3);
            $row3 = mysqli_fetch_assoc($result3);
            $oldextra = $row3['extra'];

            $id = $row1['ID'];
            $newextra = $oldextra . $splitted[$i];
            $sql2 = "UPDATE pco_parks_jobs_candidates SET extra = '$newextra' WHERE ID='$id'";
            $result2 = mysqli_query($mysqli, $sql2);
        }
    }
    static function countApplications($mysqli, $job_id)
    {
        $sql = "SELECT * FROM pco_parks_jobs_candidates WHERE job_id='$job_id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
    static function loadApplications($mysqli, $job_id, $parkid)
    {
        $sql = "SELECT * FROM pco_parks_jobs_candidates WHERE job_id='$job_id' ORDER BY ID desc";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            echo '<div class="panel-group" id="accordion">';
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['ID'];
                $name = $row['name'];
                echo '<div class="panel panel-info">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#' . $id . '">Sollicitant: ' . $name . '</a><a href="parksettings.php?id=' . $parkid . '&jobs=&removeapplication=' . $id . '&pi='.($job_id).'" class="btn btn-danger btn-sm">Verwijderen</a><br/>
                                </h4>
                            </div>
                            <div id="' . $id . '" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <h3>Informatie over zichzelf</h3><br/>
                                    <p>' . $row['about'] . '</p><br/>
                                    <h3>Kennis</h3><br/>
                                    <p>' . $row['knowledge'] . '</p><br/>
                                    <h3>Motivatie</h3><br/>
                                    <p>' . $row['reason'] . '</p><br/>
                                    <h3>Extra informatie</h3><br/>
                                    <p>' . $row['extra'] . '</p><br/>
                                    <h3>Contact gegevens</h3>
                                    <p>Email: ' . $row['email'] . '</p><br/>
                                    <p>Skype: ' . $row['skype'] . '</p><br/>
                                </div>
                            </div>
                        </div>';
            }
            echo "</div>";
        }
    }
    static function loadApplicationsStaff($mysqli, $job_id)
    {
        $sql = "SELECT * FROM pco_parks_jobs_candidates WHERE job_id='$job_id' ORDER BY ID desc";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            echo '<div class="panel-group" id="accordion">';
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['ID'];
                $name = $row['name'];
                echo '<div class="panel panel-info">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#' . $id . '">Sollicitant: ' . $name . '</a><a href="staff.php?applications=&removeapplication=' . $id . '&pi='.($job_id).'" class="btn btn-danger btn-sm">Verwijderen</a><br />

                                </h4>
                            </div>
                            <div id="' . $id . '" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <h3>Informatie over zichzelf</h3><br/>
                                    <p>' . $row['about'] . '</p><br/>
                                    <h3>Kennis</h3><br/>
                                    <p>' . $row['knowledge'] . '</p><br/>
                                    <h3>Motivatie</h3><br/>
                                    <p>' . $row['reason'] . '</p><br/>
                                    <h3>Extra informatie</h3><br/>
                                    <p>' . $row['extra'] . '</p><br/>
                                    <h3>Contact gegevens</h3>
                                    <p>Email: ' . $row['email'] . '</p><br/>
                                    <p>Skype: ' . $row['skype'] . '</p><br/>
                                </div>
                            </div>
                        </div>';
            }
            echo "</div>";
        }
    }
    static function loadAllVacatures($mysqli)
    {
        $sql = "SELECT * FROM pco_parks_jobs WHERE deleted='0' AND park_id='18'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $name = $row['job_name'];
                $description = $row['job_description'];
                $id = $row['ID'];
                $parkid = $row['park_id'];
                $parkname = park::getName($mysqli, $parkid);
                $parklogo = park::getLogo($mysqli, $parkid);
                echo '
                        <div class="hover" id="' . $id . '">
                            <div>
                                <img class="avatar" src="' . $parklogo . '" alt=""/>
                                <a href="park.php?id=' . $row['park_id'] . '" style="color: black; font-weight: bold;"><span>' . $parkname . '</span></a>
                            </div>
                            <div>
                                <h4>Vacature: ' . $name . '</h4>
                                <h5>' . $description . '</h5>
                            </div>
                            <script>
                                var id' . $id . ' = document.getElementById("' . $id . '");

                                id' . $id . '.onclick = function() {
                                    window.location.href = "vacature.php?id=' . $id . '";
                                };
                            </script>
                                <span>' . vacature::countApplications($mysqli, $id) . ' sollicitant(en)</span>
                        </div>
                        <hr />

                        ';
            }
        }
        $randomrow = array(
            "expires",
        );
        $randomorder = array(
            "desc"
        );
        $ranrow = $randomrow[array_rand($randomrow,1)];
        $ranorder = $randomorder[array_rand($randomorder,1)];
        $sql = "SELECT * FROM pco_parks_jobs WHERE deleted='0' AND park_id NOT IN (18) order by ".$ranrow." ".$ranorder;
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $name = $row['job_name'];
                $description = $row['job_description'];
                $id = $row['ID'];
                $parkid = $row['park_id'];
                $parkname = park::getName($mysqli, $parkid);
                $parklogo = park::getLogo($mysqli, $parkid);

                $datetime1 = new DateTime();

                $datetime2 = new DateTime($row['expires']);
                $difference = $datetime1->diff($datetime2);
                if($datetime1 < $datetime2) {
                    echo '
                            <div class="hover" id="' . $id . '">
                                <div>
                                    <img class="avatar" src="' . $parklogo . '" alt=""/>
                                    <a href="park.php?id=' . $row['park_id'] . '" style="color: black; font-weight: bold;"><span>' . $parkname . '</span></a>
                                </div>
                                <div>
                                    <h4>Vacature: ' . $name . '</h4>
                                    <h5>' . $description . '</h5>
                                </div>
                                <script>
                                    var id' . $id . ' = document.getElementById("' . $id . '");

                                    id' . $id . '.onclick = function() {
                                        window.location.href = "vacature.php?id=' . $id . '";
                                    };
                                </script>
                                    <span>' . vacature::countApplications($mysqli, $id) . ' sollicitant(en)</span>
                                    <span class="pull-right">Vacature verloopt over ' . $difference->d . ' dag(en)</span>
                            </div>
                            <hr />

                            ';
                }
            }
        } else {
            echo '
                        <div class="jumbotron">
                            <p>Geen vacatures gevonden!</p>
                        </div>


                        ';
        }
    }
    static function CountAllVacatures($mysqli)
    {
        $sql = "SELECT * FROM pco_parks_jobs WHERE deleted='0'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $counts = 0;
        while($row = mysqli_fetch_assoc($result)) {
            $datetime1 = new DateTime();

            $datetime2 = new DateTime($row['expires']);
            $difference = $datetime1->diff($datetime2);
            if($datetime1 < $datetime2) {
                $counts++;
            }
        }
        return $counts;
    }
    static function removeApplication($mysqli, $id) {
        $sql = "DELETE FROM pco_parks_jobs_candidates WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
    }
    static function loadAllVacaturesSearch($mysqli, $keywords)
    {
        $sql = "SELECT * FROM pco_parks_jobs WHERE deleted='0' AND job_name LIKE '%{$keywords}%' OR job_description LIKE '%{$keywords}%'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $name = $row['job_name'];
                $description = $row['job_description'];
                $id = $row['ID'];
                $parkid = $row['park_id'];
                $parkname = park::getName($mysqli, $parkid);
                $parklogo = park::getLogo($mysqli, $parkid);


                $datetime1 = new DateTime();

                $datetime2 = new DateTime($row['expires']);
                $difference = $datetime1->diff($datetime2);
                if($datetime1 < $datetime2) {
                    echo '
                            <div class="jumbotron hover" id="' . $id . '">
                                <div>
                                    <img class="avatar" src="' . $parklogo . '" alt=""/>
                                    <a href="park.php?id=' . $row['park_id'] . '" style="color: black; font-weight: bold;"><span>' . $parkname . '</span></a>
                                </div>
                                <div>
                                    <h4>Vacature: ' . $name . '</h4>
                                    <h5>' . $description . '</h5>
                                </div>
                                <script>
                                    var id' . $id . ' = document.getElementById("' . $id . '");

                                    id' . $id . '.onclick = function() {
                                        window.location.href = "vacature.php?id=' . $id . '";
                                    };
                                </script>
                                <span>' . vacature::countApplications($mysqli, $id) . ' sollicitant(en)</span>
                                <span class="pull-right">Vacature verloopt over '.$difference->d.' dag(en)</span>
                            </div>


                            ';
                }
            }
        } else {
            echo '
                        <div class="jumbotron">
                            <p>Geen vacatures gevonden!</p>
                        </div>


                        ';
        }
    }
    static function opnieuwAanbieden($mysqli, $vacatureid) {
        setlocale(LC_TIME, 'NL_nl');
        $dt = new DateTime();
        $dt->add(new DateInterval('P10D'));
        $expires = $dt->format('d-m-Y');
        $sql = "UPDATE pco_parks_jobs SET expires = '$expires' WHERE ID='$vacatureid'";
        mysqli_query($mysqli, $sql);
    }
}
class userpage {
    static function exist($mysqli, $id) {
        $sql="SELECT * FROM pco_users WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            return true;
        } else {
            return false;
        }
        return false;
    }
    static function getName($mysqli, $id) {
        $sql="SELECT * FROM pco_users WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['name'];
        }
    }
    static function getProfilePicture($mysqli, $id) {
        $sql="SELECT * FROM pco_users WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            if(empty($row['profile_picture'])) {
                return 'resources/defaultavatar.png';
            }
            return $row['profile_picture'];
        }
    }
    static function getAbout($mysqli, $id) {
        $sql="SELECT * FROM pco_users WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            return $row['profile_about'];
        }
    }
    static function getMC($mysqli, $id) {
        $sql="SELECT * FROM pco_users WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        $row = mysqli_fetch_assoc($result);
        if($count > 0) {
            if(empty($row['profile_mc'])) {
                return 'n.v.t.';
            }
            return $row['profile_mc'];
        }
    }
    static function getActiveJobsOnParkCraft($mysqli, $id) {
        $counting = 0;
        echo '
                <table class="table">
                    <thead>
                        <tr>
                            <th>Park</th>
                            <th>Functie</th>
                        </tr>
                    </thead>';
        echo '<tbody>';
        $uuid = user::getUUIDFromID($mysqli, $id);
        $sql="SELECT * FROM pco_parks_staff WHERE uuid='$uuid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                if(!park::isDeleted($mysqli, $row["park_id"])) {
                    echo '<tr>
                      <td><img src="' . park::getLogo($mysqli, $row["park_id"]) . '" alt="" class="avatar"/><a href="park.php?id=' . $row["park_id"] . '" class="">' . park::getName($mysqli, $row["park_id"]) . '</a></td>
                      <td><span>' . $row['prefix'] . '</span></td>
                      </tr>
                    ';
                    $counting++;
                }
            }
        }
        $sql="SELECT * FROM pco_parks WHERE owner='$uuid'";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if($count > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                if(!park::isDeleted($mysqli, $row["ID"])) {

                    echo '<tr>
                      <td><img src="' . park::getLogo($mysqli, $row["ID"]) . '" alt="" class="avatar"/><a href="park.php?id=' . $row["ID"] . '" class="">' . park::getName($mysqli, $row["ID"]) . '</a></td>
                      <td><span>Eigenaar</span></td>
                      </tr>
                    ';
                    $counting++;
                }
            }
        }
        if($counting == 0){
            echo '<td><span>Momenteel nergens werkzaam.</span></td>';
        }
        echo '</tbody>';
        echo '</table>';
    }
    static function update($mysqli, $id, $picture, $about, $mcname) {
        $picturesql = '';
        if(!$picture == '') {
            $picturesql = "profile_picture='$picture',";
        }
        $mcname = strip_tags($mcname);

        $sql = "UPDATE pco_users SET ".$picturesql." profile_mc='$mcname', profile_about='$about' WHERE ID='$id'";
        $result = mysqli_query($mysqli, $sql);
        return $result;
    }
    static function loadReactionsOfUser($mysqli, $userid) {
        $uuid = user::getUUIDFromID($mysqli, $userid);
        $sql = "SELECT * FROM pco_reaction WHERE uuid='$uuid' ORDER BY ID DESC LIMIT 5";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        while($row = mysqli_fetch_assoc($result)) {
            $articletitle = article::getTitle($mysqli, $row['article_id']);
            echo '
                <h4><a href="article.php?id='.$row['article_id'].'">'.$articletitle.'</a></h4>
                <p>' . $row['reaction'] . '</p>
                <hr />';
        }
    }
}
class plugins {
    static function load($mysqli) {
        $randomrow = array(
            "ID",
            "logo",
            "name",
            "description",
            "url"
        );
        $randomorder = array(
            "asc",
            "desc"
        );
        $ranrow = $randomrow[array_rand($randomrow,1)];
        $ranorder = $randomorder[array_rand($randomorder,1)];
        $sql="SELECT * FROM pco_plugins order by ".$ranrow." ".$ranorder;
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $code = common::random(30);
                echo '
                <div class="col-md-12 hover" id="' . $code . '">
                    <div class="col-md-1">
                        <img class="avatar pull-left" src="' . $row['logo'] . '" alt=""/><br />
                    </div>
                    <div class="col-md-11">
                        <a style="font-weight: bold;" href="//' . $row['url'] . '">' . $row['name'] . '</a><br />
                        <p style="color: grey;">' . $row['description'] . '</p>
                        <span>Auteur: </span><a style="font-weight: bold;" href="//' . $row['author_link'] . '">' . $row['author'] . '</a><br />
                    </div>

                </div>
                <script>
                $(\'#' . $code . '\').on(\'click\', function (e) {
                    window.location.href = "//' . $row['url'] . '";
                })
                </script>
                <br /><br /><br /><hr />';
            }
        } else {
            echo '<p>Geen plugins beschikbaar.</p>';
        }
    }
    static function plugincount($mysqli) {
        $sql="SELECT * FROM pco_plugins";
        $result = mysqli_query($mysqli, $sql);
        $count = mysqli_num_rows($result);
        return $count;
    }
}
?>
