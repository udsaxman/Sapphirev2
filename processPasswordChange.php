<?php
include 'connection.php';

session_start();

$theName = "";
$passOne = "";
$passTwo = "";
$passOld = "";
$fail = false;

$passHash = "";
$userId = 0;

$userSalt = "";
$isLockedYo = false;

if (isset($_SESSION["userName"])) {
    $theName = $_SESSION["userName"];
} else {
    $fail = true;
}

if (isset($_POST["oldPass"])) {
    $passOld = $_POST["oldPass"];
} else {
    $fail = true;
}

if (isset($_POST["newPass1"])) {
    $passOne = $_POST["newPass1"];
} else {
    $fail = true;
}

if (isset($_POST["newPass2"])) {
    $passTwo = $_POST["newPass2"];
} else {
    $fail = true;
}

if ($passOne != $passTwo) {
    $fail = true;
}

if (trim($passOne) == "") {
    $fail = true;
}

if (!$fail) {

    $sql = "Select
				user_id, 
				user_password,
				user_salt,
				isLocked
			From 
				Users 
			Where 
				user_name = '" . strtolower($theName) . "'";

    $result = $mysqli->query($sql);
    //$result = mysql_query($sql, $conn) or die(mysql_error());

    $userData = mysqli_fetch_array($result, MYSQL_ASSOC);

    $userId = $userData['user_id'];
    $passHash = $userData['user_password'];
    $userSalt = $userData['user_salt'];
    if ($userData['isLocked'] == 1) {
        $isLockedYo = true;
    }


    $hash = crypt($passOld, '$6$rounds=10000$' + $userSalt + '$');


    if ($hash == $passHash) {
        global $userId;

        $userSalt = uniqid();
        $newHash = crypt($passOne, '$6$rounds=10000$' + $userSalt + '$');
        $sql = "Call sp_UpdateUserPassword(" . $userId . ", '" . $newHash . "', '" . $userSalt . "')";
        $result = $mysqli->query($sql);
        //$result = mysql_query($sql, $conn) or die(mysql_error());

        $sql = "Call sp_UpdateUserReset(" . $userId . ", 0)";
        $result = $mysqli->query($sql);
        //$result = mysql_query($sql, $conn) or die(mysql_error());

        $sql = "Call sp_UpdateUserLock(" . $userId . ", 0)";
        $result = $mysqli->query($sql);
        //$result = mysql_query($sql, $conn) or die(mysql_error());

        success();
    } else {
        fail2();
    }
} else {
    fail();
}

function fail()
{
    header('Location: ./TDSInError.php?Error=Failed_To_Change_Password');
}

function fail2()
{
    header('Location: ./TDSInError.php?Error=Incorrect_Password,_how_the_devil_did_you_login?');
}

function success()
{
    header('Location: ./TDSInHome.php');
}

?>
