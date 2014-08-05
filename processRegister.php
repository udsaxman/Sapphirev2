<?php
include 'connection.php';

session_start();

$theName = "";
$passwordOne = "";
$passwordTwo = "";

$fail = false;

$userSalt = "";

if (isset($_POST["name"])) {
    $theName = $_POST["name"];

    $strSearch = "'";
    $strSearch2 = "*";
    $strSearch3 = "\"";

    $pos = stripos($theName, $strSearch);
    $pos2 = stripos($theName, $strSearch2);
    $pos3 = stripos($theName, $strSearch3);

    if ($pos === false && $pos2 === false && $pos3 === false) {
    } else {
        $fail = true;
    }

    if (trim($theName) == "") {
        $fail = true;
    }
} else {
    $fail = true;
}

if (isset($_POST["passwordOne"])) {
    $passwordOne = $_POST["passwordOne"];
} else {
    $fail = true;
}

if (isset($_POST["passwordTwo"])) {
    $passwordTwo = $_POST["passwordTwo"];
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

$valid = 1;

$count = 0;

function fail()
{
    header('Location: ./TDSInError.php?Error=Failed_To_Register_Please_Try_Again');
}

function success()
{
    header('Location: ./TDSInPlayerPage.php');
}

if ($passwordOne != $passwordTwo || $fail) {
    fail();
} else {


    $sql = "Select LOWER(user_name) from Users where= '" . strtolower($theName) . "'";
    //$sql = "SELECT user_name FROM Users";
    $result = $mysqli->query($sql);
    if ($result->num_rows != 0) {
        // User not found. So, redirect to login_form again.
        $valid = 0;
    }

    $userSalt = uniqid();

    $hash = crypt($passwordOne, '$6$rounds=10000$' + $userSalt + '$');

    if ($valid != 0) {
        //id , user_name , password , isk , rank
        //rank was changed to 5 by default so admins are considered 0
        $sql = "Call sp_AddNewUser('" . $theName . "', '" . $hash . "', '" . $userSalt . "')";
        $result = mysql_query($sql, $conn) or die(mysql_error());

        $_SESSION["userName"] = $theName;

        success();
    } else {
        fail();
    }
}

?>