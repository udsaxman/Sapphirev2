<?php
include 'connection.php';

$pilotIDArray[0] = 0;
$pilotCount = 0;
$totalUserCount = 0;
$targetTowerYo = 0;
$targetLandlord = 0;

$removeIdArray[0] = 0;
$removeCount = 0;

$sql = "Select Count(user_id) as Count from Users";

$result = mysql_query($sql, $conn) or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "Count") {
            $totalUserCount = $value;
        }
    }
}

for ($i = 0; $i < ($totalUserCount + 1); $i++) {

    if (isset($_POST["selType" . $i])) {

        if ($_POST["selType" . $i] != 0) {
            $pilotIDArray[$pilotCount] = $_POST["selType" . $i];
            $pilotCount++;
        }
    }
}

for ($i = 0; $i < ($totalUserCount + 2); $i++) {

    if (isset($_POST["remove" . $i])) {
        if ($_POST["remove" . $i] == "removeMe") {
            $removeIdArray[$removeCount] = $i;
            //echo "Remove Me: ".$removeIdArray[$removeCount];
            $removeCount++;

        }
    }
}

if (isset($_POST["targetTower"])) {
    $targetTowerYo = $_POST["targetTower"];
}

if (isset($_POST["selLandlord"])) {
    $targetLandlord = $_POST["selLandlord"];
}

for ($i = 0; $i < $pilotCount; $i++) {
    $tempUserCount = 0;
    $sql = "Select
					Count(u.user_name) as 'userCount'
				From
					Starbase_Users su
				Left Join
					Users u on u.user_id = su.user_id
				Where
					su.isRemoved = 0
					And
					su.user_id = " . $pilotIDArray[$i] . "
					And
					su.starbase_id = " . $targetTowerYo;

    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "userCount") {
                $tempUserCount = $value;
            }
        }
    }

    if ($tempUserCount > 0) {
        //This player is already a resident, do nothing
    } else {
        $tempDeadCount = 0;
        $sql = "Select
						Count(u.user_name) as 'userCount'
					From
						Starbase_Users su
					Left Join
						Users u on u.user_id = su.user_id
					Where
						su.isRemoved = 1
						And
						su.user_id = " . $pilotIDArray[$i] . "
						And
						su.starbase_id = " . $targetTowerYo;

        $result = mysql_query($sql, $conn) or die(mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            foreach ($row as $name => $value) {
                if ($name == "userCount") {
                    $tempDeadCount = $value;
                }
            }
        }

        if ($tempDeadCount > 0) {
            $sql = "CALL sp_ResetTowerMember(" . $pilotIDArray[$i] . ", " . $targetTowerYo . ")";

            $result = mysql_query($sql, $conn) or die(mysql_error());
        } else {
            $sql = "CALL sp_AddTowerMember(" . $pilotIDArray[$i] . ", " . $targetTowerYo . ")";

            $result = mysql_query($sql, $conn) or die(mysql_error());
        }

    }

}

for ($i = 0; $i < $removeCount; $i++) {
    $sql = "CALL sp_RemoveTowerMember(" . $removeIdArray[$i] . ", " . $targetTowerYo . ")";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    //echo "Remove Go Go Go";
}

if ($targetLandlord != -1) {
    $sql = "CALL sp_SetTowerLandlord(" . $targetLandlord . ", " . $targetTowerYo . ")";

    $result = mysql_query($sql, $conn) or die(mysql_error());
}

Success();

function Fail($error)
{
    header('Location: ./TDSInError.php?Error=' . $error . '');
}

function Success()
{
    header('Location: ./TDSInHome.php');
}

?>