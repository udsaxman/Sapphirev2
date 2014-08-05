<?php
include 'connection.php';


$speedCount = 0;
$eveItemIds[0] = "";
$eveItemAmounts[0] = 0;
$fail = false;
$itemPKs[0] = 0;
$itemIdsYo[0] = 0;

while (isset($_POST["SpeedId" . $speedCount])) {
    $eveItemIds[$speedCount] = $_POST["SpeedId" . $speedCount];

    if (isset($_POST["SpeedAmount" . $speedCount])) {
        $eveItemAmounts[$speedCount] = $_POST["SpeedAmount" . $speedCount];
    } else {
        $fail = true;
    }

    if (isset($_POST["selItem" . $speedCount])) {
        $itemPKs[$speedCount] = $_POST["selItem" . $speedCount];
    } else {
        $fail = true;
    }

    $speedCount++;
}

if (!$fail) {
    for ($x = 0; $x < $speedCount; $x++) {
        $targetItemId = -1;
        $sql = "Select
						item_id
					From
						Speed_Items
					Where
						eve_id = " . $eveItemIds[$x];

        $result = mysql_query($sql, $conn) or die(mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            foreach ($row as $name => $value) {
                if ($name == "item_id") {
                    $targetItemId = $value;
                }
            }
        }

        if ($targetItemId == -1) {
            //Add
            if ($itemPKs[$x] != -1) {
                $sql = "CALL sp_AddSpeedItem(" . $itemPKs[$x] . "," . $eveItemIds[$x] . ")";
                $result = mysql_query($sql, $conn) or die(mysql_error());
            }
        } else {
            //Update
            $sql = "CALL sp_UpdateSpeedItem(" . $itemPKs[$x] . "," . $eveItemIds[$x] . ")";
            $result = mysql_query($sql, $conn) or die(mysql_error());
        }
    }

    //Redirect to AddOps with Info
    if ($speedCount > 0) {
        $redirectInfo = "?SpeedId0=" . $itemPKs[0] . "&SpeedAmount0=" . $eveItemAmounts[0];
        for ($x = 1; $x < $speedCount; $x++) {
            $redirectInfo = $redirectInfo . "&SpeedId" . $x . "=" . $itemPKs[$x] . "&SpeedAmount" . $x . "=" . $eveItemAmounts[$x];
        }

        //Redirect
        Success($redirectInfo);
    } else {
        Fail("We got nothing");
    }

} else {
    Fail("Critical Failure");
}

function Fail($error)
{
    header('Location: ./TDSInError.php?Error=' . $error);
    //echo "<br />";
    //echo $error;
}

function Success($info)
{
    //echo "<br />";
    //echo $info;
    header('Location: ./TDSInAddOp.php' . $info);
}

?>