<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!-- InstanceBegin template="/Templates/SapphireTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
    <!-- InstanceBeginEditable name="doctitle" -->
    <title>TDSIN: Sapphire</title>
    <!-- InstanceEndEditable -->
    <link href="SapphireCSS.css" rel="stylesheet" type="text/css"/>
</head>

<body class="SapphireCSS">
<table width="1024" align="center">
<tr>
<td>

<div align="center">
<DIV ID="sapphire-top">
    <IMG SRC="./images/sapphire_top.png" WIDTH=1024 HEIGHT=182 ALT="">
</DIV>
<DIV ID="sapphire" align="">
    <IMG SRC="./images/sapphire.png" WIDTH=310 HEIGHT=39 ALT="">
</DIV>
<DIV ID="login">
    <?php
    if (isset($_SESSION["userName"])) {
        echo "<A HREF='./TDSInLogin.php'><IMG SRC='./images/logout.png' WIDTH=63 HEIGHT=39 ALT=''></A>";
    } else {
        echo "<A HREF='./TDSInLogin.php'><IMG SRC='./images/login.png' WIDTH=63 HEIGHT=39 ALT=''></A>";
    }
    ?>
</DIV>
<DIV ID="home">
    <A HREF="./TDSInHome.php"><IMG SRC="./images/home.png" WIDTH=67 HEIGHT=39 ALT=""></A>
</DIV>
<DIV ID="mypage">
    <A HREF="./TDSInPlayerPage.php"><IMG SRC="./images/mypage.png" WIDTH=99 HEIGHT=39 ALT=""></A>
</DIV>
<DIV ID="viewops">
    <A HREF="./TDSInViewOp.php"><IMG SRC="./images/viewops.png" WIDTH=107 HEIGHT=39 ALT=""></A>
</DIV>
<DIV ID="admin">
    <A HREF="./TDSInAdminTools.php"><IMG SRC="./images/admin.png" WIDTH=82 HEIGHT=39 ALT=""></A>
</DIV>
<DIV ID="right">
    <IMG SRC="./images/right.png" WIDTH=296 HEIGHT=39 ALT="">
</DIV>
<!-- InstanceBeginEditable name="content" -->
<div id="content_area" align="left">

<br/>
<br/>
<br/>
<br/>
<?php
include 'connection.php';

$sql = "Select
					access_power
				From
					Access
				Where
					access_page = 'all_towers'";

