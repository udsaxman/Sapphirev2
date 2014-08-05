<?php
session_start();
include 'header.html';
include 'connection.php';
include 'functions.php'
?>

<!-- InstanceBeginEditable name="content" -->
<div id="content_area" align="left">

<br/>
<br/>
<br/>
<br/>

<form action="TDSInTransfer.php"
      method="post">
<fieldset>
<legend>Transfer Type</legend>
<?php

//include 'connection.php';
//include 'functions.php';
$powerRequired = 100;

$powerRequired = CheckAccess('transfer');
//$sql = "Select
//			access_power
//		From
//			Access
//		Where
//			access_page = 'transfer'";
//
//$result = mysql_query($sql, $conn) or die(mysql_error());
//
//while ($row = mysql_fetch_assoc($result)) {
//    foreach ($row as $name => $value) {
//        if ($name == "access_power") {
//            $powerRequired = $value;
//        }
//    }
//}

if (isset($_SESSION["power"])) {
    if ($_SESSION["power"] >= $powerRequired) {
        AccessGranted();
    } elseif ($_SESSION["power"] < $powerRequired) {
        AccessDenied(1);
    }
} else {
    AccessDenied(0);
}

function AccessGranted()
{
    include 'connection.php';
    $typeCount = 1;
    $typeName[0] = "";

    $selectedType = 0;

    global $conn;

    $sql = "SELECT * FROM Transaction_Types";
    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "description") //This should be changed to Name, fix this when you stored proceedure all the things
            { //Nope, fix it later
                $typeName[$typeCount] = $value;
                $typeCount++;
            }
        }
    }

    if (isset($_REQUEST["selType"])) {
        $selectedType = $_REQUEST["selType"];
    }

    echo "<select name = 'selType'>";
    for ($i = 0; $i < $typeCount; $i++) //Base 1
    {
        if (($typeName[$i] == "corp")) {
            echo "<option value = " . $i;
            if ($i == $selectedType) {
                echo " selected = 'selected'";
            }
            echo ">";

            echo $typeName[$i] . "</option>";
        }
        if (($typeName[$i] == "paycheck")) {
            echo "<option value = " . $i;
            if ($i == $selectedType) {
                echo " selected = 'selected'";
            }
            echo ">";

            echo $typeName[$i] . "</option>";
        }
    }
    echo "</select>";

    echo "<input type = 'submit' value = 'Select Type'  />";

    if (isset($_REQUEST["selType"])) {
        $i = $_REQUEST["selType"];
        if ($typeName[$i] == "paycheck") {
            Paycheck();
        } elseif ($typeName[$i] == "corp") {
            Corp();
        }
    }
}

function Paycheck()
{
    include 'connection.php';
    $theName = "";
    $today = date("Y.m.d");

    $userArray[0] = "";
    $userIdArray[0] = 0;
    $iskArray[0] = 0;

    $userCount = 0;


    $sql = "Select
										rank_power, user_id, user_name, user_isk
									From
										Users
									left join
										Ranks on Users.rank_id = Ranks.rank_id
									Where
										rank_power > 0
									Order By
										user_name";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "rank_power") {
            }
            if ($name == "user_id") {
                $userIdArray[$userCount] = $value;
            }
            if ($name == "user_name") {
                $userArray[$userCount] = $value;
            }
            if ($name == "user_isk") {
                $iskArray[$userCount] = $value;
                $userCount++;
            }
        }
    }

    if (isset($_SESSION["userName"])) {
        $theName = $_SESSION["userName"];
    } else {
        $theName = "Error_No_Name_Found";
    }

    echo "</fieldset>";
    echo "</form>";
    echo "<br />";
    echo "<form action = 'processTransfer.php' method = 'post'>";
    echo "<fieldset>";
    echo "<legend>Paycheck</legend>";

    echo "<input type = 'hidden' name = 'type' value = 'paycheck' />";

    echo "<br />";
    echo "<label>Transfer Conductor:</label>";
    echo "<input type = 'text' readonly='readonly' name = 'name' value ='" . $theName . "' />";

    echo "<br />";
    echo "<label>Transfer Date:</label>";
    echo "<input type = 'text' readonly='readonly' name = 'date' value ='" . $today . "' />";

    echo "<br />";
    echo "<select id='select_user' name = 'selUser'>";
    echo "<option value =0>-=Select Pilot=-</option>";
    for ($i = 1; $i < $userCount + 1; $i++) {
        echo "<option value =" . $userIdArray[$i - 1] . ">" . $userArray[$i - 1] . "</option>";
    }
    echo "</select>";

    echo "<br />";
    echo "<label>Current Wallet:</label>";
    echo "<input type = 'text' readonly='readonly' id = 'user_wallet' name = 'wallet' value ='~~!~~' />";

    echo "<br />";
    echo "<label>Amount to Removed:</label>";
    echo "<input type = 'text' name = 'amount' value = '0' />";

    echo "<br />";
    echo "<input type = 'submit' value = 'Transfer Isk'  />";

    echo "</fieldset>";
    echo "</form>";
    echo "\n\n";
    echo "<script type='text/javascript'>";
    echo "\n";
    echo "window.onload = function()";
    echo "\n";
    echo "{";
    echo "\n\t";
    echo "var isk = new Array();";
    echo "\n\t";
    echo "isk[0] = \"~~!~~\";";
    echo "\n\t";
    for ($x = 1; $x < $userCount + 1; $x++) {
        echo "isk[" . ($x) . "] = \"" . number_format($iskArray[$x - 1], 2, '.', ',') . "\";";
        echo "\n\t";
    }
    echo "var eSelect = document.getElementById('select_user');";
    echo "\n\t";
    echo "eSelect.onchange = function ()";
    echo "\n\t";
    echo "{";
    echo "\n\t\t";
    echo "document.getElementById(\"user_wallet\").value = isk[eSelect.selectedIndex];";
    echo "\n\t";
    echo "}";
    echo "\n";
    echo "}";
    echo "\n";
    echo "</script>";
    echo "\n";
}

