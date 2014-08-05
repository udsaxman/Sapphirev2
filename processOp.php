<?php
include 'connection.php';
//ini_set('display_errors', 1);
//error_reporting(E_ALL | E_STRICT);
//echo "Are you receiving?";

//WE NEED TO VALIDATE INPUT SO WE DON'T HAVE STRINGS IN INTs
$isExsist = true;

//$pilotArray[0] = 0;
$pilotIDArray[0] = 0;
$pilotCount = 0;

$shareArray[0] = 0;
$processorName = "";
$processorID = 0;
$op_id = 0;
$opName = "";
$opDate = "";
$opValue = 0;
$opTax = 0;
$opTaxAmount = 0;
$totalShares = 0;
$shareValue = 0;

$itemArray[0] = "";
$itemAmountArray[0] = 0;
$itemTotalValue[0] = 0;

//Items are 1 based
$itemCount = 1;

while ($isExsist == true) {
    //These need to be changed to $_POST
    //Items were ordered by ItemId so they should be stored the same way too?
    if (isset($_POST["itemName" . $itemCount])) {
        $itemArray[$itemCount] = $_POST["itemName" . $itemCount];
        $itemAmountArray[$itemCount] = $_POST["itemAmount" . $itemCount];
        $itemCount++;
    } else {
        $isExsist = false;
    }
}


$totalUserCount = 0;

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
            $shareArray[$pilotCount] = $_POST["Shares" . $i];
            $pilotCount++;
        }
    }
}

if (isset($_POST["name"])) {
    $processorName = $_POST["name"];
} else {
    $processorName = "Error_No_Name_Was_Found";
}

if (isset($_POST["opName"])) {
    $opName = $_POST["opName"];
} else {
    $opName = "Error_No_Op_Name_Found";
}

if (isset($_POST["opDate"])) {
    $opDate = $_POST["opDate"];
} else {
    $opDate = "Error_No_Op_Date_Found";
}

if (isset($_POST["opTax"])) {
    $opTax = $_POST["opTax"];
} else {
    $opTax = 0;
}

$sql = "select user_id from Users where user_name = '" . $processorName . "'";

$result = mysql_query($sql, $conn) or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "user_id") {
            $processorID = $value;
        }
    }
}

$itemValue[0] = 0;
$useTax[0] = true;
$itemTax[0] = 0;
$itemTotalValue[0] = 0;

//Calculate Op Value
//Items are 1 based
for ($i = 1; $i < $itemCount; $i++) {
    //This is bad, Should just select all from items and then after you store them, use php to search the array
    $sql = "Select
					item_iskValue,
					category_taxOverride,
					category_useOverride 
				From
					Items 
				Left Join
					Item_Category on category_id = item_type 
				Where item_name = '" . $itemArray[$i] . "'";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    //echo("itemArray: ". $itemArray[$i]);

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "item_iskValue") {
                $itemAmountArray[$i] = str_replace(",", "", $itemAmountArray[$i]);
                $itemValue[$i] = (float)$value * (float)$itemAmountArray[$i];
            }
            if ($name == "category_taxOverride") {
                $itemTax[$i] = $value;
            }
            if ($name == "category_useOverride") {
                $useTax[$i] = $value;
            }
        }
    }
}

for ($i = 0; $i < $itemCount; $i++) {
    if ($useTax[$i] == 0) {
        $tempTax = (float)$itemValue[$i] * ((float)$opTax / 100.0);
        (float)$itemTotalValue[$i] = (float)$itemValue[$i] - (float)$tempTax;
        (float)$opTaxAmount += (float)$tempTax;
    } else {
        (float)$tempTax = (float)$itemValue[$i] * ((float)$itemTax[$i] / 100.0);
        (float)$itemTotalValue[$i] = (float)$itemValue[$i] - (float)$tempTax;
        (float)$opTaxAmount += (float)$tempTax;
    }
}

for ($i = 0; $i < $itemCount; $i++) {
    (float)$opValue += (float)$itemTotalValue[$i];
}

/*$opTaxAmount = (float)$opValue * ((float)$opTax/100.0);

$opValue -= (float)$opTaxAmount;*/

for ($i = 0; $i < $pilotCount; $i++) {
    $shareArray[$i] = str_replace(",", "", $shareArray[$i]);
    if ($shareArray[$i] < 0) {
        $shareArray[$i] = 0;
    }
    $totalShares += (int)$shareArray[$i];
}

if ($totalShares > 0) {
    $shareValue = (float)$opValue / (float)$totalShares;
} else {
    $shareValue = 0.00;
}

//CreateNew Op
$strSearch = "'";
$pos = stripos($opName, $strSearch);

if ($pos === false && trim($opName) != "") {
} else {
    $opName = "DefaultOpName";
}

if (trim($opTax) == "" || !is_numeric($opTax)) {
    $opTax = 0.00;
}

$sql = "Call sp_AddNewOp('" . $opName . "', " . $processorID . ", '" . $opDate . "', " . $opTax . ", " . $opTaxAmount . ", " . $shareValue . ", " . $opValue . ")";

$result = mysql_query($sql, $conn) or die(mysql_error());

//$sql = "select op_id from Ops";

$sql = "select op_id from Ops Order by op_id desc limit 1";

$result = mysql_query($sql, $conn) or die(mysql_error());

//We get ALL the op_ids, but in the end we are only storing the last one
while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "op_id") {
            $op_id = $value;
        }
    }
}

//Then Create Op_Attendance
for ($i = 0; $i < $pilotCount; $i++) {
    if ($shareArray[$i] > 0 && trim($shareArray[$i]) != "" && is_numeric($shareArray[$i])) {
        $sql = "Call sp_AddUserToOpAttendence(" . $op_id . ", " . $pilotIDArray[$i] . ", " . $shareArray[$i] . ")";
        $result = mysql_query($sql, $conn) or die(mysql_error());

        //Add Money to people's Wallets
        $sql = "Select user_isk From Users Where user_id = " . $pilotIDArray[$i] . "";
        $result = mysql_query($sql, $conn) or die(mysql_error());
        //This all should be done at the beginning or something
        //Just seems like I'm going to be flooding the Database with ALL the things
        while ($row = mysql_fetch_assoc($result)) {
            foreach ($row as $name => $value) {
                if ($name == "user_isk") {
                    $tempIsk = $value;
                }
            }
        }

        $tempIsk += ($shareValue * $shareArray[$i]);

        $sql = "Call sp_OpTransaction(" . $pilotIDArray[$i] . ", " . $op_id . ", " . ($shareValue * $shareArray[$i]) . ", '" . $opDate . "')";

        $result = mysql_query($sql, $conn) or die(mysql_error());

        $sql = "Call sp_UpdateUserIsk(" . $tempIsk . ", " . $pilotIDArray[$i] . ")";

        $result = mysql_query($sql, $conn) or die(mysql_error());


    }
}

//Then Create Op_Loot
for ($i = 1; $i < $itemCount; $i++) {
    if ($itemAmountArray[$i] != 0 && trim($itemAmountArray[$i]) != "" && is_numeric($itemAmountArray[$i])) {
        $sql = "Call sp_AddItemToOpLoot(" . $op_id . ", " . $i . ", " . $itemAmountArray[$i] . ")";

        $result = mysql_query($sql, $conn) or die(mysql_error());
    }
}

Success();
//echo("Op Value: ". $opValue);

function Fail($error)
{
    header('Location: ./TDSInError.php?Error=' . $error . '');
}

function Success()
{
    //Go to View Op
    header('Location: ./TDSInAdminTools.php');
}

?>