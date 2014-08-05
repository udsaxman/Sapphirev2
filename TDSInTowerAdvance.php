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
        AccessGranted(true);
    } elseif ($_SESSION["power"] < $powerRequired) {
        AccessGranted(false);
    }
} else {
    AccessDenied(0);
}

function AccessGranted($isAdmin)
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
    $targetStarbase = -1;
    $targetUser = -1;

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "key_id")
                $keyIdYo = $value;
        }
    }

    if ($keyIdYo == -1)
        $fail = true;

    if (isset($_REQUEST["ViewStarbase"])) {
        $targetStarbase = $_REQUEST["ViewStarbase"];
    } else if (isset($_REQUEST["TargetUser"])) {
        $targetUser = $_REQUEST["TargetUser"];
    } else {
        $fail = true;
    }

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
							mapDenormalize d on d.itemID = s.moonID";
        if ($targetUser != -1) {
            $sql = $sql . " left join
								Starbase_Users su on su.starbase_id = s.starbase_id";
        }
        $sql = $sql . " Where
							key_id = " . $keyIdYo;
        if ($targetStarbase != -1) {
            $sql = $sql . " And s.Starbase_id = " . $targetStarbase;
        }
        if ($targetUser != -1) {
            $sql = $sql . " And su.user_id = " . $targetUser;
        }

        $sql = $sql . " order by d.itemName";

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

        $posMembers[0] = array();
        $posMemberIds[0] = array();
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


            $tempCount = 0;

            $sql = " Select
								u.user_name,
								u.user_id
							From
								Starbase_Users su
							Left Join
								Users u on u.user_id = su.user_id
							Where
								su.isRemoved = 0
								And
								su.starbase_id = " . $starbaseIDs[$i] . "
							order by user_name";

            $result = mysql_query($sql, $conn) or die(mysql_error());

            while ($row = mysql_fetch_assoc($result)) {
                foreach ($row as $name => $value) {
                    if ($name == "user_name") {
                        $posMembers[$i][$tempCount] = $value;
                    }
                    if ($name == "user_id") {
                        $posMemberIds[$i][$tempCount] = $value;
                        $tempCount++;
                    }
                }
            }
        }

        $userCount = 0;
        $userList[0] = "None";
        $userIdList[0] = 0;

        $sql = "Select
							user_id, user_name
						From
							Users
						left join
							Ranks on Users.rank_id = Ranks.rank_id
						Where
							rank_power > 0
						order by user_name";

        $result = mysql_query($sql, $conn) or die(mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            foreach ($row as $name => $value) {
                if ($name == "user_id") {
                    $userIdList[$userCount] = $value;
                }
                if ($name == "user_name") {
                    $userList[$userCount] = $value;
                    $userCount++;
                }
            }
        }

        //echo "<a href = './processGetTowers.php'><input type = 'button' value = 'Refresh Towers' /></a>";
        //echo "<a href = './processTowerDetails.php'><input type = 'button' value = 'Refresh All Tower Details' /></a>";

        //echo "<br />";
        //echo "<br />";

        echo "<form action='processTowerMembers.php' method='post'/>";
        echo "<fieldset>";
        echo "<legend>Towers</legend>";

        for ($i = 0; $i < $starbaseCount; $i++) {
            echo "<fieldset>";
            echo "<legend><span class='DisplayBlue'>" . $moonIDs[$i] . "</span> -> <span class='DisplayYellow'>" . $typeIDs[$i] . "</span> ---> " . $states[$i] . "</legend>";
            echo "<table class='Tower' border='' align='center' >";
            echo "<tr>";
            echo "<th>Pilot</th>";
            if ($isAdmin) {
                echo "<th>Remove</th>";
            }
            echo "</tr>";
            for ($x = 0; $x < $memberCount[$i]; $x++) {
                echo "<tr>";
                echo "<td>" . $posMembers[$i][$x] . "</td>";
                if ($isAdmin) {
                    echo "<td><input type='checkbox' name='remove" . $posMemberIds[$i][$x] . "' value = 'removeMe' /></td>";
                }
                echo "</tr>";
            }
            echo "<tr>";
            echo "</table>";
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
            //echo "<br />";
            //echo "<a href = './TDSInTowerAdvance.php?ViewStarbase=".$starbaseIDs[$i]."'><input type = 'button' value = 'Tower Advance View' /></a>";
            //echo "<a href = './processTowerDetails.php?UpdateStarbase=".$starbaseIDs[$i]."'><input type = 'button' value = 'Refresh Tower Details' /></a>";
            if ($isAdmin) {
                echo "<br />";
                echo "<br />";
                echo "<input type='hidden' name='targetTower' value='" . $starbaseIDs[$i] . "' />";
                echo "<br />";
                echo "<br />";

                echo "<label>Landlord:</label>";
                echo "<select name = 'selLandlord'>";
                echo "<option value='-1'>-= Select A Landlord =-</option>";
                for ($x = 0; $x < $memberCount[$i]; $x++) {
                    echo "<option value = '" . $posMemberIds[$i][$x] . "'>" . $posMembers[$i][$x] . "</option>";
                }
                echo "</select>";
                echo "<br />";
                echo "<button type = 'button' onclick='newPilot()'>";
                echo "Add Residents";
                echo "</button>";
                echo "<input type = 'text' id = 'txtPilots' value = '0' />";
                echo "<div id = 'divOutput'>";
                echo "</div>";

                echo "<input type='submit' value='Submit Changes' />";
            }
            echo "</fieldset>";
        }

        echo "</fieldset>";
        echo "</form>";

        if ($isAdmin) {
            echo "<script type = 'text/javascript'>";
            echo "\n";
            echo "//<![CDATA[";
            echo "\n";
            echo "//from TDSInTowerAdvance.php";
            echo "\n";

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
            echo "var count = " . ($userCount + 1) . ";";
            echo "\n";

            echo "\n";
            echo "users[0] = \"-= Select Pilot =-\";";
            echo "\n";
            echo "usersIds[0] = 0;";
            echo "\n";
            for ($x = 1; $x < $userCount; $x++) {
                echo "users[" . $x . "] = \"" . $userList[$x] . "\";";
                echo "\n";
                echo "usersIds[" . $x . "] = " . $userIdList[$x] . ";";
                echo "\n";
            }
            echo "result += \"<input type = 'hidden' name = 'AddedPilotCountYo' value = '\"+pilots+\"' />\";";

            echo "for(var i = 0; i < pilots; i++)";
            echo "\n";
            echo "{";
            echo "\n";
            echo "result += \"<label>Pilot Name:</label>\";";
            echo "\n";
            echo "result += \"<select name = 'selType\" + i + \"'>\"";
            echo "\n";
            echo "for(var y = 0; y < users.length; y++)";
            echo "\n";
            echo "{";
            echo "\n";
            echo "result += \"<option value = '\"+usersIds[y]+\"'>\"+users[y]+\"</option>\";";
            echo "\n";
            echo "}";
            echo "\n";
            echo "result += \"</select>\";";
            echo "\n";
            /*echo"result += \"<label>Shares:</label>\";";
            echo "\n";
            echo"result += \"<input type = 'text' name = 'Shares\" + i + \"' />\";";
            echo "\n";*/
            echo "result += \"<br />\";";
            //divOutput.innerHTML += result;
            echo "\n";
            echo "}";
            echo "\n";
            //result += "<p>"+ count +"</p>";
            echo "divOutput.innerHTML = result;";

            echo "\n";
            echo "}";
            echo "\n";
            echo "//]]>";
            echo "\n";
            echo "</script>";
        }
    } else {
        Fail("There is required information missing");
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
    header('Location: ./TDSInHome.php');
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
