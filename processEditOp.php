<?php

session_start();

$failed = false;
$error = "";
$editor = "";
$editorID = NULL;

//From edit Ops
$op_id = 0; //
$newOp_name = ""; //
$newOpTax = (float)0.0; //

//From Database
//$oldOpTax = (float)0.0;
//$oldOpTaxAmount = (float)0.0;
//$oldOpValue = (float)0.0;
$oldOpShareValue = (float)0.0;

//From edit Ops
$oldUserIds[0] = 0; //
$oldUserNewShares[0] = 0; //
$oldUserCount = 0; //

//From Database
$oldUserShares[0] = 0; //
$oldUserIsks[0] = (float)0.0;

$allUserIds[0] = 0; //
$allUserCount = 0; //
$allUserIsks[0] = (float)0.0;

//From edit Ops
$oldItemIds[0] = 0; //
$oldItemNewAmounts[0] = 0; //
$oldItemCount = 0; //

//From Database
$oldItemAmounts[0] = 0; //
$oldItemValues[0] = (float)0.0;
$oldItemUseOverrides[0] = 0;
$oldItemTaxOverrides[0] = (float)0;

$allItemValues[0] = (float)0.0;
$allItemUseOverrides[0] = 0;
$allItemTaxOverrides[0] = (float)0;
$allItemIds[0] = 0; //
$allItemCount = 0; //

//Get the Editor
if (isset($_SESSION["userName"])) {
    $editor = $_SESSION["userName"];
} else {
    $failed = true;
    $error = "No Username";
}

//Gather All From Edit ops
if (isset($_REQUEST["opID"])) {
    $op_id = $_REQUEST["opID"];
} else {
    $failed = true;
    $error = "No op found";
}

if (isset($_REQUEST["opName"])) {
    $newOp_name = $_REQUEST["opName"];
} else {
    $newOp_name = ""; //If "", then use old Op Name
}

if (isset($_REQUEST["opTax"])) {
    $newOpTax = (float)$_REQUEST["opTax"];
} else {
    $newOpTax = NULL;
}

//Done With Edit ops

include 'connection.php';

//Gather Everything from the Database
$sql = "select user_id, user_name, user_isk from Users";

$result = mysql_query($sql, $conn) or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "user_id") {
            $allUserIds[$allUserCount] = $value;
        }
        if ($name == "user_name") {
            if (strtolower($editor) == strtolower($value)) {
                $editorID = $allUserIds[$allUserCount];
            }
        }
        if ($name == "user_isk") {
            $allUserIsks[$allUserCount] = $value;
            $allUserCount++;
        }
    }
}

if ($editorID == NULL) {
    $error = "Unable to Idenify User";
    $failed = true;
}

$sql = "Select
				item_id,
				item_iskValue,
				category_taxOverride,
				category_useOverride 
			From
				Items
			Left Join Item_Category on category_id = item_type";

$result = mysql_query($sql, $conn) or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "item_id") {
            $allItemIds[$allItemCount] = $value;
        }
        if ($name == "item_iskValue") {
            $allItemValues[$allItemCount] = (float)$value;
        }
        if ($name == "category_taxOverride") {
            $allItemTaxOverrides[$allItemCount] = (float)$value;
        }
        if ($name == "category_useOverride") {
            $allItemUseOverrides[$allItemCount] = $value;
            $allItemCount++;
        }
    }
}

for ($x = 0; $x < $allUserCount; $x++) {
    if (isset($_REQUEST["oldUser" . $allUserIds[$x]])) {
        $oldUserIds[$oldUserCount] = $allUserIds[$x];
        $oldUserIsks[$oldUserCount] = $allUserIsks[$x];
        $oldUserNewShares[$oldUserCount] = $_REQUEST["oldUser" . $allUserIds[$x]];
        $oldUserCount++;
    }
}

for ($x = 0; $x < $allItemCount; $x++) {
    if (isset($_REQUEST["oldItem" . $allItemIds[$x]])) {
        $oldItemIds[$oldItemCount] = $allItemIds[$x];
        $oldItemUseOverrides[$oldItemCount] = $allItemUseOverrides[$x];
        $oldItemTaxOverrides[$oldItemCount] = $allItemTaxOverrides[$x];
        $oldItemValues[$oldItemCount] = $allItemValues[$x];
        $oldItemNewAmounts[$oldItemCount] = $_REQUEST["oldItem" . $allItemIds[$x]];
        $oldItemNewAmounts[$oldItemCount] = str_replace(",", "", $oldItemNewAmounts[$oldItemCount]);

        if ($oldItemNewAmounts[$oldItemCount] == ""
            || !is_numeric($oldItemNewAmounts[$oldItemCount])
        )
            $oldItemNewAmounts[$oldItemCount] = 0;

        $oldItemCount++;
    }
}

$newItemIds[0] = 0;
$newItemValues[0] = (float)0;
$newItemAmounts[0] = 0;
$newItemUseOverrides[0] = 0;
$newItemTaxOverrides[0] = 0;
$newItemCount = 0;

