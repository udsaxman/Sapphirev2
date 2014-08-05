<?php

include 'eveAPIInterface.php';
include 'connection.php';

$singleStarbase = 0;

if (isset($_REQUEST["UpdateStarbase"])) {
    $singleStarbase = $_REQUEST["UpdateStarbase"];
}

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
$keyIdYo = 0;
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

if ($keyIdYo != 0) {
    if ($singleStarbase == 0) {
        $sql = "Select
					starbase_id,
					itemID
				From
					Starbases";
    } else {
        $sql = "Select
					starbase_id,
					itemID
				From
					Starbases
				Where
					starbase_id =" . $singleStarbase;
    }


    $result = mysql_query($sql, $conn) or die(mysql_error());

    $starBasePks[0] = 0;
    $starBaseId[0] = 0;
    $starBaseCount = 0;

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "starbase_id")
                $starBasePks[$starBaseCount] = $value;
            if ($name == "itemID") {
                $starBaseId[$starBaseCount] = $value;
                $starBaseCount++;
            }
        }
    }

    $api = new eveAPI();

    $api->Init($keyIdYo, $vCode);

    for ($i = 0; $i < $starBaseCount; $i++) {
        $types[0] = 0;
        $amounts[0] = 0;

        $detailCount = 0;

        $ret = $api->getTowerDetailXML($starBaseId[$i]);

        if ($ret) {
            $types = $api->getValueArrayFromXML($ret, "typeID");
            $amounts = $api->getValueArrayFromXML($ret, "quantity");

            $sql = "Select
						detail_id
					From
						Starbase_Details
					Where
						starbase_id =" . $starBasePks[$i];

            $result = mysql_query($sql, $conn) or die(mysql_error());

            while ($row = mysql_fetch_assoc($result)) {
                foreach ($row as $name => $value) {
                    if ($name == "detail_id") {
                        $detailCount++;
                    }
                }
            }

            if ($detailCount == 0) {
                //Add Everything
                for ($x = 0; $x < count($types); $x++) {
                    //Add Fuel
                    $detailPk = 0;

                    $sql = "CALL sp_AddDetail(" . $types[$x] . ", " . $amounts[$x] . ")";
                    $result = mysql_query($sql, $conn) or die(mysql_error());

                    $sql = "select detail_id from Details Order by detail_id desc limit 1;";
                    $result = mysql_query($sql, $conn) or die(mysql_error());

                    while ($row = mysql_fetch_assoc($result)) {
                        foreach ($row as $name => $value) {
                            if ($name == "detail_id") {
                                $detailPk = $value;
                            }
                        }
                    }

                    $sql = "CALL sp_AddStarbaseDetail(" . $starBasePks[$i] . ", " . $detailPk
                        . ", '" . date("Y.m.d") . "')";
                    $result = mysql_query($sql, $conn) or die(mysql_error());
                }
            } else {
                $details[0] = 0;
                $detailCount = 0;
                $today = date("Y.m.d");

                for ($x = 0; $x < count($types); $x++) {
                    $amountCheck = -1;
                    $update = "";
                    $sql = "Select
								s.lastUpdate,
								d.detail_id,
								d.amount
							From
								Starbase_Details s
							left join Details d on d.detail_id = s.detail_id
							where
								s.starbase_id = " . $starBasePks[$i] . "
								 And 
								d.fuel_typeID = " . $types[$x];

                    $result = mysql_query($sql, $conn) or die(mysql_error());

                    while ($row = mysql_fetch_assoc($result)) {
                        foreach ($row as $name => $value) {
                            if ($name == "lastUpdate") {
                                $update = $value;
                            }
                            if ($name == "detail_id") {
                                $details[$x] = $value;
                            }
                            if ($name == "amount") {
                                $amountCheck = $value;
                            }
                        }
                    }

                    if ($amountCheck == -1) {
                        $superDetailPK = 0;

                        //Add Fuel
                        $sql = "CALL sp_AddDetail(" . $types[$x] . ", " . $amounts[$x] . ")";
                        $result = mysql_query($sql, $conn) or die(mysql_error());

                        //Add StarbaseDetail
                        $sql = "select detail_id from Details Order by detail_id desc limit 1";
                        $result = mysql_query($sql, $conn) or die(mysql_error());

                        while ($row = mysql_fetch_assoc($result)) {
                            foreach ($row as $name => $value) {
                                if ($name == "detail_id") {
                                    $superDetailPK = $value;
                                }
                            }
                        }

                        $sql = "CALL sp_AddStarbaseDetail(" . $starBasePks[$i] . ", " . $superDetailPK
                            . ", '" . $today . "')";
                        $result = mysql_query($sql, $conn) or die(mysql_error());
                    } else if ($update != $today) {
                        //Update Fuel;
                        $sql = "CALL sp_UpdateDetail(" . $details[$x] . ", " . $amounts[$x] . ")";
                        $result = mysql_query($sql, $conn) or die(mysql_error());

                        $sql = "CALL sp_UpdateStarbaseDetail(" . $starBasePks[$i] . ", " . $details[$x]
                            . ", '" . $today . "')";
                        $result = mysql_query($sql, $conn) or die(mysql_error());
                    }


                }
            }
        } else {
            $fail = true;
            break;
        }
    }
    if ($fail) {
        Fail("There was an Error getting Tower Details");
    } else {
        Success();
    }
} else {
    Fail("Failed to Get Tower Details");
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