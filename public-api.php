<?php
/**
 * Created by PhpStorm.
 * User: daniq
 * Date: 28-2-2017
 * Time: 20:02
 */
include 'includes/connectdb.php';
include 'includes/PCO_API.php';
if(isset($_REQUEST['parkid'])) {
    if(park::exist($mysqli, $_REQUEST['parkid'])) {
        $id = $_REQUEST['parkid'];
        $id = strip_tags($id);
        $id = trim($id);
        $id = mysqli_real_escape_string($mysqli, $id);
        if(isset($_REQUEST['followers'])) {
            $sql="SELECT ID, followers FROM pco_parks WHERE ID='$id'";
            $result=mysqli_query($mysqli,$sql);
            $count=mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);
            $followers = explode(",", $row['followers']);

            $json = array('Error' => false, 'ID' => "$id", 'Followers' => count($followers)-1);
            $jsonencoded = json_encode($json);
            echo $jsonencoded;
        } else if(isset($_REQUEST['banner'])) {
            $sql="SELECT ID, header FROM pco_parks WHERE ID='$id'";
            $result=mysqli_query($mysqli,$sql);
            $count=mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);

            $json = array('Error' => false, 'ID' => "$id", 'Banner' => $row['header']);
            $jsonencoded = json_encode($json);
            echo $jsonencoded;
        } else if(isset($_REQUEST['logo'])) {
            $sql="SELECT ID, logo FROM pco_parks WHERE ID='$id'";
            $result=mysqli_query($mysqli,$sql);
            $count=mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);

            $json = array('Error' => false, 'ID' => "$id", 'Logo' => $row['logo']);
            $jsonencoded = json_encode($json);
            echo $jsonencoded;
        } else if(isset($_REQUEST['ip'])) {
            $sql="SELECT ID, ip FROM pco_parks WHERE ID='$id'";
            $result=mysqli_query($mysqli,$sql);
            $count=mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);

            $json = array('Error' => false, 'ID' => "$id", 'IP' => $row['ip']);
            $jsonencoded = json_encode($json);
            echo $jsonencoded;
        } else if(isset($_REQUEST['description'])) {
            $sql="SELECT ID, description FROM pco_parks WHERE ID='$id'";
            $result=mysqli_query($mysqli,$sql);
            $count=mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);

            $json = array('Error' => false, 'ID' => "$id", 'Description' => $row['description']);
            $jsonencoded = json_encode($json);
            echo $jsonencoded;
        } else if(isset($_REQUEST['background'])) {
            $sql="SELECT ID, background FROM pco_parks WHERE ID='$id'";
            $result=mysqli_query($mysqli,$sql);
            $count=mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);

            $json = array('Error' => false, 'ID' => "$id", 'Background' => $row['background']);
            $jsonencoded = json_encode($json);
            echo $jsonencoded;
        }  else if(isset($_REQUEST['name'])) {
            $sql="SELECT ID, name FROM pco_parks WHERE ID='$id'";
            $result=mysqli_query($mysqli,$sql);
            $count=mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);

            $json = array('Error' => false, 'ID' => "$id", 'Name' => $row['name']);
            $jsonencoded = json_encode($json);
            echo $jsonencoded;
        } else {
            $sql="SELECT ID, followers, header, logo, ip, description, background, name FROM pco_parks WHERE ID='$id'";
            $result=mysqli_query($mysqli,$sql);
            $count=mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);
            $followers = count(explode(",", $row['followers']))-1;
            $banner = $row['header'];
            $logo = $row['logo'];
            $ip = $row['ip'];
            $description = $row['description'];
            $background = $row['background'];
            $name = $row['name'];

            $json = array('Error' => false, 'ID' => "$id", 'Followers' => $followers, 'Banner' => $banner, 'Logo' => $banner, 'IP' => $ip, 'Description' => $description, 'Background' => $background, 'Name' => $name);
            $jsonencoded = json_encode($json);
            echo $jsonencoded;
        }
    } else {
        $err = array('Error' => true, 'Message' => "This park does'nt excist");
        echo json_encode($err);
    }
} else {
    $err = array('Error' => true, 'Message' => "Park is not setted");
    echo json_encode($err);
}
mysqli_close($mysqli);