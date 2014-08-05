<?php
session_start();
include 'header.html';
?>
<!-- InstanceBeginEditable name="content" -->
<div id="content_area" align="left">

<br/>
<br/>
<br/>
<br/>

<form action="processItems.php"
      method="post">
<fieldset>
<legend>Items</legend>
<?php

$categoryCount = 0;
$categoryIds[0] = 0;
$categoryName[0] = "";

include 'connection.php';
include 'functions.php';
$powerRequired = 100;

//$sql = "Select
//								access_power
//							From
//								Access
//							Where
//								access_page = 'edit_items'";
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

$powerRequired = CheckAccess('edit_items');
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
    $itemId[0] = 0;
    $itemName[0] = "None";
    $itemType[0] = 0;
    $itemValue[0] = 0;
    $itemOrder[0] = 0;
    $itemCount = 0;


    $categoryOrder[0] = 0;
    $categoryTax[0] = 0;
    $categoryOverride[0] = 0;


    global $conn, $categoryCount, $categoryIds, $categoryName;

    $sql = "SELECT * FROM Items order by item_order";
    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "item_id") {
                $itemId[$itemCount] = $value;
            }
            if ($name == "item_name") {
                $itemName[$itemCount] = $value;
            }
            if ($name == "item_type") {
                $itemType[$itemCount] = $value;
            }
            if ($name == "item_iskValue") {
                $itemValue[$itemCount] = $value;
            }
            if ($name == "item_order") {
                $itemOrder[$itemCount] = $value;
                $itemCount++;
            }

        }
    }

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
        echo "<th>Item Value</th>";
        echo "<th>Item Order</th>";
        echo "<th>Item Category</th>";
        echo "<tr>";

        for ($i = 0; $i < $itemCount; $i++) {
            if ($itemType[$i] == $categoryIds[$z]) {
                echo "<tr>";
                echo "<td><input type = 'text' size='35'  readonly='readonly' name = 'oldItemname"
                    . $itemId[$i] . "' value = '" . $itemName[$i] . "' /></td>";
                echo "<td><input type = 'text' style='text-align:right' name = 'oldValue"
                    . $itemId[$i] . "' value = '" . number_format($itemValue[$i], 0, '.', ',') . "' /></td>";
                echo "<td><input type = 'text' name = 'oldOrder"
                    . $itemId[$i] . "' value = '" . $itemOrder[$i] . "' /></td>";
                echo "<td><select name = 'oldType" . $itemId[$i] . "'>";
                for ($q = 0; $q < $categoryCount; $q++) {
                    if ($categoryIds[$q] == $itemType[$i]) {
                        echo "<option value = '" . $categoryIds[$q] . "' selected='selected'>" . $categoryName[$q] . "</option>";
                    } else {
                        echo "<option value = '" . $categoryIds[$q] . "'>" . $categoryName[$q] . "</option>";
                    }
                }
                echo "</select></td>";
                echo "</tr>";
            }
        }

        echo "</table>";
        echo "</fieldset>";
    }

    echo "<fieldset>";
    echo "<legend>New Items</legend>";
    echo "<div id = 'divOutput'>";
    echo "</div>";
    echo "</fieldset>";
    echo "<input type = 'submit' value = 'Submit Changes'  />";

    echo "<button type = 'button' onclick='newItem()'>";
    echo "Add Items";
    echo "</button>";
}

function AccessDenied()
{
    echo "<p>Nice try, but no. You are not allowed to be here<p>";
}


echo "</fieldset>";
echo "</form>";
echo "</p>";

global $categoryCount, $categoryIds, $categoryName;

echo "\n";
echo "<script type = 'text/javascript'>";
echo "\n";
echo "//<![CDATA[";
echo "\n";
echo "//from TDSInItems.php";
echo "\n";
echo "var count = 0;";
echo "\n";
echo "var catName = new Array();";
echo "\n";
echo "var catIds = new Array();";
echo "\n";
echo "var catCount = " . $categoryCount . ";";
echo "\n";
for ($x = 0; $x < $categoryCount; $x++) {
    echo "catName[" . $x . "] = \"" . $categoryName[$x] . "\";";
    echo "\n";
    echo "catIds[" . $x . "] = \"" . $categoryIds[$x] . "\";";
    echo "\n";
}

echo "function newItem()";
echo "\n";
echo "{";
echo "\n";
echo "var result = '';";
echo "\n";
echo "result += \"<label>ItemName:</label>\";";
echo "\n";
echo "result += \"<input type = 'text' name = 'newItemname\" + count + \"' />\";";
echo "\n";
echo "result += \"<label>ItemType:</label>\";";
echo "\n";
echo "result += \"<select name = 'selType\" + count + \"'>\"";
echo "\n";
echo "for(var y = 0; y < catCount; y++)";
echo "{";
echo "\n";
echo "result += \"<option value = '\"+catIds[y]+\"'>\"+catName[y]+\"</option>\";";
echo "\n";
echo "}";
echo "\n";
echo "result += \"</select>\"";
echo "\n";
echo "result += \"<label>ItemValue:</label>\";";
echo "\n";
echo "result += \"<input type = 'text' style='text-align:right' name = 'newValue\" + count +\"' />\";";
echo "\n";
echo "result += \"<label>ItemOrder:</label>\";";
echo "\n";
echo "result += \"<input type = 'text' name = 'newOrder\" + count +\"' />\";";
echo "\n";
echo "result += \"<br />\";";
echo "\n";
echo "divOutput.innerHTML += result;";
echo "\n";
echo "count += 1;";
echo "\n";
echo "}";
echo "\n";
echo "//]]>";
echo "\n";
echo "</script>";
echo "\n";
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
