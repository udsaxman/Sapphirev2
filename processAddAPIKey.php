<?php
include 'eveAPIInterface.php';
include 'connection.php';

$fail = false;
$key_ID = 0;
$v_Code = "";

if (isset($_POST["keyID"])) {
    $key_ID = $_POST["keyID"];
    if (trim($key_ID) == "")
        $fail = true;
} else {
    $fail = true;
}

if (isset($_POST["vCode"])) {
    $v_Code = $_POST["vCode"];
    if (trim($v_Code) == "")
        $fail = true;
} else {
    $fail = true;
}

if (!$fail) {
    $api = new eveAPI();

    $charIds[0] = 0;
    $charNames[0] = "";
    $corpIds[0] = 0;
    $corpNames[0] = 0;

    $api->Init($key_ID, $v_Code);
    $ret = $api->getCharXML();
    if ($ret) {
        $charIds = $api->getValueArrayFromXML($ret, "characterID");
        $charNames = $api->getValueArrayFromXML($ret, "name");
        $corpIds = $api->getValueArrayFromXML($ret, "corporationID");
        $corpNames = $api->getValueArrayFromXML($ret, "corporationName");
    } else {
        $fail = true;
    }

    if (!$fail) {
        if (IsProduction()) {
            mysql_select_db("eve.tdsin");
        } else {
            mysql_select_db("naed_Sapphire");
        }

        $sql = "CALL sp_AddAPIKey(" . $key_ID . ", '" . $v_Code . "', " . $charIds[0] . ", '" . $charNames[0] . "', " . $corpIds[0] . ", '" . $corpNames[0] . "')";

        $result = mysql_query($sql, $conn) or die(mysql_error());

        Success();
    } else {
        Fail("Invalid input was provided");
    }
}

function Fail($error)
{
    header('Location: ./TDSInError.php?Error=' . $error . '');
}

function Success()
{
    header('Location: ./TDSInAdminTools.php');
}


?>