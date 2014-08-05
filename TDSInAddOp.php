<?php
session_start();
include('header.html');
?>
<!-- InstanceBeginEditable name="content" -->
<div id="content_area" align="left">

<br/>
<br/>
<br/>
<br/>

<script>
    function validateForm() {
        var x = document.forms["addOpForm"]["AddedPilotCountYo"];
        if (x == null || x.value == "" || x.value < 1) {
            alert("O shit, No Pilots have Been Added!");
            return false;
        }

        for (var i = 0; i < x.value; i++) {
            var shares = document.forms["addOpForm"]["Shares" + i].value;

            if (shares < 1) {
                alert("O shit, One or more Pilot(s) has less then one share!");
                return false;
            }
        }
    }
</script>

<form name="addOpForm" action="processOp.php" onsubmit="return validateForm()" method="post">
<fieldset>
<legend>Zeh Op</legend>
<?php
$userIdList[0] = 0;
$userCount = 1; //start at 1 because of -= Select Pilot =-
$userList[0] = "None";
$userIdList[0] = 0;
//$stupidList[0] = 0; ///////   I AM DONE
$userIdList[1] = 2;
$javaCount = 0;

include 'connection.php';

$powerRequired = 100;

$sql = "Select
								access_power
							From
								Access
							Where
								access_page = 'add_op'";

$result = mysql_query($sql, $conn) or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "access_power") {
            $powerRequired = $value;
        }
    }
}

if (isset($_SESSION["power"])) {
    if ($_SESSION["power"] >= $powerRequired) {
        AccessGranted();
    } elseif ($_SESSION["power"] < $powerRequired) {
        AccessDenied();
    }
} else {
    AccessDenied();
}

function AccessGranted()
{
    global $conn, $userIdList, $userCount, $userList;

    if (isset($_SESSION["userName"])) {
        $theName = $_SESSION["userName"];
    } else {
        $theName = "Error_No_Name";
    }

    $today = date("Y.m.d");

    $itemId[0] = 0;
    $itemName[0] = "None";
    $itemType[0] = 0;
    $itemValue[0] = 0;
    $itemCount = 0;

    $sql = "SELECT * FROM Items order by item_order";
    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "item_id") {
                //What happens when an entry is deleted, do the tables fill in the gap? yes?
                $itemId[$itemCount] = $value;
            }
            if ($name == "item_name") {
                $itemName[$itemCount] = $value;
            }
            if ($name == "item_type") {
                $itemType[$itemCount] = $value;
                $itemCount++;
            }
        }
    }

    $sql = "Select
									user_id, user_name
								From
									Users
								left join
									Ranks on Users.rank_id = Ranks.rank_id
								Where
									rank_power > 0
								order by user_name";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "user_id") {
                $userIdList[$userCount] = $value;
            }
            if ($name == "user_name") {
                $userList[$userCount] = $value;
                $userCount++;
            }
        }
    }

    $categoryIds[0] = 0;
    $categoryName[0] = "";
    $categoryOrder[0] = 0;
    $categoryTax[0] = 0;
    $categoryOverride[0] = 0;
    $categoryCount = 0;

    $sql = "select * from Item_Category order by category_order";
    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "category_id") {
                $categoryIds[$categoryCount] = $value;
            }
            if ($name == "category_name") {
                $categoryName[$categoryCount] = $value;
            }
            if ($name == "category_order") {
                $categoryOrder[$categoryCount] = $value;
            }
            if ($name == "category_taxOverride") {
                $categoryTax[$categoryCount] = $value;
            }
            if ($name == "category_useOverride") {
                $categoryOverride[$categoryCount] = $value;
                $categoryCount++;
            }
        }
    }

    $speedItemCount = 0;
    $speedItemIdsYo[0] = 0;
    $speedItemAmountsYo[0] = 0;

    while (isset($_REQUEST["SpeedId" . $speedItemCount])) {
        $speedItemIdsYo[$speedItemCount] = $_REQUEST["SpeedId" . $speedItemCount];
        $speedItemAmountsYo[$speedItemCount] = $_REQUEST["SpeedAmount" . $speedItemCount];
        $speedItemCount++;
    }

    $tabIndex = 0;

    echo "<label>Op Processor:</label>";
    echo "<input type = 'text' readonly='readonly' tabindex='"
        . ($tabIndex + 100) . "' name = 'name' value = '" . $theName . "'  />";

    $tabIndex++;
    echo "<br />";

    echo "<label>Op Name:</label>";
    echo "<input type='text' name = 'opName' tabindex='"
        . $tabIndex . "' value = 'DefaultOpName' />";

    $tabIndex++;
    echo "<br />";

    echo "<label>Op Tax %:</label>";
    echo "<input type='text' tabindex='"
        . $tabIndex . "' name = 'opTax' value = '0' />";

    $tabIndex++;
    echo "<br />";

    echo "<label>Process Date:</label>";
    echo "<input type='text' readonly='readonly' tabindex='"
        . ($tabIndex + 100) . "' name = 'opDate' value = '" . $today . "' />";
    echo "<br />";

    echo "<p>Total Pilots: ";
    echo "<input type = 'text' tabindex='"
        . $tabIndex . "' id = 'txtPilots' value = '0' />";

    echo "<button type = 'button' onclick='newPilot()'>";
    echo "Add Pilots";
    echo "</button>";
    echo "</p>";

    echo "<div id = 'divOutput'>";
    echo "</div>";

    for ($z = 0; $z < $categoryCount; $z++) {
        echo "<fieldset>";
        if ($categoryOverride[$z] == 0) {
            echo "<legend>" . $categoryName[$z] . "</legend>";
        } else {
            echo "<legend>" . $categoryName[$z] . " - Tax Override: " . $categoryTax[$z] . "%</legend>";
        }

        echo "<table class='Display' border=''>";

        echo "<tr>";
        echo "<th>Item Name</th>";
        echo "<th>Amount</th>";
        echo "</tr>";

        for ($i = 0; $i < $itemCount; $i++) {
            if ($itemType[$i] == $categoryIds[$z]) {
                echo "<tr>";
                echo "<td><input type = 'text' tabindex='"
                    . ($tabIndex + 100) . "' size='35' readonly='readonly'
												name = 'itemName" . $itemId[$i] . "' value = '"
                    . $itemName[$i] . "' /></td>";
                $displayAmount = 0;
                for ($x = 0; $x < $speedItemCount; $x++) {
                    if ($speedItemIdsYo[$x] == $itemId[$i]) {
                        $displayAmount = $speedItemAmountsYo[$x];
                        break;
                    }
                }
                echo "<td><input type = 'text' tabindex='"
                    . $tabIndex . "' name = 'itemAmount"
                    . $itemId[$i] . "' value = '" . $displayAmount . "' /></td>";

                echo "</tr>";

                $tabIndex++;
            }
        }
        echo "</table>";
        echo "</fieldset>";
    }

    echo "<input type = 'submit' value = 'Confirm Op'  />";
}