for ($x = 0; $x < $allItemCount; $x++) {
    if (isset($_REQUEST["selItem" . $x])) {
        $newItemIds[$newItemCount] = $_REQUEST["selItem" . $x];
        if (isset($_REQUEST["selAmounts" . $x]))
            $newItemAmounts[$newItemCount] = $_REQUEST["selAmounts" . $x];
        else
            $newItemAmounts[$newItemCount] = 0;

        $newItemAmounts[$x] = str_replace(",", "", $newItemAmounts[$x]);

        if ($newItemAmounts[$newItemCount] == "" || !is_numeric($newItemAmounts[$newItemCount]))
            $newItemAmounts[$newItemCount] = 0;

        $newItemCount++;
    }
}

$newUserIds[0] = 0;
$newUserIsks[0] = (float)0.0;
$newUserShares[0] = 0;
$newUserCount = 0;

for ($x = 0; $x < $allUserCount; $x++) {
    if (isset($_REQUEST["selPilot" . $x])) {
        $newUserIds[$newUserCount] = $_REQUEST["selPilot" . $x];
        //$newUserIsks[$newUserCount] = $allUserIsks[$x];
        if (isset($_REQUEST["selShares" . $x]))
            $newUserShares[$newUserCount] = $_REQUEST["selShares" . $x];
        else
            $newUserShares[$newUserCount] = 0;

        if ($newUserShares[$newUserCount] == "" || !is_numeric($newUserShares[$newUserCount]))
            $newUserShares[$newUserCount] = 0;

        $newUserCount++;
    }
}

for ($x = 0; $x < $newUserCount; $x++) {
    for ($y = 0; $y < $allUserCount; $y++) {
        if ($newUserIds[$x] == $allUserIds[$y]) {
            $newUserIsks[$x] = $allUserIsks[$y];
        }
    }
}

for ($x = 0; $x < $newItemCount; $x++) {
    for ($y = 0; $y < $allItemCount; $y++) {
        if ($newItemIds[$x] == $allItemIds[$y]) {
            $newItemValues[$x] = $allItemValues[$y];
        }
    }
}

for ($x = 0; $x < $oldUserCount; $x++) {
    $sql = "Select
					shares
				From
					Op_Attendence
				Where 
					op_id = " . $op_id . "
					And
					user_id = " . $oldUserIds[$x] . "";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "shares") {
                $oldUserShares[$x] = $value;
            }
        }
    }
}

for ($x = 0; $x < $oldItemCount; $x++) {
    $sql = "Select
					amount
				From
					Op_Loot
				Where 
					op_id = " . $op_id . "
					And
					item_id = " . $oldItemIds[$x] . "";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "amount") {
                $oldItemAmounts[$x] = $value;
            }
        }
    }
}

$sql = "Select
				op_shareValue
			From
				Ops
			Where
				op_id = " . $op_id . "";

$result = mysql_query($sql, $conn) or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "op_shareValue") {
            $oldOpShareValue = (float)$value;
        }
    }
}

//Done with Database

//Process Information
$newOpTaxAmount = (float)0.0; //
$newOpValue = (float)0.0; //
$newOpShareValue = (float)0.0; //

$TotalShares = 0; //

//Get final share list of all pilots
for ($x = 0; $x < $oldUserCount; $x++) {
    $oldUserNewShares[$x] = str_replace(",", "", $oldUserNewShares[$x]);
    if ($oldUserNewShares[$i] < 0) {
        $oldUserNewShares[$i] = 0;
    }
    $TotalShares += (int)$oldUserNewShares[$x];
}

for ($x = 0; $x < $newUserCount; $x++) {
    $newUserShares[$x] = str_replace(",", "", $newUserShares[$x]);
    if ($newUserShares[$x] < 0) {
        $newUserShares[$x] = 0;
    }
    $TotalShares += (int)$newUserShares[$x];
}

//Get new Op information
for ($x = 0; $x < $oldItemCount; $x++) {
    if ($oldItemUseOverrides[$x] == 0) {
        //$oldItemNewAmounts[$x] = str_replace(",", "", $oldItemNewAmounts[$x]);
        $tempItemValue = (float)$oldItemValues[$x] * (float)$oldItemNewAmounts[$x];
        $tempTax = (float)$tempItemValue * ((float)$newOpTax / 100.0);
        $newOpValue += ((float)$tempItemValue - (float)$tempTax);
        $newOpTaxAmount += (float)$tempTax;
    } else {
        //$oldItemNewAmounts[$x] = str_replace(",", "", $oldItemNewAmounts[$x]);
        $tempItemValue = (float)$oldItemValues[$x] * (float)$oldItemNewAmounts[$x];
        $tempTax = (float)$tempItemValue * ((float)$oldItemTaxOverrides[$x] / 100.0);
        $newOpValue += ((float)$tempItemValue - (float)$tempTax);
        $newOpTaxAmount += (float)$tempTax;
    }
}

