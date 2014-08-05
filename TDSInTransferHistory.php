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

$ZehName = "";
$Limit = 50;
$isLimit = true;
$userID = 0;

if (isset($_REQUEST["Limit"])) {
    $Limit = $_REQUEST["Limit"];
    if (is_numeric($Limit)) {
        if ($Limit <= 0) {
            $isLimit = false;
        }
    } else {
        $Limit = 50;
    }
}

if (isset($_REQUEST["TargetUser"])) {
    $zehName = $_REQUEST["TargetUser"];

    $sql = "SELECT
								user_id
							FROM 
								Users 
							WHERE 
								user_name = '" . strtolower($zehName) . "'";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "user_id") {
                $userID = $value;
            }
        }
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
									else
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
							order by transaction_id desc";
    if ($isLimit) {
        $sql = $sql . " LIMIT 0," . $Limit;
    }

    $result = mysql_query($sql, $conn) or die(mysql_error());

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

    echo "<form action = '' method = ''>";
    echo "<fieldset>";
    if ($isLimit)
        echo "<legend>Top " . $Limit . " Transaction History - " . $zehName . "</legend>";
    else
        echo "<legend>Top Infinate Transaction History - " . $zehName . "</legend>";

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
        if (strtolower($sourceNameArray[$i]) == strtolower($zehName) && strtolower($sourceTypeArray[$i]) == "user") {
            if ($typeDescriptionArray[$i] != NULL) {
                echo "<td title='" . $typeDescriptionArray[$i] . "'>" . $targetTypeArray[$i] . "</td>";
            } else {
                echo "<td>" . $targetTypeArray[$i] . "</td>";
            }
            $blnTarget = true;
        } elseif (strtolower($targetNameArray[$i]) == strtolower($zehName) && strtolower($targetTypeArray[$i]) == "user") {
            if ($typeDescriptionArray[$i] != NULL) {
                echo "<td title='" . $typeDescriptionArray[$i] . "'>" . $sourceTypeArray[$i] . "</td>";
            } else {
                echo "<td>" . $sourceTypeArray[$i] . "</td>";
            }
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
    $sql = " Select
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
										else
											 (select description from Transaction_Types where type_id = target_type_id)
									End
								AS target_name,
							(select description from Transaction_Types where type_id = target_type_id)
								AS target_type,
								description
							 FROM 
								 Transactions t
							order by transaction_id desc";
    if ($isLimit) {
        $sql = $sql . " LIMIT 0," . $Limit;
    }

    $result = mysql_query($sql, $conn) or die(mysql_error());

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

    $typeDescriptionArray[0] = "";

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
            if ($name == "description") {
                $typeDescriptionArray[$tranCount] = $value;
                $tranCount++;
            }
        }
    }

    echo "<form action = '' method = ''>";
    echo "<fieldset>";
    if ($isLimit)
        echo "<legend>Top " . $Limit . " Transfers by Execution</legend>";
    else
        echo "<legend>Top 8 Transfers by Execution</legend>";

    echo "<table class='Display' border=''>";

    echo "<tr>";
    echo "<th>Date</th>";
    echo "<th>Item</th>";
    echo "<th>Amount</th>";
    echo "<th>Source Type</th>";
    echo "<th>Source Name</th>";
    echo "<th>Target Type</th>";
    echo "<th>Target Name</th>";
    echo "</tr>";

    for ($i = 0; $i < $tranCount; $i++) {
        echo "<tr>";
        echo "<td>" . $dateArray[$i] . "</td>";
        echo "<td>" . $amountTypeArray[$i] . "</td>";
        if ($typeDescriptionArray[$i] != NULL) {
            echo "<td title='" . $typeDescriptionArray[$i] . "'>" . number_format($amountArray[$i], 2, '.', ',') . "</td>";
            echo "<td title='" . $typeDescriptionArray[$i] . "'>" . $sourceTypeArray[$i] . "</td>";
        } else {
            echo "<td>" . number_format($amountArray[$i], 2, '.', ',') . "</td>";
            echo "<td>" . $sourceTypeArray[$i] . "</td>";
        }
        if ($sourceTypeArray[$i] == "op" || $sourceTypeArray[$i] == "edit op") {
            echo "<td><a href = './TDSInViewOp.php?selOp=" . $sourceTypeIDArray[$i] . "'>" . $sourceNameArray[$i] . "</a></td>";
        } else {
            echo "<td>" . $sourceNameArray[$i] . "</td>";
        }
        echo "<td>" . $targetTypeArray[$i] . "</td>";
        echo "<td>" . $targetNameArray[$i] . "</td>";
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
