<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 10-2-2017
 * Time: 15:50
 */
include 'includes/connectdb.php';
include 'includes/PCO_API.php';
if(isset($_GET['key']) && isset($_GET['ride_code']) && isset($_GET['status'])) {
    $key = preg_replace("/[^a-zA-Z0-9]+/", "",str_replace(' ', '', preg_replace('/[[:^print:]]/', '', strip_tags($_GET['key']))));
    $ride_code = preg_replace("/[^a-zA-Z0-9]+/", "",str_replace(' ', '', preg_replace('/[[:^print:]]/', '', strip_tags($_GET['ride_code']))));
    $status = preg_replace("/[^a-zA-Z0-9]+/", "",str_replace(' ', '', preg_replace('/[[:^print:]]/', '', strip_tags($_GET['status']))));
    $key = trim($key);
    $key = strip_tags($key);
    $key = mysqli_real_escape_string($mysqli, $key);
    $ride_code = trim($ride_code);
    $ride_code = strip_tags($ride_code);
    $ride_code = mysqli_real_escape_string($mysqli, $ride_code);
    $status = trim($status);
    $status = strip_tags($status);
    $status = mysqli_real_escape_string($mysqli, $status);
    if(API::keyExist($mysqli, $key)) {
        if(!rides::rideExist($mysqli, $ride_code)){
            $error = '{"Error":true, "Message": Ride code is incorrect}';
            echo $error;
        } else {
            $good = false;
            $ride = true;
            $parkid = park::getParkIdFromAPIKey($mysqli, $key);
            if (!rides::isRideOfPark($mysqli, $parkid, $ride_code)) {
                $error = '{"Error":true, "Message": This ride is not of this park}';
                echo $error;
            } else {
                if ($status == '') {
                    $good = false;
                } else {
                    if ($status == 0) {
                        rides::updateStatus($mysqli, $ride_code, 0, $key);
                        $good = true;
                    }
                    if ($status == 1) {
                        rides::updateStatus($mysqli, $ride_code, 1, $key);
                        $good = true;
                    }
                    if ($status == 2) {
                        rides::updateStatus($mysqli, $ride_code, 2, $key);
                        $good = true;
                    }
                    if ($status == 3) {
                        rides::updateStatus($mysqli, $ride_code, 3, $key);
                        $good = true;
                    }
                    if ($status == 4) {
                        rides::updateStatus($mysqli, $ride_code, 4, $key);
                        $good = true;
                    }
                    if ($status == 5) {
                        rides::updateStatus($mysqli, $ride_code, 5, $key);
                    }
                }
                if ($good) {
                    $error = '{"Error":false, "Message": Status updated}';
                    echo $error;
                } else {
                    $error = '{"Error":true, "Message": Status is incorrect}';
                    echo $error;
                }
            }
        }
    } else {
        $error = '{"Error":true, "Message": Key is incorrect}';
        echo $error;
    }
}
if(isset($_GET['key']) && isset($_GET['show_code']) && isset($_GET['getstatus'])) {
    $key = preg_replace("/[^a-zA-Z0-9]+/", "",str_replace(' ', '', preg_replace('/[[:^print:]]/', '', strip_tags($_GET['key']))));
    $ride_code = preg_replace("/[^a-zA-Z0-9]+/", "",str_replace(' ', '', preg_replace('/[[:^print:]]/', '', strip_tags($_GET['show_code']))));
    $key = trim($key);
    $key = strip_tags($key);
    $key = mysqli_real_escape_string($mysqli, $key);
    $ride_code = trim($ride_code);
    $ride_code = strip_tags($ride_code);
    $ride_code = mysqli_real_escape_string($mysqli, $ride_code);
    if(API::keyExist($mysqli, $key)) {
        if (!rides::rideExist($mysqli, $ride_code)) {
            $error = '{"Error":true, "Message": Ride code is incorrect}';
            echo $error;
        } else {
            $good = false;
            $ride = true;
            $parkid = park::getParkIdFromAPIKey($mysqli, $key);
            if (!rides::isRideOfPark($mysqli, $parkid, $ride_code)) {
                $error = '{"Error":true, "Message": This show is not of this park}';
                echo $error;
            } else {
                $status = '{"Show":'.$_GET['show_code'].', "Status": '.rides::getShowStatus($mysqli, $ride_code).'}';
                echo $status;
            }
        }
    }
}
if(isset($_GET['key']) && isset($_GET['ride_code']) && isset($_GET['getstatus'])) {
    $key = preg_replace("/[^a-zA-Z0-9]+/", "",str_replace(' ', '', preg_replace('/[[:^print:]]/', '', strip_tags($_GET['key']))));
    $ride_code = preg_replace("/[^a-zA-Z0-9]+/", "",str_replace(' ', '', preg_replace('/[[:^print:]]/', '', strip_tags($_GET['ride_code']))));
    $key = trim($key);
    $key = strip_tags($key);
    $key = mysqli_real_escape_string($mysqli, $key);
    $ride_code = trim($ride_code);
    $ride_code = strip_tags($ride_code);
    $ride_code = mysqli_real_escape_string($mysqli, $ride_code);
    if(API::keyExist($mysqli, $key)) {
        if (!rides::rideExist($mysqli, $ride_code)) {
            $error = '{"Error":true, "Message": Ride code is incorrect}';
            echo $error;
        } else {
            $parkid = park::getParkIdFromAPIKey($mysqli, $key);
            if (!rides::isRideOfPark($mysqli, $parkid, $ride_code)) {
                $error = '{"Error":true, "Message": This ride is not of this park}';
                echo $error;
            } else {
                $status = '{"Ride":'.$_GET['ride_code'].', "Status": '.rides::getRideStatus($mysqli, $ride_code).'}';
                echo $status;
            }
        }
    } else {
        $error = '{"Error":true, "Message": Key is incorrect}';
        echo $error;
    }
}
if(isset($_GET['key']) && isset($_GET['getrides'])) {
    $key = preg_replace("/[^a-zA-Z0-9]+/", "",str_replace(' ', '', preg_replace('/[[:^print:]]/', '', strip_tags($_GET['key']))));
    $key = trim($key);
    $key = strip_tags($key);
    $key = mysqli_real_escape_string($mysqli, $key);
    if(API::keyExist($mysqli, $key)) {
        $parkid = park::getParkIdFromAPIKey($mysqli, $key);
        rides::getRides($mysqli, $parkid);
    } else {
        $error = '{"Error":true, "Message": Key is incorrect}';
        echo $error;
    }
}
if(isset($_GET['key']) && isset($_GET['getshows'])) {
    $key = preg_replace("/[^a-zA-Z0-9]+/", "",str_replace(' ', '', preg_replace('/[[:^print:]]/', '', strip_tags($_GET['key']))));
    $key = trim($key);
    $key = strip_tags($key);
    $key = mysqli_real_escape_string($mysqli, $key);
    if(API::keyExist($mysqli, $key)) {
        $parkid = park::getParkIdFromAPIKey($mysqli, $key);
        rides::getShows($mysqli, $parkid);
    } else {
        $error = '{"Error":true, "Message": Key is incorrect}';
        echo $error;
    }
}
if(isset($_GET['key']) && isset($_GET['show_code']) && isset($_GET['status'])) {
    $key = preg_replace("/[^a-zA-Z0-9]+/", "",str_replace(' ', '', preg_replace('/[[:^print:]]/', '', strip_tags($_GET['key']))));
    $ride_code = preg_replace("/[^a-zA-Z0-9]+/", "",str_replace(' ', '', preg_replace('/[[:^print:]]/', '', strip_tags($_GET['show_code']))));
    $status = preg_replace("/[^a-zA-Z0-9]+/", "",str_replace(' ', '', preg_replace('/[[:^print:]]/', '', strip_tags($_GET['status']))));
    $key = trim($key);
    $key = strip_tags($key);
    $key = mysqli_real_escape_string($mysqli, $key);
    $ride_code = trim($ride_code);
    $ride_code = strip_tags($ride_code);
    $ride_code = mysqli_real_escape_string($mysqli, $ride_code);
    $status = trim($status);
    $status = strip_tags($status);
    $status = mysqli_real_escape_string($mysqli, $status);
    if(API::keyExist($mysqli, $key)) {
        if(!rides::rideExist($mysqli, $ride_code)){
            $error = '{"Error":true, "Message": Ride code is incorrect}';
            echo $error;
        } else {
            $good = false;
            $ride = true;
            $parkid = park::getParkIdFromAPIKey($mysqli, $key);
            if (!rides::isRideOfPark($mysqli, $parkid, $ride_code)) {
                $error = '{"Error":true, "Message": This show is not of this park}';
                echo $error;
            } else {
                if ($status == '') {
                    $good = false;
                } else {
                    if ($status == 0) {
                        rides::updateShowStatus($mysqli, $ride_code, 0, '', $key);
                        $good = true;
                    }
                    if ($status == 1) {
                        rides::updateShowStatus($mysqli, $ride_code, 1, '', $key);
                        $good = true;
                    }
                    if ($status == 2) {
                        if(isset($_GET['time']) && strlen($_GET['time']) == 5) {
                            rides::updateShowStatus($mysqli, $ride_code, 2, $_GET['time'], $key);
                            $good = true;
                        } else {
                            $error = '{"Error":true, "Message": Time is not in 24-hour format of an hour with leading zeros}';
                            echo $error;
                            exit;
                        }
                    }
                    if ($status == 3) {
                        rides::updateShowStatus($mysqli, $ride_code, 3, '', $key);
                        $good = true;
                    }
                }
                if ($good) {
                    $error = '{"Error":false, "Message": Status updated}';
                    echo $error;
                } else {
                    $error = '{"Error":true, "Message": Status is incorrect}';
                    echo $error;
                }
            }
        }
    } else {
        $error = '{"Error":true, "Message": Key is incorrect}';
        echo $error;
    }
}
mysqli_close($mysqli);?>