for ($x = 0; $x < $newItemCount; $x++) {
    if ($newItemUseOverrides[$x] == 0) {
        //$newItemAmounts[$x] = str_replace(",", "", $newItemAmounts[$x]);
        $tempItemValue = (float)$newItemValues[$x] * (float)$newItemAmounts[$x];
        $tempTax = (float)$tempItemValue * ((float)$newOpTax / 100.0);
        $newOpValue += ((float)$tempItemValue - (float)$tempTax);
        $newOpTaxAmount += (float)$tempTax;
    } else {
        $tempItemValue = (float)$newItemValues[$x] * (float)$newItemAmounts[$x];
        $tempTax = (float)$tempItemValue * ((float)$newItemTaxOverrides[$x] / 100.0);
        $newOpValue += ((float)$tempItemValue - (float)$tempTax);
        $newOpTaxAmount += (float)$tempTax;
    }
}

//Foreach New Item

$newOpShareValue = (float)$newOpValue / (float)$TotalShares;

if (!$failed) {
    //sp_UpdateOp(opID, opName, opTax, opTaxAmount, opShareValue, opIskValue)
    $sql = "Call sp_UpdateOp(" . $op_id . ", '" . $newOp_name . "', " . $newOpTax . ", " . $newOpTaxAmount . ", " . $newOpShareValue . ", " . $newOpValue . ")";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    $today = date("Y.m.d");

    //Foreach Old User
    for ($x = 0; $x < $oldUserCount; $x++) {
        $tempOldPay = (float)($oldUserShares[$x] * $oldOpShareValue);
        $tempNewPay = (float)($oldUserNewShares[$x] * $newOpShareValue);
        $diff = (float)($tempNewPay - $tempOldPay);
        if ($diff != (float)0.0) {
            $tempNewIsk = $oldUserIsks[$x] + $diff;
            $sql = "Call sp_UpdateUserIsk(" . $tempNewIsk . ", " . $oldUserIds[$x] . ")";
            $result = mysql_query($sql, $conn) or die(mysql_error());

            $sql = "Call sp_EditOpTransaction(" . $oldUserIds[$x] . ", " . $op_id . ", "
                . $diff . ", '" . $today . "')";
            $result = mysql_query($sql, $conn) or die(mysql_error());

            $sql = "Call sp_UpdateOpAttendence(" . $oldUserIds[$x] . ", " . $op_id . ", "
                . $oldUserNewShares[$x] . ")";
            $result = mysql_query($sql, $conn) or die(mysql_error());
        }
    }
    //sp_UpdateUserIsk(userIsk, userID)
    //sp_EditOpTransaction(userID, opID, _amount, _date)
    //sp_UpdateOpAttendence(userID, opID, _shares)

    //Foreach New User
    for ($x = 0; $x < $newUserCount; $x++) {
        $tempPay = (float)($newUserShares[$x] * $newOpShareValue);
        if ($tempPay != (float)0.0) {
            $tempIsk = $newUserIsks[$x] + $tempPay;
            $sql = "Call sp_UpdateUserIsk(" . $tempIsk . ", " . $newUserIds[$x] . ")";
            $result = mysql_query($sql, $conn) or die(mysql_error());

            $sql = "Call sp_EditOpTransaction(" . $newUserIds[$x] . ", " . $op_id . ", "
                . $tempPay . ", '" . $today . "')";
            $result = mysql_query($sql, $conn) or die(mysql_error());

            $sql = "Call sp_AddUserToOpAttendence(" . $op_id . ", " . $newUserIds[$x] . ", "
                . $newUserShares[$x] . ")";
            $result = mysql_query($sql, $conn) or die(mysql_error());
        }
    }
    //sp_UpdateUserIsk(userIsk, userID)
    //sp_OpTransaction(userID, opID, _amount, _date)
    //sp_AddUserToOpAttendence(opID, userID, _shares)

    //Foreach Item
    for ($x = 0; $x < $oldItemCount; $x++) {
        $sql = "Call sp_UpdateOpLoot(" . $oldItemIds[$x] . ", " . $op_id . ", " . $oldItemNewAmounts[$x] . ")";
        $result = mysql_query($sql, $conn) or die(mysql_error());
    }
    //sp_UpdateOpLoot(itemID, opID, _amount)

    //Foreach New Item
    for ($x = 0; $x < $newItemCount; $x++) {
        $sql = "Call sp_AddItemToOpLoot(" . $op_id . ", " . $newItemIds[$x] . ", " . $newItemAmounts[$x] . ")";
        $result = mysql_query($sql, $conn) or die(mysql_error());
    }
    //sp_AddItemToOpLoot(opID, itemID, _amount)

    Success();
} else {
    Fail($error);
}

function Fail($inError)
{
    header('Location: ./TDSInError.php?Error=' . $inError . '');
}

function Success()
{
    //Go to View Op
    header('Location: ./TDSInAdminTools.php');
}

?>