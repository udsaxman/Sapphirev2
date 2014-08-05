<?php
include 'connection.php';

$oldCatIds[0] = 0;
$oldCatNames[0] = "";
$oldCatOrders[0] = 0;
$oldCatTaxs[0] = 0;
$oldCatOverride[0] = 0;
$oldCatCount = 0;

$validOldCount = 0;

$newCatNames[0] = "";
$newCatOrders[0] = 0;
$newCatTaxs[0] = 0;
$newCatOverride[0] = 0;

$newCatCount = 0;

$sql = "Select Count(category_id) As Total From Item_Category";

$result = mysql_query($sql, $conn) or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "Total") {
            $oldCatCount = $value;
        }
    }
}

for ($i = 1; $i < $oldCatCount + 1; $i++) {
    if (isset($_POST["oldCatName" . $i]) && $_POST["oldCatName" . $i] != "") {
        $oldCatNames[$validOldCount] = $_POST["oldCatName" . $i];
        $oldCatIds[$validOldCount] = $i;

        if (isset($_POST["oldCatOrder" . $i]) && is_numeric($_POST["oldCatOrder" . $i])) {
            $oldCatOrders[$validOldCount] = $_POST["oldCatOrder" . $i];
        } else {
            $oldCatOrders[$validOldCount] = 0;
        }
        if (isset($_POST["oldCatTax" . $i]) && is_numeric($_POST["oldCatTax" . $i])) {
            $oldCatTaxs[$validOldCount] = $_POST["oldCatTax" . $i];
        } else {
            $oldCatTaxs[$validOldCount] = 0;
        }
        if (isset($_POST["oldCatOverride" . $i])) {
            $oldCatOverride[$validOldCount] = 1;
        } else {
            $oldCatOverride[$validOldCount] = 0;
        }

        $validOldCount++;
    }
}

while (isset($_POST["newCatName" . $newCatCount]) && $_POST["newCatName" . $newCatCount] != "") {
    $newCatNames[$newCatCount] = $_POST["newCatName" . $newCatCount];

    if (isset($_POST["newCatOrder" . $newCatCount]) && is_numeric($_POST["newCatOrder" . $newCatCount])) {
        $newCatOrders[$newCatCount] = $_POST["newCatOrder" . $newCatCount];
    } else {
        $newCatOrders[$newCatCount] = 0;
    }
    if (isset($_POST["newCatTax" . $newCatCount]) && is_numeric($_POST["newCatTax" . $newCatCount])) {
        $newCatTaxs[$newCatCount] = $_POST["newCatTax" . $newCatCount];
    } else {
        $newCatTaxs[$newCatCount] = 0;
    }
    if (isset($_POST["newCatOverride" . $newCatCount])) {
        $newCatOverride[$newCatCount] = 1;
    } else {
        $newCatOverride[$newCatCount] = 0;
    }

    $newCatCount++;
}

for ($i = 0; $i < $validOldCount; $i++) {
    $sql = "Call sp_UpdateItemCategory(" . $oldCatIds[$i]
        . ", '" . $oldCatNames[$i] . "', " . $oldCatOrders[$i]
        . ", " . $oldCatTaxs[$i] . ", " . $oldCatOverride[$i] . ")";
    $result = mysql_query($sql, $conn) or die(mysql_error());
}

for ($i = 0; $i < $newCatCount; $i++) {
    $sql = "Call sp_AddNewItemCategory('" . $newCatNames[$i]
        . "', " . $newCatOrders[$i] . ", " . $newCatTaxs[$i] . ", " . $newCatOverride[$i] . ")";
    $result = mysql_query($sql, $conn) or die(mysql_error());
}

Success();

function Fail($error)
{
    header('Location: ./TDSInError.php?Error=' . $error . '');
}

function Success()
{
    header('Location: ./TDSInAdminTools.php');
}

?>