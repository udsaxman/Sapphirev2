<?php

include 'connection.php';
//echo "Are you recieving?"

$targetUserName = "";
$targetUserID = -1;

$targetCurrentRankName = "";
$targetCurrentRankPower = 0;

$newRankId = 0;
$newRankPower = 0;

$processorName = "";
$processorRankPower = 0;

if (isset($_POST["adminName"])) {
    $processorName = $_POST["adminName"];
} else {
    Fail("Bad Data");
}

if (isset($_POST["currentUser"])) {
    $targetUserName = $_POST["currentUser"];
} else {
    Fail("Bad Data");
}

if (isset($_POST["currentRank"])) {
    $targetCurrentRankName = $_POST["currentRank"];
} else {
    Fail("Bad Data");
}

if (isset($_POST["selRank"])) {
    $newRankId = ($_POST["selRank"] + 1);
} else {
    Fail("Bad Data");
}

$sql = "Select
				rank_power
			from
				Ranks
			where
				rank_id = 
					(Select
						rank_id
					from
						Users
					where
						user_name = '" . strtolower($processorName) . "')";

$result = mysql_query($sql, $conn) or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "rank_power") {
            $processorRankPower = $value;
        }
    }
}

$sql = "Select
				rank_power
			from
				Ranks
			where
				rank_id = 
					(Select
						rank_id
					from
						Users
					where
						user_name = '" . strtolower($targetUserName) . "')";

$result = mysql_query($sql, $conn) or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "rank_power") {
            $targetCurrentRankPower = $value;
        }
    }
}

if ($targetCurrentRankPower >= $processorRankPower) {
    Fail("You do not have the power to Preform this act");
} else {

    $sql = "Select
					rank_power
				from
					Ranks
				where
					rank_id = '" . $newRankId . "'";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "rank_power") {
                $newRankPower = $value;
            }
        }
    }

    if ($newRankPower >= $processorRankPower) {
        Fail("You do not have the power to Preform this act");
    } else {

        $sql = "Select
						user_id
					from
						Users
					where
						user_name = '" . strtolower($targetUserName) . "'";

        $result = mysql_query($sql, $conn) or die(mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            foreach ($row as $name => $value) {
                if ($name == "user_id") {
                    $targetUserID = $value;
                }
            }
        }

        if ($targetUserID == -1) {
            Fail("Bad Data, I think??");
        } else {

            $sql = "Call sp_UpdateUserRank(" . $targetUserID . ", " . $newRankId . ")";

            $result = mysql_query($sql, $conn) or die(mysql_error());

            Success();
        }
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