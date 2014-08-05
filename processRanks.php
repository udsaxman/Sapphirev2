<?php
include 'connection.php';

$adminName = "";
$adminPower = 0;

$oldRankIds[0] = 0;
$oldRankOldPower[0] = 0;
$oldRankNewPower[0] = 0;
$oldRankCount = 0;

$anotherOldCount = 0;

$newRankNames[0] = "";
$newRankPower[0] = 0;
$newRankCount = 0;

$fail = false;
$rankFail = false;

if (isset($_POST["adminName"])) {
    $adminName = $_POST["adminName"];
} else {
    $fail = true;
}


$sql = "Select
				Count(rank_id) As Total
			From
				Ranks";

$result = mysql_query($sql, $conn) or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "Total") {
            $oldRankCount = $value;
        }
    }
}

$sql = "Select
				rank_power
			From
				Ranks
			Where
				rank_id = 
					(Select
						rank_id
					From
						Users
					Where
						user_name = '" . $adminName . "')";

$result = mysql_query($sql, $conn) or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "rank_power") {
            $adminPower = $value;
        }
    }
}

//Old Ranks
for ($i = 0; $i < $oldRankCount + 1; $i++) {
    if (isset($_POST["currentPower" . $i])) {
        if (isset($_POST["newPower" . $i])) {
            if ($_POST["currentPower" . $i] != $_POST["newPower" . $i]) {
                global $oldRankIds, $oldRankNewPower;

                /*echo $i;
                echo "<br />";
                echo $_POST["newPower". $i];
                echo "<br />";
                echo $_POST["currentPower". $i];
                echo "<br />";*/

                $oldRankIds[$anotherOldCount] = $i + 1;
                $oldRankOldPower[$anotherOldCount] = $_POST["currentPower" . $i];
                $oldRankNewPower[$anotherOldCount] = $_POST["newPower" . $i];
                $anotherOldCount++;

                if ($_POST["newPower" . $i] >= $adminPower || $_POST["currentPower" . $i] >= $adminPower) {
                    $rankFail = true;
                }
            }
        }
    }
}

//New Ranks
while (isset($_POST["newRankName" . $newRankCount]) && $_POST["newRankName" . $newRankCount] != "") {
    if (isset($_POST["newRankPower" . $newRankCount]) && $_POST["newRankPower" . $newRankCount] != "") {
        $newRankNames[$newRankCount] = $_POST["newRankName" . $newRankCount];
        $newRankPower[$newRankCount] = $_POST["newRankPower" . $newRankCount];
        $newRankCount++;

        if ($_POST["newRankPower" . $newRankCount] >= $adminPower) {
            $rankFail = true;
        }
    }
}

if (!$fail) {
    if (!$rankFail) {
        //Update Old Ranks
        for ($i = 0; $i < $anotherOldCount; $i++) {
            global $oldRankIds, $oldRankNewPower;
            $sql = "Call sp_UpdateRankPower(" . $oldRankIds[$i] . ", " . $oldRankNewPower[$i] . ")";
            $result = mysql_query($sql, $conn) or die(mysql_error());
        }

        //Add New Ranks
        for ($i = 0; $i < $newRankCount; $i++) {
            $sql = "Call sp_AddNewRank('" . $newRankNames[$i] . "', " . $newRankPower[$i] . ")";
            $result = mysql_query($sql, $conn) or die(mysql_error());
        }
    } else {
        Fail("You cannot Create or Edit the Rank of a Rank with higher or Equal Power as yourself");
    }

    Success();
} else {
    Fail("Bad Data");
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