function Corp()
{
     include 'connection.php';
    $theName = "";
    $today = date("Y.m.d");

    $userArray[0] = "";
    $userIdArray[0] = 0;
    $iskArray[0] = 0;

    $userCount = 0;


    $sql = "Select
										rank_power, user_id, user_name, user_isk
									From
										Users
									left join
										Ranks on Users.rank_id = Ranks.rank_id
									Where
										rank_power > 0
									Order By
										user_name";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "rank_power") {
            }
            if ($name == "user_id") {
                $userIdArray[$userCount] = $value;
            }
            if ($name == "user_name") {
                $userArray[$userCount] = $value;
            }
            if ($name == "user_isk") {
                $iskArray[$userCount] = $value;
                $userCount++;
            }
        }
    }

    if (isset($_SESSION["userName"])) {
        $theName = $_SESSION["userName"];
    } else {
        $theName = "Error_No_Name_Found";
    }

    echo "</fieldset>";
    echo "</form>";
    echo "<br />";
    echo "<form action = 'processTransfer.php' method = 'post'>";
    echo "<fieldset>";
    echo "<legend>Corp Transaction</legend>";

    echo "<input type = 'hidden' name = 'type' value = 'corp' />";

    echo "<br />";
    echo "<label>Transfer Conductor:</label>";
    echo "<input type = 'text' readonly='readonly' name = 'name' value ='" . $theName . "' />";

    echo "<br />";
    echo "<label>Transfer Date:</label>";
    echo "<input type = 'text' readonly='readonly' name = 'date' value ='" . $today . "' />";

    echo "<br />";
    echo "<select id='select_user' name = 'selUser'>";
    echo "<option value =0>-=Select Pilot=-</option>";
    for ($i = 1; $i < $userCount + 1; $i++) {
        echo "<option value =" . $userIdArray[$i - 1] . ">" . $userArray[$i - 1] . "</option>";
    }
    echo "</select>";

    echo "<br />";
    echo "<label>Current Wallet:</label>";
    echo "<input type = 'text' readonly='readonly' id = 'user_wallet' name = 'wallet' value ='~~!~~' />";

    echo "<br />";
    echo "<label>Amount to Add:</label>";
    echo "<input type = 'text' name = 'amount' value = '0' />";
    echo "<br />";
    echo "<label>Addition Text:</label>";
    echo "<input type = 'text' name = 'addText' value = '' />";

    echo "<br />";
    echo "<input type = 'submit' value = 'Transfer Isk'  />";

    echo "</fieldset>";
    echo "</form>";
    echo "\n\n";
    echo "<script type='text/javascript'>";
    echo "\n";
    echo "window.onload = function()";
    echo "\n";
    echo "{";
    echo "\n\t";
    echo "var isk = new Array();";
    echo "\n\t";
    echo "isk[0] = \"~~!~~\";";
    echo "\n\t";
    for ($x = 1; $x < $userCount + 1; $x++) {
        echo "isk[" . $x . "] = \"" . number_format($iskArray[$x - 1], 2, '.', ',') . "\";";
        echo "\n\t";
    }
    echo "var eSelect = document.getElementById('select_user');";
    echo "\n\t";
    echo "eSelect.onchange = function ()";
    echo "\n\t";
    echo "{";
    echo "\n\t\t";
    echo "document.getElementById(\"user_wallet\").value = isk[eSelect.selectedIndex];";
    echo "\n\t";
    echo "}";
    echo "\n";
    echo "}";
    echo "\n";
    echo "</script>";
    echo "\n";
}

function AccessDenied($error)
{
    echo "</fieldset>";
    echo "</form>";

    switch ($error) {
        case 0: //There is no Admin in Session, you have not loggin in yet
            echo "You have not yet logged in yet, please log in";
            break;
        case 1: //You are Not an Admin
            echo "You do not have the rights to view this page";
            break;
        default:
            echo "You have been denied access to this page";
    }
}

?>

</div>
<!-- InstanceEndEditable -->
<footer align="center">
    EVE Online, the EVE logo, EVE and all associated logos and designs are the intellectual property of CCP hf.
    <br/>
    All artwork, screenshots, characters, vehicles, storylines, world facts or other recognizable features of the
    intellectual property relating to these trademarks are likewise the intellectual property of CCP hf.
    <br/>
    EVE Online and the EVE logo are the registered trademarks of CCP hf. All rights are reserved worldwide. All other
    trademarks are the property of their respective owners.
    <br/>
    CCP hf. has granted permission to tdsin.net to use EVE Online and all associated logos and designs for promotional
    and information purposes on its website but does not endorse, and is not in any way affiliated with, tdsin.net.
    <br/>
    CCP is in no way responsible for the content on or functioning of this website, nor can it be liable for any damage
    arising from the use of this website.
</footer>
</DIV>

</td>
</tr>
</table>
</body>

<!-- InstanceEnd --></html>
