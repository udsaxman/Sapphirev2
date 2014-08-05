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

if (isset($_SESSION["userName"])) {
    $theName = $_SESSION["userName"];

    include 'connection.php';

    $userID = 0;
    $isk = 0;
    $rank = "";
    $lockedBro = false;

    $sql = "SELECT
							user_id, user_isk, rank_name, isLocked
						FROM 
							Users 
						Left join
							Ranks on Ranks.rank_id = Users.rank_id
						WHERE 
							user_name = '" . strtolower($theName) . "'";

    $result = $mysqli->query($sql);

    $userData = mysqli_fetch_array($result, MYSQL_ASSOC);

    $userID = $userData['user_id'];

    $isk = $userData['user_isk'];

    $rank = $userData['rank_name'];

    if ($userData['isLocked'] == 1) {
        $lockedBro = true;

        //print "$name: $value <br />\n";
    }


    $sql = "Select
							transaction_id,
							date,
							amount,
						(select description from Transaction_Types where type_id = amount_type_id)
							AS amount_type,
						case 
							when
								source_type_id = 1
								THEN
									(select user_name from Users where user_id = t.source_id)
							when
								source_type_id = 2
								THEN
									(select op_name from Ops where op_id = t.source_id)
              				when
								source_type_id = 4
								THEN
									(select user_name from Users where user_id = t.source_id)
							when
								source_type_id = 5
								THEN
									(select user_name from Users where user_id = t.source_id)
							when
								source_type_id = 6
								THEN
									(select op_name from Ops where op_id = t.source_id)
								ELSE
									(select description from Transaction_Types where type_id = source_type_id)
								END
							AS source_name,
						(select description from Transaction_Types where type_id = source_type_id)
							AS source_type,
						source_id,
						case
							when 
								target_type_id = 1
								Then
									(select user_name from Users where user_id = t.target_id)
							when
								target_type_id = 2
								Then
									(select op_name from Ops where op_id = t.target_id)
							when
								target_type_id = 4
								Then
									(select user_name from Users where user_id = t.target_id)
								ELSE
									 (select description from Transaction_Types where type_id = target_type_id)
								End
							AS target_name,
						(select description from Transaction_Types where type_id = target_type_id)
							AS target_type,
						target_id,
						description
						FROM 
							Transactions t
						WHERE
							(t.target_id = " . $userID . " and (t.target_type_id = 1 or t.target_type_id = 4 or t.target_type_id = 5))
						order by transaction_id desc
						LIMIT 0,20";


    $result = mysql_query($sql, $conn) or die(mysql_error());

    //Probably should mention that if the table structure were to ever change, all the things may break

    //Transaction Variables
    $tranCount = 0;
    $dateArray[0] = "";

    $amountArray[0] = 0;
    $amountTypeArray[0] = "";

    $sourceNameArray[0] = "";
    $sourceTypeArray[0] = "";
    $sourceTypeIDArray[0] = 0;

    $targetNameArray[0] = "";
    $targetTypeArray[0] = "";
    $targetTypeIDArray[0] = 0;

    $typeDescriptionArray[0] = "";

    $blnTarget = false;
    $blnError = false;

    //Grab all of the Incoming Transactions
    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "date") {
                $dateArray[$tranCount] = $value;
            }
            if ($name == "amount") {
                $amountArray[$tranCount] = $value;
            }
            if ($name == "amount_type") {
                $amountTypeArray[$tranCount] = $value;
            }
            if ($name == "source_name") {
                $sourceNameArray[$tranCount] = $value;
            }
            if ($name == "source_type") {
                $sourceTypeArray[$tranCount] = $value;
            }
            if ($name == "source_id") {
                $sourceTypeIDArray[$tranCount] = $value;
            }
            if ($name == "target_name") {
                $targetNameArray[$tranCount] = $value;
            }
            if ($name == "target_type") {
                $targetTypeArray[$tranCount] = $value;
            }
            if ($name == "target_id") {
                $targetTypeIDArray[$tranCount] = $value;
            }
            if ($name == "description") {
                $typeDescriptionArray[$tranCount] = $value;
                $tranCount++;
            }
        }
    }

    $towerFail = false;

    $sql = "SELECT
							key_id
						FROM
							EveAPIKeys
						Where
							inUse = 1";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    $fail = false;
    $keyIdYo = -1;
    $starbaseIDs[0] = 0;
    $starbaseMoons[0] = "";
    $starbaseTypes[0] = "";
    $starbaseStates[0] = "";
    $starbaseCount = 0;

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "key_id")
                $keyIdYo = $value;
        }
    }

    $sql = "Select
							s.starbase_id As 'starbase_id',
							t.typeName As 'typeName',
							d.itemName As 'moonName',
							ss.name As 'state'
						From 
							Starbase_Key k
						Left Join Starbases s on s.starbase_id = k.starbase_id 
						Left Join Starbase_States ss on ss.state = s.state 
						Left Join invTypes t on t.typeID = s.typeID 
						left join mapDenormalize d on d.itemID = s.moonID
						left join Starbase_Users su on su.starbase_id = s.starbase_id
						Where 
							key_id = 1
							AND
							su.user_id = " . $userID . "
						order by d.itemName";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "starbase_id") {
                $starbaseIDs[$starbaseCount] = $value;
            }
            if ($name == "typeName") {
                $starbaseTypes[$starbaseCount] = $value;
            }
            if ($name == "moonName") {
                $starbaseMoons[$starbaseCount] = $value;
            }
            if ($name == "state") {
                $starbaseStates[$starbaseCount] = $value;
                $starbaseCount++;
            }
        }
    }

    echo "<p> Welcome $theName <p>";
    echo "<br />";
    echo "<p> Isk = " . number_format($isk, 2, '.', ',') . " <p>";
    //echo "<br />";
    echo "<p> Rank = $rank <p>";

    echo "<br /><br />";

    if (isset($_REQUEST["Message"])) {
        echo "<p> !!! " . $_REQUEST["Message"] . " !!! <p>";
        echo "<br /><br />";
    }

    if ($lockedBro) {
        echo "<br />";
        "<p> !!! You must change youre password, failure to do so will result in a lockout !!! </p>";
        echo "<br />";
    }

    echo "<a href = './TDSInPlayerSettings.php'><input type = 'button' value = 'Account Settings'  /></a>";
    echo "<a href = './TDSInTransferHistory.php?TargetUser=" . $theName . "&Limit=-1'><input type = 'button' value = 'View Full Transaction History'  /></a>";

    if ($starbaseCount > 0) {
        echo "<a href = './TDSInTowerAdvance.php?TargetUser=" . $userID . "'><input type = 'button' value = 'View all My Towers'  /></a>";
    }

    echo "<br /><br />";

    if ($keyIdYo != -1) {
        for ($x = 0; $x < $starbaseCount; $x++) {
            echo "<a class='DisplayWhite' href='./TDSInTowerAdvance.php?ViewStarbase=" . $starbaseIDs[$x] . "'>";
            echo "<span class='DisplayBlue'>" . $starbaseMoons[$x] . "</span>";
            echo "->";
            echo "<span class='DisplayYellow'>" . $starbaseTypes[$x] . "</span>";
            echo "-->";
            echo $starbaseStates[$x];
            echo "</a>";
            echo "<br />";
        }

        echo "<br /><br />";
    }

    echo "Top 20 Recent Transactions";

    echo "<br />";

    echo "<form action = '' method = ''>";
    echo "<fieldset>";
    echo "<legend>Transaction History</legend>";

    echo "<table class='Display' border='' align='center'>";

    echo "<tr>";
    echo "<th>Date</th>";
    echo "<th>Item</th>";
    echo "<th>Amount</th>";
    echo "<th>Type</th>";
    echo "<th>Name</th>";
    echo "</tr>";

    for ($i = 0; $i < $tranCount; $i++) {
        echo "<tr>";
        echo "<td>" . $dateArray[$i] . "</td>";
        echo "<td>" . $amountTypeArray[$i] . "</td>";
        if ($typeDescriptionArray[$i] != NULL) {
            if ($amountArray[$i] < 0)
                echo "<td title='" . $typeDescriptionArray[$i] . "' style='text-align:right'><span>" . number_format($amountArray[$i], 2, '.', ',') . "</span></td>";
            else
                echo "<td title='" . $typeDescriptionArray[$i] . "' style='text-align:right'>" . number_format($amountArray[$i], 2, '.', ',') . "</td>";
        } else {
            if ($amountArray[$i] < 0)
                echo "<td style='text-align:right'><span>" . number_format($amountArray[$i], 2, '.', ',') . "</span></td>";
            else
                echo "<td style='text-align:right'>" . number_format($amountArray[$i], 2, '.', ',') . "</td>";
        }
        if (strtolower($sourceNameArray[$i]) == strtolower($theName) && strtolower($sourceTypeArray[$i]) == "user") {
            if ($typeDescriptionArray[$i] != NULL)
                echo "<td title='" . $typeDescriptionArray[$i] . "'>" . $targetTypeArray[$i] . "</td>";
            else
                echo "<td>" . $targetTypeArray[$i] . "</td>";
            $blnTarget = true;
        } elseif (strtolower($targetNameArray[$i]) == strtolower($theName) && strtolower($targetTypeArray[$i]) == "user") {
            if ($typeDescriptionArray[$i] != NULL)
                echo "<td title='" . $typeDescriptionArray[$i] . "'>" . $sourceTypeArray[$i] . "</td>";
            else
                echo "<td>" . $sourceTypeArray[$i] . "</td>";
        } else {
            echo "<td>UNKNOWN TYPE</td>";
            $blnError = true;
        }
        if ($blnTarget && !$blnError) {
            if (strtolower($targetTypeArray[$i]) == "op" || strtolower($targetTypeArray[$i]) == "edit op") {
                echo "<td><a href = './TDSInViewOp.php?selOp=" . $targetTypeIDArray[$i] . "'>" . $targetNameArray[$i] . "</a></td>";
            } else {
                echo "<td>" . $targetNameArray[$i] . "</td>";
            }
        } elseif (!$blnTarget && !$blnError) {
            if (strtolower($sourceTypeArray[$i]) == "op" || strtolower($sourceTypeArray[$i]) == "edit op") {
                echo "<td><a href = './TDSInViewOp.php?selOp=" . $sourceTypeIDArray[$i] . "'>" . $sourceNameArray[$i] . "</a></td>";
            } else {
                echo "<td>" . $sourceNameArray[$i] . "</td>";
            }
        } else {
            echo "<td>UNKNOWN NAME</td>";
        }
        echo "</tr>";
    }

    if ($tranCount == 0) {
        echo "<tr>";
        echo "<td>*</td>";
        echo "<td>*</td>";
        echo "<td>*</td>";
        echo "<td>*</td>";
        echo "<td>*</td>";
        echo "</tr>";
    }

    echo "</table>";

    echo "</fieldset>";
    echo "</form>";


} else {
    echo "<p> You are not logged in as anyone, thus you cannot see this page </p>";
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
