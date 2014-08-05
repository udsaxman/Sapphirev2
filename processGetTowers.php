<?php

include 'eveAPIInterface.php';
include 'connection.php';

$sql = "SELECT
			key_id, 
			keyID,
			v_code
		FROM
			EveAPIKeys
		Where
			inUse = 1";

$result = mysql_query($sql, $conn) or die(mysql_error());

$keyPK = 0;
$keyIdYo = -1;
$vCode = "";

while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "key_id")
            $keyPK = $value;
        if ($name == "keyID")
            $keyIdYo = $value;
        if ($name == "v_code")
            $vCode = $value;
    }
}

if ($keyIdYo != -1) {
    $itemIDs[0] = 0;
    $typeIDs[0] = 0;
    $locationIDs[0] = 0;
    $moonIDs[0] = 0;
    $states[0] = 0;
    $stateTimes[0] = "";
    $onlineTimes[0] = "";
    $lastUpdates[0] = "";

    $api = new eveAPI();

    $api->Init($keyIdYo, $vCode);

    $ret = $api->getTowerListXML();

    if ($ret) {
        $itemIDs = $api->getValueArrayFromXML($ret, "itemID");
        $typeIDs = $api->getValueArrayFromXML($ret, "typeID");
        $locationIDs = $api->getValueArrayFromXML($ret, "locationID");
        $moonIDs = $api->getValueArrayFromXML($ret, "moonID");
        $states = $api->getValueArrayFromXML($ret, "state");
        $stateTimes = $api->getValueArrayFromXML($ret, "stateTimestamp");
        $onlineTimes = $api->getValueArrayFromXML($ret, "onlineTimestamp");
    } else {
        $fail = true;
    }
} else {
    $fail = true;
}

if (!$fail) {
    for ($i = 0; $i < count($itemIDs); $i++) {
        $starbasePK = 0;
        $update = "";

        $sql = "Select
					starbase_id,
					lastUpdate
				From
					Starbases
				Where
					itemID = " . $itemIDs[$i];

        //echo $itemIDs[0];
        //echo "<br />";

        $result = mysql_query($sql, $conn) or die(mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            foreach ($row as $name => $value) {
                if ($name == "starbase_id") {
                    $starbasePK = $value;
                }
                if ($name == "lastUpdate") {
                    $update = $value;
                }
            }
        }

        $today = date("Y.m.d");

        if ($starbasePK == 0) {
            $sql = "CALL sp_AddStarbase(" . $itemIDs[$i] . ", " . $typeIDs[$i] . ", " . $locationIDs[$i]
                . ", " . $moonIDs[$i] . ", " . $states[$i] . ", '" . $stateTimes[$i] . "', '" . $onlineTimes[$i]
                . "', '" . $today . "')";
            $result = mysql_query($sql, $conn) or die(mysql_error());

            $starbase_id = 0;

            ///THIS NEEDS TO BE Optimized!!!
            $sql = "select starbase_id from Starbases Order by starbase_id desc limit 1";
            $result = mysql_query($sql, $conn) or die(mysql_error());

            while ($row = mysql_fetch_assoc($result)) {
                foreach ($row as $name => $value) {
                    if ($name == "starbase_id") {
                        $starbase_id = $value;
                    }
                }
            }

            $sql = "CALL sp_AddStarbaseKey(" . $starbase_id . ", " . $keyPK . ")";
            $result = mysql_query($sql, $conn) or die(mysql_error());
        } else if ($update != $today) {
            $sql = "CALL sp_UpdateStarbase(" . $starbasePK . ", " . $locationIDs[$i] . ", " . $moonIDs[$i]
                . ", " . $states[$i] . ", '" . $stateTimes[$i] . "', '" . $onlineTimes[$i] . "', '" . $today . "')";
            $result = mysql_query($sql, $conn) or die(mysql_error());
        } else {
            //Same Day
        }
    }
    Success();
} else {
    Fail("Failed to get the List of Towers");
}

function Fail($error)
{
    header('Location: ./TDSInError.php?Error=' . $error . '');
}

function Success()
{
    header('Location: ./TDSInTowers.php');
}

?>