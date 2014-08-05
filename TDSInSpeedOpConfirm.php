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
<?php
include 'connection.php';
include 'functions.php';


$sql = "Select
				access_power
			From
				Access
			Where
				access_page = 'add_op'";

$result = $mysqli->query($sql);
$RankResult = mysqli_fetch_assoc($result);
$powerRequired = $RankResult['access_power'];


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
    $fail = false;
    $inData = "";

    if (isset($_POST['opData'])) {
        $inData = $_POST['opData'];
    } else {
        $fail = true;
    }

    if (!$fail) {
        $nameArray[0] = "";
        $numberArray[0] = 0;
        $itemIdArray[0] = 0;
        $eveIdArray[0] = 0;
        $speedCount = 0;

        $dataArray = explode("\r", $inData);
        foreach ($dataArray as $data) {
            //echo $data;
            $innerData = explode("\t", $data);

            $nameArray[$speedCount] = trim($innerData[0]);
            $numberArray[$speedCount] = trim($innerData[1]);

            //echo "Test_";
            //echo $nameArray[$speedCount];
            //echo "_End_";

            $sql = "Select
								typeID
							From
								invTypes
							Where
								typeName = '" . $nameArray[$speedCount] . "'";

            $result = mysql_query($sql, $conn) or die(mysql_error());

            while ($row = mysql_fetch_assoc($result)) {
                foreach ($row as $name => $value) {
                    if ($name == "typeID") {
                        $eveIdArray[$speedCount] = $value;
                        $speedCount++;
                    }
                }
            }
        }

        for ($x = 0; $x < $speedCount; $x++) {
            //echo "Set";
            //echo $eveIdArray[$x];
            $itemIdArray[$x] = 0;
            $sql = "Select
								item_id
							From
								Speed_Items
							Where
								eve_id = " . $eveIdArray[$x];

            $result = mysql_query($sql, $conn) or die(mysql_error());

            $itemIdArray[$x] = -1;

            while ($row = mysql_fetch_assoc($result)) {
                foreach ($row as $name => $value) {
                    if ($name == "item_id") {
                        $itemIdArray[$x] = $value;
                    }
                }
            }
        }

        /*				foreach($itemIdArray as $itemID)
                        {
                            if($itemID == 0)
                                $fail = true;
                        }

                        if(!$fail)
                        {
                            //Pass All Information to Ops page
                        }
                        else
                        {*/
        //Create Interface
        $itemNameArray[0] = "";
        $itemPkArray[0] = 0;
        $itemCount = 0;

        //echo "GO";

        $sql = "Select
								item_id,
								item_name
							From
								Items";

        $result = mysql_query($sql, $conn) or die(mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            foreach ($row as $name => $value) {
                if ($name == "item_id") {
                    $itemPkArray[$itemCount] = $value;
                }
                if ($name == "item_name") {
                    $itemNameArray[$itemCount] = $value;
                    $itemCount++;
                }
            }
        }
        echo "<form action='processSpeedOp.php' method='post' />";
        echo "<fieldset>";
        echo "<legend>Please Identify All Items</legend>";
        for ($x = 0; $x < $speedCount; $x++) {
            echo "<input type='hidden' name='SpeedId" . $x . "' value='" . $eveIdArray[$x] . "' />";
            echo "<input type='text' readonly='readonly' size='50' name='SpeedItem" . $x . "' value='" . $nameArray[$x] . "' />";
            echo "<input type='text' readonly='readonly' name='SpeedAmount" . $x . "' value='" . $numberArray[$x] . "' />";
            echo "<select name='selItem" . $x . "' >";
            echo "<option value='-1'>-= Ignore Item =-</option>";
            for ($y = 0; $y < $itemCount; $y++) {
                if ($itemIdArray[$x] == $itemPkArray[$y]) {
                    echo "<option value='" . $itemPkArray[$y] . "' selected='selected'>" . $itemNameArray[$y] . "</option>";
                } else
                    echo "<option value='" . $itemPkArray[$y] . "'>" . $itemNameArray[$y] . "</option>";
            }
            echo "</select>";
            echo "<br />";
        }
        echo "<input type='submit' value='Confirm Relations' />";
        echo "</fieldset>";
        echo "</form>";
    }

    /*}
    else
    {
        echo "Error: No data found";
    }*/
}

function AccessDenied($error)
{
    //echo "</fieldset>";
    //echo "</form>";

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
<!-- Add Footer -->
<?php include('footer.html'); ?>
</DIV>

</td>
</tr>
</table>
</body>

<!-- InstanceEnd --></html>
