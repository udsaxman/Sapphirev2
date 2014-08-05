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

    <form action="processRanks.php"
          method="post">
        <fieldset>
            <legend>Ranks</legend>

            <?php

            include 'connection.php';
            include 'functions.php';

//            $powerRequired = 100;
//
//            $sql = "Select
//						access_power
//					From
//						Access
//					Where
//						access_page = 'edit_ranks'";
//
//            $result = $mysqli->query($sql);
//
//            $AccessReq = mysqli_fetch_array($result, MYSQL_ASSOC);

            $powerRequired = CheckAccess('edit_ranks');


            if (isset($_SESSION["userName"]) && $_SESSION["userName"] != "") {
                $theName = $_SESSION["userName"];

                if (isset($_SESSION["power"])) {
                    if ($_SESSION["power"] >= $powerRequired) {
                        AccessGranted($theName);
                    } else {
                        AccessDenied();
                    }
                } else {
                    AccessDenied();
                }
            }

            function AccessGranted($adminName)
            {
                global $conn;

                $rankList[0] = "";
                $rankIdArray[0] = 0;
                $rankPowerArray[0] = 0;
                $rankCount = 0;

                $sql = "Select
							rank_id, rank_power, rank_name
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
                        if ($name == "rank_power") {
                            $rankPowerArray[$rankCount] = $value;
                        }
                        if ($name == "rank_name") {
                            $rankList[$rankCount] = $value;
                            $rankCount++;
                        }
                    }
                }

                echo "<input type ='text' readonly='readonly' name ='warning' size='70' value ='Ranks with Rank Power Above 10 Gives the User Admin Rights!!!' />";

                echo "<br />";

                echo "<input type ='text' readonly='readonly' name ='warning' size='70' value ='Ranks with Rank Power below 1 will not show up for Ops and Paychecks!!!' />";

                echo "<br />";
                echo "<br />";

                if (isset($_REQUEST["selRank"])) {
                    $selectedRank = $_REQUEST["selRank"];
                }

                echo "<br />";
                echo "<label>Your Username:</label>";
                echo "<input type ='text' readonly='readonly' name ='adminName' value ='" . $adminName . "' />";
                echo "<br />";
                echo "<br />";

                echo "<table class='Display' border=''>";

                echo "<tr>";
                echo "<th>Rank</th>";
                echo "<th>Current Power</th>";
                echo "<th>New Power</th>";
                echo "</tr>";

                for ($x = 0; $x < $rankCount; $x++) {
                    echo "<tr>";
                    echo "<td>" . $rankList[$x] . "</td>";
                    echo "<td><input type ='text' readonly='readonly' name ='currentPower" . $x . "' value ='" . $rankPowerArray[$x] . "' /></td>";
                    echo "<td><input type ='text' name ='newPower" . $x . "' value ='" . $rankPowerArray[$x] . "' /></td>";
                    echo "</tr>";
                }
                echo "</table>";

                echo "<br />";
                echo "<br />";

                echo "<fieldset>";
                echo "<legend>New Ranks</legend>";
                echo "<div id = 'divOutput'>";
                echo "</div>";
                echo "</fieldset>";

                echo "<input type = 'submit' value = 'Submit Changes'  />";

                echo "<button type = 'button' onclick='newRank()'>";
                echo "Add Ranks";
                echo "</button>";
            }

            function AccessDenied()
            {
                echo "You are not allowed to view this page";
            }

            ?>

        </fieldset>
    </form>

    <script type="text/javascript">
        //<![CDATA[
        //from TDSInItems.php
        var count = 0;

        function newRank() {
            var result = "";
            result += "<label>Rank Name:</label>";
            result += "<input type = 'text' name = 'newRankName" + count + "' />";
            result += "<label>Rank Power:</label>";
            result += "<input type = 'text' name = 'newRankPower" + count + "' />";
            result += "<br />";
            divOutput.innerHTML += result;

            count += 1;
        }
        //]]>
    </script>

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
