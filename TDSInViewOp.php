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

<form action="TDSInViewOp.php"
      method="">
<fieldset>
<legend>Zeh Op</legend>

<?php

//Login Check
if (isset($_SESSION["userName"])) {

    echo "<a href = './TDSinLiveOp.php'><input type = 'button' value = 'Live Ops'  /></a>";

    echo "<a href = './TDSInViewItems.php'><input type = 'button' value = 'View Items'  /></a>";

    echo "<br />";

    $opArray[0] = "";
    $opIDArray[0] = 0;
    $opCount = 0;

    $selectedOp = 0;

    include 'connection.php';

    //$sql = select op_name from Ops;
    $sql = "Select op_id, op_name From Ops order by op_id desc LIMIT 0,20";
    $result = $mysqli->query($sql);

    while ($row = $result->fetch_assoc()) {
        $OPArray[] = $row;
    }
    $result->free();

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

    echo "<input type = 'submit' value = 'View Op'  />";

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
    							user_name, shares
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

        //Get all of the Op_Loot

        $sql = "Select
    							item_name, item_type, amount
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

        $result = $mysqli->query($sql);

        while ($row = $result->fetch_array(MYSQLI_BOTH)) {
            $items[] = $row;
        }

        $sql = "select * from Item_Category order by category_order";

        $result = $mysqli->query($sql);

        while ($row = $result->fetch_array(MYSQLI_BOTH)) {
            $categories[] = $row;
        }

        //Build basic Op information from Ops Table
        echo "<br />";
        echo "<br />";
        echo "Op Information";
        echo "<br />";
        echo "<table class='Display' border=''>";

        echo "<tr>";
        echo "<td>Op Name</td>";
        echo "<td>" . $opName . "</tr>";
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
        echo "<td style='text-align:right'>" . number_format($opTax, 3, '.', ',') . "</tr>";
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

        foreach ($Users as $user){
            echo "<tr>";
            echo "<td>" . $user['user_name'] . "</td>";
            echo "<td>" . $user['shares'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
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
                        echo "<td style='text-align:right'>" . number_format($item['amount'], 0, '.', ',') . "</td>";
                        echo "</tr>";
                    }
                }

                echo "</table>";
                echo "</fieldset>";
            }

        }

        echo "</fieldset>";
    }


} else {
    echo "<p> You are not logged in as anyone, thus you cannot see this page </p>";
}

?>
</fieldset>
</form>

</div>
<!-- InstanceEndEditable -->
<!-- Add Footer -->
<?php include('footer.html'); ?>
</DIV>

</td>
</tr>
</table>
</body>

<!-- InstanceEnd --></html>
