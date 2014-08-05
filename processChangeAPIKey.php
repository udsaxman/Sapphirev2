<?php

$fail = false;
$key_id = 0;

if (isset($_POST["key"])) {
    $key_id = $_POST["key"];
} else {
    $fail = true;
}

include 'connection.php';

if (IsProduction()) {
    mysql_select_db("eve.tdsin");
} else {
    mysql_select_db("naed_Sapphire");
}


if (!$fail) {
    $sql = "CALL sp_ChangeActiveAPIKey(" . $key_id . ")";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    Success();
} else {
    Fail("No Key was selected");
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