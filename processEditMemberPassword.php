<?php
include 'connection.php';

$fail = false;

$passwordOne = "";
$passwordTwo = "";

$targetUserID = 0;

if (isset($_POST["password1"])) {
    $passwordOne = $_POST["password1"];
} else {
    $fail = true;
}

if (isset($_POST["password2"])) {
    $passwordTwo = $_POST["password2"];
} else {
    $fail = true;
}

if (isset($_POST["selUser"])) {
    $targetUserID = $_POST["selUser"];
    if ($targetUserID == 0)
        $fail = true;
} else {
    $fail = true;
}

$strSearch = "'";
//$strSearch2 = "*";
$strSearch3 = "\"";

$pos = stripos($passwordOne, $strSearch);
//$pos2 = stripos($passwordOne, $strSearch2);
$pos3 = stripos($passwordOne, $strSearch3);

if ($pos === false && $pos3 === false) {
} else {
    $fail = true;
}

if (trim($passwordOne) == "")
    $fail = true;

if ($passwordOne != $passwordTwo)
    $fail = true;

if (!$fail) {
    $userSalt = uniqid();
    $newHash = crypt($passwordOne, '$6$rounds=10000$' + $userSalt + '$');
    $sql = "Call sp_UpdateUserPassword(" . $targetUserID . ", '" . $newHash . "', '" . $userSalt . "')";
    $result = mysql_query($sql, $conn) or die(mysql_error());
    $sql = "Call sp_UpdateUserReset(" . $targetUserID . ", 1)";
    $result = mysql_query($sql, $conn) or die(mysql_error());
    $sql = "Call sp_UpdateUserLock(" . $targetUserID . ", 0)";
    $result = mysql_query($sql, $conn) or die(mysql_error());
    //echo $sql;
    Success();
} else {
    Fail("You failed to change the password of the target user");
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