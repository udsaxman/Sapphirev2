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
    global $mysqli;

    $sql = "SELECT
              item_id,
              item_name,
              item_type,
              item_iskValue,
              item_order
            FROM Items";
    $result = $mysqli->query($sql);

    while ($row = $result->fetch_array(MYSQLI_BOTH)) {
        $Items[] = $row;
    }
    $result->free();

    $sql = "select * from Item_Category order by category_order";

    $result = $mysqli->query($sql);

    while ($row = $result->fetch_array(MYSQLI_BOTH)) {
        $categories[] = $row;
    }
    $result->free();
    //copy categories array for generating the dropdowns.  Reuse of categories would mess with its internal pointer so better safe than sorry here
    $categorylist = $categories;
    print_r ($categorylist);

    foreach($categories as $category) {
        echo "<fieldset>";
        if ($category['category_useOverride'] == 0) {
            echo "<legend>" . $category['category_name'] . "</legend>";
        } else {
            echo "<legend>" . $category['category_name'] . " - Tax Override: " . $category['category_taxOverride'] . "%</legend>";
        }

        echo "<table class='Display' border=''>";
        echo "<tr>";
        echo "<th>Item Name</th>";
        echo "<th>Item Value</th>";
        echo "<th>Item Order</th>";
        echo "<th>Item Category</th>";
        echo "<tr>";

        foreach ($Items as $item) {
            if ($item['item_type'] == $category['category_id']) {
                echo "<tr>";
                echo "<td>".$item['item_name']."</td>";
                echo "<input type='hidden' name='itemupdate[".$item['item_id']."][id]' value ='".$item['item_id']."'/></td>";
                echo "<td><input type = 'text' style='text-align:right' name = 'itemupdate[".$item['item_id']."][value]' value = '" . number_format($item['item_iskValue'], 0, '.', ',') . "' /></td>";
                echo "<td><input type = 'text' name = 'itemupdate[".$item['item_id']."][order]' value = '" . $item['item_order'] . "' /></td>";
                echo "<td><select name = 'itemupdate[".$item['item_id']."][type]'>";
                foreach ($categorylist as $categoryoption){
                    echo "<option value=".$categoryoption['category_id'];
                    if ($categoryoption['category_id'] == $item['item_type']){
                        echo " selected = 'selected'";
                    }
                    echo ">".$categoryoption['category_name'];
                    echo "</option>";
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
<?php include('footer.html'); ?>
</DIV>

</td>
</tr>
</table>
</body>

<!-- InstanceEnd --></html>
