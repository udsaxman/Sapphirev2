<?php
include 'connection.php';

$theName = "";
$date = "";
$userId = 0;

$wallet = 0;
$amount = 0;

$type = 0;

$addText = "";

$fail = false;

//There probably should be a transfer code somewhere so we know what we're looking for + doing

if (isset($_POST["name"])) {
    $theName = $_POST["name"];
} else {
    $theName = "Error_No_Name";
}

if (isset($_POST["date"])) {
    $date = $_POST["date"];
} else {
    $date = "Error_No_Date";
}

if (isset($_POST["addText"])) {
    $addText = $_POST["addText"];
    if ($addText == "")
        $addText = NULL;
} else {
    $addText = NULL;
}

if (isset($_POST["selUser"])) {
    $userId = $_POST["selUser"];
    if ($userId == 0)
        $fail = true;
} else {
    $fail = true;
    Fail("No user found for Transfer");
}

if (isset($_POST["wallet"])) {
    $wallet = $_POST["wallet"];
} else {
    $fail = true;
    Fail("No wallet found for Transfer");
}

if (isset($_POST["amount"])) {
    $amount = $_POST["amount"];
} else {
    $amount = 0;
}

if (isset($_POST["type"])) {
    $type = $_POST["type"];
} else {
    $fail = true;
}

$amount = str_replace(",", "", $amount);
$wallet = str_replace(",", "", $wallet);

if (!is_numeric($amount)) {
    $fail = true;
}

if (!$fail) {

    if (trim($amount) == "" || !is_numeric($amount)) {
        Fail("Bad Data");
    } else {
        switch ($type) {
            case "paycheck":
                $amount *= -1;
                $newWallet = $wallet + $amount;

                $sql = "Call sp_PaycheckTransaction(" . $userId . ", '" . $theName . "', " . $amount . ", '" . $date . "')";

                $result = mysql_query($sql, $conn) or die(mysql_error());

                $sql = "Call sp_UpdateUserIsk(" . $newWallet . ", " . $userId . ")";

                $result = mysql_query($sql, $conn) or die(mysql_error());

                Success();
                break;
            case "corp":
                $newWallet = $wallet + $amount;

                $sql = "Call sp_CorpTransaction(" . $userId . ", '" . $theName . "', " . $amount . ", '" . $date . "', '" . $addText . "')";

                $result = mysql_query($sql, $conn) or die(mysql_error());

                $sql = "Call sp_UpdateUserIsk(" . $newWallet . ", " . $userId . ")";

                $result = mysql_query($sql, $conn) or die(mysql_error());

                Success();
                break;
            default:
                Fail("Bad Data");
                break;
        }
    }


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