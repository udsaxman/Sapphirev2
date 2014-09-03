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
        global $mysqli;
        echo "<form action = 'processItemCategories.php' method = 'post'>";
        echo "<fieldset>";
        echo "<legend>Item Categories</legend>";

        $sql = "select * from Item_Category order by category_order";

        $result = $mysqli->query($sql);

        while ($row = $result->fetch_array(MYSQLI_BOTH)) {
            $categories[] = $row;
        }
        echo "<table class='Display' border=''>";

        echo "<tr>";
        echo "<th>Category Name</th>";
        echo "<th>Category Order</th>";
        echo "<th>Category Tax</th>";
        echo "<th>Use Tax</th>";
        echo "</tr>";

        foreach($categories as $category) {
            echo "<tr>";
            echo "<td>".$category['category_name']."</td>";
            echo "<input type='hidden' name='catupdate[".$category['category_id']."][id]' value ='".$category['category_id']."'/></td>";
            echo "<td><input type = 'text' name = 'catupdate[".$category['category_id']."][order]' value = '" . $category['category_order'] . "' /></td>";
            echo "<td><input type = 'text' name = 'catupdate[".$category['category_id']."][tax]' value = '" . $category['category_taxOverride'] . "' /></td>";
            if ($category['category_useOverride'] == 1) {
                echo "<td><input type = 'checkbox' name = 'catupdate[".$category['category_id']."][override]' checked='checked' value = 'yes' /></td>";
            } else {
                echo "<td><input type = 'checkbox' name = 'catupdate[".$category['category_id']."][override]' value = 'yes' /></td>";
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
            result += "<input type = 'text' name = 'newCat[" + count + "][name]' />";
            result += "<label>CategoryTax:</label>";
            result += "<input type = 'text' style='text-align:right' name = 'newCat[" + count + "][tax]'  value = '0' />";
            result += "<label>UseTax:</label>";
            result += "<input type = 'checkbox' name =  'newCat[" + count + "][override]' />";
            result += "<label>CategoryOrder:</label>";
            result += "<input type = 'text' name = 'newCat[" + count + "][order]' />";
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