$result = mysql_query($sql, $conn) or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) {
    foreach ($row as $name => $value) {
        if ($name == "access_power") {
            $powerRequired = $value;
        }
    }
}

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

    $sql = "SELECT
						key_id
					FROM
						EveAPIKeys
					Where
						inUse = 1";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    $fail = false;
    $keyIdYo = -1;

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "key_id")
                $keyIdYo = $value;
        }
    }

    if ($keyIdYo == -1)
        $fail = true;

    if (!$fail) {
        $sql = "Select
							s.starbase_id As 'starbase_id',
							s.itemID As 'itemID',
							t.typeName As 'typeID',
							s.locationID As 'locationID',
							d.itemName As 'moonID',
							ss.name As 'state',
							s.stateTimestamp As 'stateTimestamp',
							s.onlineTimestamp As 'onlineTimestamp',
							s.lastUpdate As 'lastUpdate'
						From
							Starbase_Key k
						Left Join
							Starbases s on s.starbase_id = k.starbase_id
						Left Join
							Starbase_States ss on ss.state = s.state
						Left Join
							invTypes t on t.typeID = s.typeID
						left join
							mapDenormalize d on d.itemID = s.moonID
						Where
							key_id = " . $keyIdYo . "
						order by d.itemName";

        $result = mysql_query($sql, $conn) or die(mysql_error());

        $starbaseIDs[0] = 0;
        $itemIDs[0] = 0;
        $typeIDs[0] = 0;
        $locationIDs[0] = 0;
        $moonIDs[0] = "";
        $states[0] = "";
        $stateTimes[0] = "";
        $onlineTimes[0] = "";
        $lastUpdates[0] = "";
        $starbaseCount = 0;

        while ($row = mysql_fetch_assoc($result)) {
            foreach ($row as $name => $value) {
                if ($name == "starbase_id") {
                    $starbaseIDs[$starbaseCount] = $value;
                }
                if ($name == "itemID") {
                    $itemIDs[$starbaseCount] = $value;
                }
                if ($name == "typeID") {
                    $typeIDs[$starbaseCount] = $value;
                }
                if ($name == "locationID") {
                    $locationIDs[$starbaseCount] = $value;
                }
                if ($name == "moonID") {
                    $moonIDs[$starbaseCount] = $value;
                }
                if ($name == "state") {
                    $states[$starbaseCount] = $value;
                }
                if ($name == "stateTimestamp") {
                    $stateTimes[$starbaseCount] = $value;
                }
                if ($name == "onlineTimestamp") {
                    $onlineTimes[$starbaseCount] = $value;
                }
                if ($name == "lastUpdate") {
                    $lastUpdates[$starbaseCount] = $value;
                    $starbaseCount++;
                }
            }
        }

        $starbaseFuelType[0] = array();
        $starbaseFuel[0] = array();
        $starbaseUpdate[0] = array();

        $memberCount[0] = 0;
        $landlord[0] = "";

        for ($i = 0; $i < $starbaseCount; $i++) {
            $fuelCount = 0;
            $sql = "Select
								s.lastUpdate,
								t.typeName as 'fuel_type',
								d.amount
							From
								Starbase_Details s
							Left Join
								Details d on d.detail_id = s.detail_id
							Left Join
								invTypes t on t.typeID = d.fuel_typeID
							Where
								s.starbase_id =" . $starbaseIDs[$i];

            $result = mysql_query($sql, $conn) or die(mysql_error());

            while ($row = mysql_fetch_assoc($result)) {
                foreach ($row as $name => $value) {
                    if ($name == "lastUpdate") {
                        $starbaseUpdate[$i][$fuelCount] = $value;
                    }
                    if ($name == "fuel_type") {
                        $starbaseFuelType[$i][$fuelCount] = $value;
                    }
                    if ($name == "amount") {
                        $starbaseFuel[$i][$fuelCount] = $value;
                        $fuelCount++;
                    }
                }
            }

            $landlordName = "-= No Landlord =-";

            $sql = "Select
								Count(user_id) as 'Residents'
							From
								Starbase_Users
							Where
								isRemoved = 0
								And
								Starbase_id = " . $starbaseIDs[$i];

            $result = mysql_query($sql, $conn) or die(mysql_error());

            while ($row = mysql_fetch_assoc($result)) {
                foreach ($row as $name => $value) {
                    if ($name == "Residents") {
                        $memberCount[$i] = $value;
                    }
                }
            }

            $sql = "Select
								u.user_name
							From
								Starbase_Users su
							Left Join
								Users u on u.user_id = su.user_id
							Where
								su.isRemoved = 0
								And
								su.isLandlord = 1
								And
								su.Starbase_id = " . $starbaseIDs[$i] . "
							Limit 1";

            $landlord[$i] = $landlordName;

            $result = mysql_query($sql, $conn) or die(mysql_error());

            while ($row = mysql_fetch_assoc($result)) {
                foreach ($row as $name => $value) {
                    if ($name == "user_name") {
                        $landlord[$i] = $value;
                    }
                }
            }
        }

        echo "<a href = './processGetTowers.php'><input type = 'button' value = 'Refresh Towers' /></a>";
        echo "<a href = './processTowerDetails.php'><input type = 'button' value = 'Refresh All Tower Details' /></a>";

        echo "<br />";
        echo "<br />";

        echo "<form action='' method='post'/>";
        echo "<fieldset>";
        echo "<legend>Towers</legend>";

        for ($i = 0; $i < $starbaseCount; $i++) {
            echo "<fieldset>";
            echo "<legend><span class='DisplayBlue'>" . $moonIDs[$i] . "</span> -> <span class='DisplayYellow'>" . $typeIDs[$i] . "</span> ---> " . $states[$i] . "</legend>";
            /*echo "<table class='Tower' border='' align='center' >";
                echo "<tr>";
                    echo "<th>Type</th>";
                    echo "<th>Info</th>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>itemID</td>";
                    echo "<td>".$itemIDs[$i]."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>typeID</td>";
                    echo "<td>".$typeIDs[$i]."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>locationID</td>";
                    echo "<td>".$locationIDs[$i]."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>moonID</td>";
                    echo "<td>".$moonIDs[$i]."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>state</td>";
                    echo "<td>".$states[$i]."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>stateTime</td>";
                    echo "<td>".$stateTimes[$i]."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>onlineTime</td>";
                    echo "<td>".$onlineTimes[$i]."</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>lastUpdate</td>";
                    echo "<td>".$lastUpdates[$i]."</td>";
                echo "</tr>";
            echo "</table>";*/
            echo "<table class='Tower' border='' align='center' >";
            echo "<tr>";
            echo "<th>Type</th>";
            echo "<th>Amount</th>";
            echo "<th>lastUpdate</th>";
            echo "</tr>";

            for ($x = 0; $x < $fuelCount; $x++) {
                echo "<tr>";
                echo "<td>" . $starbaseFuelType[$i][$x] . "</td>";
                echo "<td>" . $starbaseFuel[$i][$x] . "</td>";
                echo "<td>" . $starbaseUpdate[$i][$x] . "</td>";
                echo "</tr>";
            }

            echo "</table>";

            if ($states[$i] == "Reinforced") {
                echo "<span class = 'Display'>This tower is currently Reinforced. It will exit at: " . $stateTimes[$i];
            }

            echo "Current Number of Residents: " . $memberCount[$i];
            echo "<br />";
            echo "Current Landlord: " . $landlord[$i];
            echo "<br />";
            echo "<a href = './TDSInTowerAdvance.php?ViewStarbase=" . $starbaseIDs[$i] . "'><input type = 'button' value = 'Tower Advance View' /></a>";
            echo "<a href = './processTowerDetails.php?UpdateStarbase=" . $starbaseIDs[$i] . "'><input type = 'button' value = 'Refresh Tower Details' /></a>";
            echo "</fieldset>";
        }

        echo "</fieldset>";
        echo "</form>";
    } else {
        Fail("No Active API Keys Found");
    }
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

function Fail($error)
{
    header('Location: ./TDSInError.php?Error=' . $error . '');
}

function Success()
{
    header('Location: ./TDSInAdminTools.php');
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
