<?php
$fail = false;
include 'connection.php';

foreach ($_POST['newPower'] as $power){
    $sql = "Call sp_UpdateAccess(" . $power['id'] . ", " . $power['power'] . ")";
    $mysqli->query($sql);
}

header('Location: ./TDSInAdminTools.php');

?>