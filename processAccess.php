<?php
$fail = false;
include 'connection.php';

$accessCount = 0;
$arrayCount = 0;
$accessId[0] = 0;
$accessNewPower[0] = 0;


$sql = "Select
				Count(access_id) As Total
			From
				Access";

$result = mysql_query($sql, $conn) or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "Total") {
            $accessCount = $value;
        }
    }
}

for ($i = 0; $i < $accessCount + 1; $i++) {
    if (isset($_POST["currentPower" . $i])) {
        if (isset($_POST["newPower" . $i])) {
            if ($_POST["currentPower" . $i] != $_POST["newPower" . $i]) {
                global $accessId, $accessNewPower;

                $accessId[$arrayCount] = $i + 1;

                if (!(is_numeric($_POST["newPower" . $i])) || trim($_POST["newPower" . $i]) == "") {
                    $fail = true;
                } else {
                    $accessNewPower[$arrayCount] = $_POST["newPower" . $i];
                }
                $arrayCount++;
            }
        }
    }
}

if (!$fail) {
    for ($i = 0; $i < $arrayCount; $i++) {
        global $accessId, $accessNewPower;

        $sql = "Call sp_UpdateAccess(" . $accessId[$i] . ", " . $accessNewPower[$i] . ")";
        $result = mysql_query($sql, $conn) or die(mysql_error());
    }

    Success();
} else {
    Fail("Invalid Input detected");
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