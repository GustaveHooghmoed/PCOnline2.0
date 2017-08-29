<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 16-3-2017
 * Time: 16:19
 */
ob_start();
session_start();
require 'includes/phpimports.php';
if(!$cvwl) {
    if (!isset($_SESSION['user'])) {
        header("Location: index.php?redirect=".urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
    if (!user::hasAccess($mysqli, $_SESSION['UUID'])) {
        header("Location: index.php");
        exit;
    }
    if (!user::isActivated($mysqli, $_SESSION['UUID'])) {
        header("Location: index.php");
        exit;
    }
}
if(system::isMaintenanceModeOn($mysqli)) {
    user::logout();
    header("Location: index.php?warning=Onderhoudsmode is ingeschakeld. Meer info? Volg ons op <a href=\"https://www.facebook.com/ParkCraft-370915049752819/\" class=\"soc-btn fb\">Facebook</a>, <a href=\"https://twitter.com/ParkenCraft\" class=\"soc-btn tw\">Twitter</a> en <a href=\"https://www.youtube.com/ParkCraft\" class=\"soc-btn gp\">YouTube</a>");
    exit;
}
user::setLastExcecution($mysqli);
if(isset($_GET['loadchats'])) {
    chats::loadChats($mysqli, $_SESSION['UUID']);
}else if(isset($_GET['chat'])) {
    $chatid = trim($_GET['chat']);
    $chatid = strip_tags($chatid);
    $chatid = mysqli_real_escape_string($mysqli, $chatid);
    chats::loadChatReload($mysqli, $chatid);
}else if(isset($_GET['sendchat'])) {
    $chatid = trim($_GET['sendchat']);
    $chatid = strip_tags($chatid);
    $chatid = mysqli_real_escape_string($mysqli, $chatid);
    $message = urldecode($_GET['message']);
    $message = trim($message);
    $message = strip_tags($message);
    $message = mysqli_real_escape_string($mysqli, $message);

    chats::sendMessage($mysqli, $chatid, $message);
}else if(isset($_GET['chatcount'])) {
    echo chats::countNotReadedMessages($mysqli, $_SESSION['UUID']);
}else if(isset($_GET['search'])) {
    $search = urldecode($_GET['search']);
    $search = trim($search);
    $search = strip_tags($search);
    $search = mysqli_real_escape_string($mysqli, $search);

    echo chats::newChat($mysqli, $_GET['search']);
} else {
    header("Location: messenger.php");
    exit;
}
exit;