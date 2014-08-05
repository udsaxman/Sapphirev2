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
//This makes a user an Admin or not, (Less then this = Admin)

/* HOLY SHIT apparently the line below this FUCKS everything up
//$userName = "";
*/

include 'connection.php';
include 'functions.php';
$powerRequired = 100;
//
//$sql = "Select
//								access_power
//							From
//								Access
//							Where
//								access_page = 'edit_members'";
//
//$result = $mysqli->query($sql);
//
//$RankResult = mysqli_fetch_array($result, MYSQL_ASSOC);
//
//$powerRequired = $RankResult['access_power'];

$powerRequired = CheckAccess('edit_members');

if (isset($_SESSION["userName"]) && $_SESSION["userName"] != "") {
    $theName = $_SESSION["userName"];

    if (isset($_SESSION["power"])) {
        if ($_SESSION["power"] >= $powerRequired) {
            AccessGranted($theName);
        } else {
            AccessDenied();
        }
    } else {
        AccessDenied(); //There should be a switch code for how this page failed
    }
} else {
    AccessDenied();
}

function AccessGranted($adminName)
{
    $userList[0] = "Default";
    $userIdArray[0] = 0;
    $userRank[0] = 0;
    $userCount = 0;
    $selectedUser = 0;

    $rankList[0] = "";
    $rankIdArray[0] = 0;
    $rankCount = 0;

    global $conn;

    $sql = "Select
									user_id, user_name, Users.rank_id
								From
									Users
								Left Join
									Ranks on Ranks.rank_id = Users.rank_id
								Where
									rank_power > -1
								order by
									user_name";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "user_id") {
                $userIdArray[$userCount] = $value;
            }
            if ($name == "user_name") {
                $userList[$userCount] = $value;
            }
            if ($name == "rank_id") {
                $userRank[$userCount] = $value;
                $userCount++;
            }

        }
    }

    $sql = "Select
    								rank_id, rank_name, rank_power
								From
   									Ranks";
    //order by
    //rank_power";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "rank_id") {
                $rankIdArray[$rankCount] = $value;
            }
            if ($name == "rank_name") {
                $rankList[$rankCount] = $value;
                $rankCount++;
            }
        }
    }

    //$userName = "";
    $selUserRank = "";

    echo "<input type ='text' readonly='readonly' name ='warning' size='70' value ='Ranks with Rank Power Above 10 Gives the User Admin Rights!!!' />";

    echo "<br />";
    echo "<br />";

    /*if(isset($_SESSION["userName"]) && $_SESSION["userName"] != $userList[$x])
    {
        $userName = $_SESSION["userName"];
    }*/

    if (isset($_REQUEST["selUser"])) {
        echo "<form action = 'processMembers.php' method = 'post'>";
        echo "<fieldset>";
        echo "<legend>Selected Member</legend>";

        echo "<br />";
        echo "<label>Your Username:</label>";
        echo "<input type ='text' readonly='readonly' name ='adminName' value ='" . $adminName . "' />";
        echo "<br />";

        $selectedUser = $_REQUEST["selUser"];

        for ($i = 0; $i < $userCount; $i++) {
            if ($selectedUser == $userIdArray[$i]) //Convert user_id to array position
            {
                $selectedUser = $userIdArray[$i];
                break;
            }
        }
        echo "<label>Selected User:</label>";
        echo "<input type ='text' readonly='readonly' name ='currentUser' value ='" . $userList[$selectedUser] . "' />";

        for ($i = 0; $i < $rankCount; $i++) {
            if ($rankIdArray[$i] == $userRank[$selectedUser]) {
                $selUserRank = $rankList[$i];
                break;
            }
        }

        echo "<br />";
        echo "<label>Selected User Rank:</label>";
        echo "<input type ='text' readonly='readonly' name ='currentRank' value ='" . $selUserRank . "' />";

        echo "<br />";
        echo "<label>Select New Rank for Selected User:</label>";

        //Ranks
        echo "<select name = 'selRank'>";
        for ($x = 0; $x < $rankCount; $x++) {
            echo "<option value = " . $x;
            if ($rankIdArray[$x] == $userRank[$selectedUser]) {
                echo " selected = 'selected'";
            }
            echo ">" . $rankList[$x];
            echo "</option>";

        }
        echo "</select>";

        echo "<br />";

        echo "<input type = 'submit' value = 'Change Member Rank to Selected Rank'  />";
        echo "</fieldset>";
        echo "</form>";

        echo "<br />";
    }

    //Users
    echo "<form action = 'TDSInEditMembers.php' method = ''>";
    echo "<fieldset>";
    echo "<select name = 'selUser'>";
    for ($x = 0; $x < $userCount; $x++) {
        echo "<option value = " . $x;
        if ($x == $selectedUser) {
            echo " selected = 'selected'";
        }
        echo ">" . $userList[$x];
        echo "</option>";
    }
    echo "</select>";

    echo "<input type = 'submit' value = 'Select User'  />";
    echo "</fieldset>";
    echo "</form>";

    $selectedRank = 0;


    echo "<br />";


}

function AccessDenied()
{
    echo "You are not allowed to view this page";
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
