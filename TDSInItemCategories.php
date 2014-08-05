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
    <?php
    //This could be a good place to add in allowing people to ONLY process ops

    $powerRequired = 100;

    include 'connection.php';
    include 'functions.php';
    $powerRequired = CheckAccess('item_category');

    if (isset($_SESSION["power"]) && isset($_SESSION["userName"])) {
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
        global $conn;
        echo "<form action = 'processItemCategories.php' method = 'post'>";
        echo "<fieldset>";
        echo "<legend>Item Categories</legend>";

        $catIds[0] = 0;
        $catNames[0] = "None";
        $catOrders[0] = 0;
        $catTaxs[0] = 0;
        $catOverrides[0] = 0;
        $catCount = 0;

        $sql = "Select * From Item_Category Order By category_order";
        $result = mysql_query($sql, $conn) or die(mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            foreach ($row as $name => $value) {
                if ($name == "category_id") {
                    $catIds[$catCount] = $value;
                }
                if ($name == "category_name") {
                    $catNames[$catCount] = $value;
                }
                if ($name == "category_order") {
                    $catOrders[$catCount] = $value;
                }
                if ($name == "category_taxOverride") {
                    $catTaxs[$catCount] = $value;
                }
                if ($name == "category_useOverride") {
                    $catOverrides[$catCount] = $value;
                    $catCount++;
                }
            }
        }

        echo "<table class='Display' border=''>";

        echo "<tr>";
        echo "<th>Category Name</th>";
        echo "<th>Category Order</th>";
        echo "<th>Category Tax</th>";
        echo "<th>Use Tax</th>";
        echo "</tr>";

        for ($i = 0; $i < $catCount; $i++) {
            echo "<tr>";
            echo "<td><input type = 'text' size='35' name = 'oldCatName" .
                $catIds[$i] . "' value = '" . $catNames[$i] . "' /></td>";
            echo "<td><input type = 'text' size='5' name = 'oldCatOrder" .
                $catIds[$i] . "' value = '" . $catOrders[$i] . "' /></td>";
            echo "<td><input type = 'text' size='10' name = 'oldCatTax" .
                $catIds[$i] . "' value = '" . $catTaxs[$i] . "' /></td>";
            if ($catOverrides[$i] == 1) {
                echo "<td><input type = 'checkbox' name = 'oldCatOverride" .
                    $catIds[$i] . "' checked='checked' value = 'yes' /></td>";
            } else {
                echo "<td><input type = 'checkbox' name = 'oldCatOverride" .
                    $catIds[$i] . "' value = 'yes' /></td>";
            }
        }
        echo "</table>";
        echo "</fieldset>";

        echo "<fieldset>";
        echo "<legend>New Categories</legend>";
        echo "<div id = 'divOutput'>";
        echo "</div>";
        echo "</fieldset>";
        echo "<input type = 'submit' value = 'Submit Changes'  />";

        echo "<button type = 'button' onclick='newCategory()'>";
        echo "Add Category";
        echo "</button>";
    }

    function AccessDenied($error)
    {
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

    <script type="text/javascript">
        //<![CDATA[
        //from TDSInItems.php
        var count = 0;

        function newCategory() {
            var result = "";
            result += "<label>CategoryName:</label>";
            result += "<input type = 'text' name = 'newCatName" + count + "' />";
            result += "<label>CategoryTax:</label>";
            result += "<input type = 'text' style='text-align:right' name = 'newCatTax" + count + "'  value = '0' />";
            result += "<label>UseTax:</label>";
            result += "<input type = 'checkbox' name = 'newCatOverride" + count + "' />";
            result += "<label>CategoryOrder:</label>";
            result += "<input type = 'text' name = 'newCatOrder" + count + "' />";
            result += "<br />";
            divOutput.innerHTML += result;

            count += 1;
        }
        //]]>
    </script>

</div>
<!-- InstanceEndEditable -->
<?php include('footer.html'); ?>
</DIV>

</td>
</tr>
</table>
</body>

<!-- InstanceEnd --></html>