function AccessDenied()
{
    echo "<p>Nice try, but no. You are not allowed to be here<p>";
}

?>
</fieldset>
</form>

<?php

//Hey there kids! Ever wanna know how to write javascript code that writes HTML code in php code?
//Well here we go!

echo "<script type = 'text/javascript'>";
echo "\n";
echo "//<![CDATA[";
echo "\n";
echo "//from TDSInAddOp.php";
echo "\n";

echo "function newPilot()";
echo "\n";
echo "{";
echo "\n";
echo "var result = \"\";";
echo "\n";
echo "var pilots = txtPilots.value;";
echo "\n";
echo "var users = new Array();";
echo "\n";
echo "var usersIds = new Array();";
echo "\n";
echo "var count = " . ($userCount + 1) . ";";
echo "\n";

echo "\n";
echo "users[0] = \"-= Select Pilot =-\";";
echo "\n";
echo "usersIds[0] = 0;";
echo "\n";
for ($x = 1; $x < $userCount; $x++) {
    echo "users[" . $x . "] = \"" . $userList[$x] . "\";";
    echo "\n";
    echo "usersIds[" . $x . "] = " . $userIdList[$x] . ";";
    echo "\n";
}
echo "result += \"<input type = 'hidden' name = 'AddedPilotCountYo' value = '\"+pilots+\"' />\";";

echo "for(var i = 0; i < pilots; i++)";
echo "\n";
echo "{";
echo "\n";
echo "result += \"<label>Pilot Name:</label>\";";
echo "\n";
echo "result += \"<select name = 'selType\" + i + \"'>\"";
echo "\n";
echo "for(var y = 0; y < users.length; y++)";
echo "\n";
echo "{";
echo "\n";
echo "result += \"<option value = '\"+usersIds[y]+\"'>\"+users[y]+\"</option>\";";
echo "\n";
echo "}";
echo "\n";
echo "result += \"</select>\"";
echo "\n";
echo "result += \"<label>Shares:</label>\";";
echo "\n";
echo "result += \"<input type = 'text' name = 'Shares\" + i + \"' />\";";
echo "\n";
echo "result += \"<br />\";";
//divOutput.innerHTML += result;
echo "\n";
echo "}";
echo "\n";
//result += "<p>"+ count +"</p>";
echo "divOutput.innerHTML = result;";

echo "\n";
echo "}";
echo "\n";
echo "//]]>";
echo "\n";
echo "</script>";
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
