<?php
session_start();
include'header.html';
?>

<!-- InstanceBeginEditable name="content" -->
<div id="content_area" align="left">

<br/>
<br/>
<br/>
<br/>
<?php
include 'connection.php';
include 'functions.php';
$powerRequired = 100;

$powerRequired = CheckAccess('edit_op');


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

       //Login Check
    if (isset($_SESSION["userName"])) {

        $selectedOp = 0;
        $OPArray = null;

        global $mysqli;
        global $conn;

        //$sql = select op_name from Ops;
        $sql = "Select op_id, op_name From Ops order by op_id desc LIMIT 0,20";

        $result = $mysqli->query($sql);

        while ($row = $result->fetch_assoc()) {
            $OPArray[] = $row;
        }
        $result->free();

        echo "<form action = 'TDSInEditOp.php' method = ''>";
        echo "<fieldset>";
        echo "<legend>Select Op to Edit</legend>";

        if (isset($_REQUEST["selOp"])) {
            $selectedOp = $_REQUEST["selOp"];
        }

        echo "<select name = 'selOp'>";


        foreach ($OPArray as $oplist){
            echo "<option value=".$oplist['op_id'];
            if ($oplist['op_id'] == $selectedOp){
               echo " selected = 'selected'";
            }
            echo ">".$oplist['op_name'];
            echo "</option>";

        }

        echo "</select>";

        echo "<input type = 'submit' value = 'Select Op'  />";

        //Create form that shows ALL of the op detail
        //op_id = $_REQUEST["selOp"] + 1


        //Btw, deleting stuff is bad
        if (isset($_REQUEST["selOp"])) {

            //Get ALL the opInfo
            $op_ID = ($_REQUEST["selOp"]); //Database is base 1, we are base 0

            $sql = "SELECT op_id, op_name, op_date, op_tax, op_taxAmount,op_shareValue,op_iskValue, user_name as processor_name  FROM Ops JOIN Users where Ops.op_processor = Users.user_id and op_id =" . $op_ID."";
            $result = $mysqli->query($sql);
            $op = $result->fetch_assoc();
            $result->free();
            $opName = $op["op_name"];
            $opProcessor = $op['processor_name'];
            $opDate = $op['op_date'];
            $opTax = $op['op_tax'];
            $opTaxAmount = $op['op_taxAmount'];
            $opShareValue = $op['op_shareValue'];
            $opValue = $op['op_iskValue'];


            //Get all the Attendance Info

            $sql = "Select
                        Users.user_id as id, user_name, shares
                    from
                        Users
                    left join
                            Op_Attendence
                        on
                            op_id = " . $op_ID . "
                        AND
                            Op_Attendence.user_id = Users.user_id
                    where
                        Users.user_id =
                    ANY
                        (select user_id from Op_Attendence where op_id = " . $op_ID . ")
                    group by
                        user_name";

            $result = $mysqli->query($sql);

            while ($row = $result->fetch_array(MYSQLI_BOTH)) {
                $Users[] = $row;
            }
            $result->free();


            $sql = "Select
                        Items.item_id as id, item_name, item_type, amount
                    from
                        Items
                    left join
                            Op_Loot
                        on
                            op_id = " . $op_ID . "
                        AND
                            Op_Loot.item_id = Items.item_id
                    where
                        Items.item_id =
                    ANY
                        (select item_id from Op_Loot where op_id = " . $op_ID . ")
                    group by
                        item_name
                    order by
                        item_type, item_order";

           // $result = mysql_query($sql, $conn) or die(mysql_error());
            $result = $mysqli->query($sql);

            while ($row = $result->fetch_array(MYSQLI_BOTH)) {
                $items[] = $row;
            }



            $sql = "select * from Item_Category order by category_order";

            $result = $mysqli->query($sql);

            while ($row = $result->fetch_array(MYSQLI_BOTH)) {
                $categories[] = $row;
            }
            $result->free();

//
//            $sql = "Select
//                        user_id, user_name
//                    From
//                        Users
//                    left join
//                        Ranks on Users.rank_id = Ranks.rank_id
//                    Where
//                        rank_power > 0
//                    order by user_name";
//
//            $result = $mysqli->query($sql);
//
//            while ($row = $result->fetch_array(MYSQLI_BOTH)) {
//                $Users[] = $row;
//            }
//            $result->free();

            echo "</fieldset>";
            echo "</form>";

            echo "<form action = 'processEditOp.php' method = 'get'>";
            echo "<fieldset>";
            echo "<legend>Edit Op</legend>";

            //Build basic Op information from Ops Table
            echo "<br />";
            echo "<br />";
            echo "Op Information";
            echo "<br />";
            echo "<table class='Display' border=''>";

            echo "<tr>";
            echo "<td>Op Id</td>";
            echo "<td><input type='text' readonly='readonly' name = 'opID' value = '" . $op_ID . "'</tr>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>Op Name</td>";
            echo "<td><input type='text' name = 'opName' value = '" . $opName . "' /></tr>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>Op Processor</td>";
            echo "<td>" . $opProcessor . "</tr>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>Op Process Date</td>";
            echo "<td>" . $opDate . "</tr>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>Op Tax %</td>";
            echo "<td><input type='text' name = 'opTax' value = '" . number_format($opTax, 3, '.', ',') . "' /></tr>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>Op Tax Amount</td>";
            echo "<td style='text-align:right'>" . number_format($opTaxAmount, 2, '.', ',') . "</tr>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>Op Value (After Tax)</td>";
            echo "<td style='text-align:right'>" . number_format($opValue, 2, '.', ',') . "</tr>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>Op Value Per Share</td>";
            echo "<td style='text-align:right'>" . number_format($opShareValue, 2, '.', ',') . "</tr>";
            echo "</tr>";

            echo "</table>";

            //Build Pilot Information from Op_Attendence and Users Tables
            echo "<fieldset>";
            echo "<legend>Pilots</legend>";
            echo "<table class='Display' border=''>";
            echo "<tr>";
            echo "<th>Pilot Name</th>";
            echo "<th>Shares</th>";
            echo "</tr>";


            foreach ($Users as $user) {
                echo "<tr>";
                echo "<td>" . $user['user_name'] . "</td>";
                echo "<td><input type='text' name = 'oldUser" . $user['id'] . "' value = '" . $user['shares'] . "' /></td>";
                echo "</tr>";
            }

            echo "</table>";

            echo "<p>Total Added Pilots: ";
            echo "<input type = 'text' id = 'txtPilots' value = '0' />";
            echo "<button type = 'button' onclick='newPilot()'>";
            echo "Add Pilots";
            echo "</button>";
            echo "</p>";

            echo "<div id = 'divOutput'>";
            echo "</div>";

            echo "</fieldset>";


            $showCat[0] = false;

            foreach ($categories as $category) {
                $showCat[$category['category_id']] = false;
                foreach($items as $item) {
                    if ($item['item_type'] == $category['category_id']) {
                        $showCat[$category['category_id']] = true;
                    }
                }
            }

            echo "<fieldset>";
            echo "<legend>Items</legend>";

            foreach ($categories as $category) {
                if ($showCat[$category['category_id']] == true) {
                    echo "<fieldset>";
                    if ($category['category_useOverride'] == 0) {
                        echo "<legend>" . $category['category_name'] . "</legend>";
                    } else {
                        echo "<legend>" . $category['category_name'] . " - Tax Override: " . $category['category_taxOverride'] . "%</legend>";
                    }

                    echo "<table class='Display' border=''>";
                    echo "<tr>";
                    echo "<th>Item Name</td>";
                    echo "<th>Amount</td>";
                    echo "</tr>";

                    foreach ($items as $item) {
                        if ($item['item_type'] == $category['category_id']) {
                            echo "<tr>";
                            echo "<td>" . $item['item_name'] . "</td>";
                            echo "<td><input type = 'text' name = 'oldItem" .$item['id']. "' value = '" . number_format($item['amount'], 0, '.', ',') . "' /></td>";
                            echo "</tr>";
                        }
                    }

                    echo "</table>";
                    echo "</fieldset>";
                }

            }

            echo "<p>Total Added Items: ";
            echo "<input type = 'text' id = 'txtItems' value = '0' />";
            echo "<button type = 'button' onclick='newItem()'>";
            echo "Add Items";
            echo "</button>";
            echo "</p>";

            echo "<div id = 'divItems'>";
            echo "</div>";

            echo "</fieldset>";
            echo "<input type = 'submit' value = 'Confirm Edit Op'  />";
            //Add new Pilots
            //Add new Items
        }


        echo "</fieldset>";
        echo "</form>";

        echo "<script type = 'text/javascript'>";
        echo "\n";
        echo "//<![CDATA[";
        echo "\n";
        echo "//from TDSInAddOp.php";
        echo "\n";
        $sql = "Select
									user_id, user_name
								From
									Users
								left join
									Ranks on Users.rank_id = Ranks.rank_id
								Where
									rank_power > 0
								order by user_name";
        $result = $mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_BOTH)) {
            $newUsers[] = $row;
        }
        $result->free();
        $newUserCount = count($newUsers['user_name']);

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
        echo "var count = " . $newUserCount . ";";
        echo "\n";

        echo "\n";
        $x = 0;
        foreach($newUsers as $newuser) {
            echo "users[" . $x . "] = \"" . $newuser['user_name'] . "\";";
            echo "\n";
            echo "usersIds[" . $x . "] = " .$newuser['user_id'] . ";";
            echo "\n";
            $x++;
        }


        echo "for(var i = 0; i < pilots; i++)";
        echo "\n";
        echo "{";
        echo "\n";
        echo "result += \"<label>Pilot Name:</label>\";";
        echo "\n";
        echo "result += \"<select name = 'selPilot\" + i + \"'>\"";
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
        echo "result += \"<input type = 'text' name = 'selShares\" + i + \"' />\";";
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

        $sql = "Select
				item_id, item_name
								From
									Items
								order by item_name";

        $result = $mysqli->query($sql);
        while ($row = $result->fetch_assoc()) {
            $newItems[] = $row;
        }
        $result->free();

        $newItemCount = count($newItems['item_name']);

        echo "function newItem()";
        echo "\n";
        echo "{";
        echo "\n";
        echo "var result = \"\";";
        echo "\n";
        echo "var pilots = txtItems.value;";
        echo "\n";
        echo "var items = new Array();";
        echo "\n";
        echo "var itemIds = new Array();";
        echo "\n";
        echo "var count = " . $newItemCount . ";";
        echo "\n";

        echo "\n";
        $x=0;
        foreach($newItems as $newItem) {
            echo "items[" . $x . "] = \"" . $newItem['item_name'] . "\";";
            echo "\n";
            echo "itemIds[" . $x . "] = " . $newItem['item_id'] . ";";
            echo "\n";
            $x++;
        }

        echo "for(var i = 0; i < pilots; i++)";
        echo "\n";
        echo "{";
        echo "\n";
        echo "result += \"<label>Item Name:</label>\";";
        echo "\n";
        echo "result += \"<select name = 'selItem\" + i + \"'>\"";
        echo "\n";
        echo "for(var y = 0; y < items.length; y++)";
        echo "\n";
        echo "{";
        echo "\n";
        echo "result += \"<option value = '\"+itemIds[y]+\"'>\"+items[y]+\"</option>\";";
        echo "\n";
        echo "}";
        echo "\n";
        echo "result += \"</select>\"";
        echo "\n";
        echo "result += \"<label>Amount:</label>\";";
        echo "\n";
        echo "result += \"<input type = 'text' name = 'selAmounts\" + i + \"' />\";";
        echo "\n";
        echo "result += \"<br />\";";
        //divOutput.innerHTML += result;
        echo "\n";
        echo "}";
        echo "\n";
        //result += "<p>"+ count +"</p>";
        echo "divItems.innerHTML = result;";

        echo "\n";
        echo "}";
        echo "\n";
        echo "//]]>";
        echo "\n";
        echo "</script>";

    } else {
        echo "<p> You are not logged in as anyone, thus you cannot see this page </p>";
    }
}


?>
</div>
<!-- InstanceEndEditable -->
<!-- Add Footer -->
<?php include('footer.html'); ?>

</DIV>

</td>
</tr>
</table>
</body>

<!-- InstanceEnd -->

</html>
