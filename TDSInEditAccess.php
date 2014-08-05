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

                    <form action="processAccess.php"
                          method="post">
                        <fieldset>
                            <legend>Access</legend>

                            <?php



                            include 'connection.php';

                            $powerRequired = 100;

                            $sql = "Select
						access_power
					From
						Access
					Where
						access_page = 'edit_access'";

                            $result = $mysqli->query($sql);

                            $RankResult = mysqli_fetch_array($result, MYSQL_ASSOC);

                            $powerRequired = $RankResult['access_power'];


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

                                $accessList[0] = "";
                                $accessIdArray[0] = 0;
                                $accessPowerArray[0] = 0;
                                $accessCount = 0;

                                $sql = "Select
							access_id, access_page, access_power
						From
							Access";

                                $result = mysql_query($sql, $conn) or die(mysql_error());

                                while ($row = mysql_fetch_assoc($result)) {
                                    foreach ($row as $name => $value) {
                                        if ($name == "access_id") {
                                            $accessIdArray[$accessCount] = $value;
                                        }
                                        if ($name == "access_page") {
                                            $accessList[$accessCount] = $value;
                                        }
                                        if ($name == "access_power") {
                                            $accessPowerArray[$accessCount] = $value;
                                            $accessCount++;
                                        }
                                    }
                                }

                                echo "<input type ='text' readonly='readonly' name ='warning' size='70' value ='Be Careful with how you set these values or people might complain!!!' />";

                                echo "<br />";
                                echo "<br />";

                                echo "<br />";
                                echo "<label>Your Username:</label>";
                                echo "<input type ='text' readonly='readonly' name ='adminName' value ='" . $adminName . "' />";
                                echo "<br />";
                                echo "<br />";

                                echo "<table class='Display' border=''>";

                                echo "<tr>";
                                echo "<th>Page</th>";
                                echo "<th>Current Power</th>";
                                echo "<th>New Power</th>";
                                echo "</tr>";

                                for ($x = 0; $x < $accessCount; $x++) {
                                    echo "<tr>";
                                    echo "<td>" . $accessList[$x] . "</td>";
                                    echo "<td><input type ='text' readonly='readonly' name ='currentPower" . $x . "' value ='" . $accessPowerArray[$x] . "' /></td>";
                                    echo "<td><input type ='text' name ='newPower" . $x . "' value ='" . $accessPowerArray[$x] . "' /></td>";
                                    echo "</tr>";
                                }
                                echo "</table>";

                                echo "<br />";
                                echo "<br />";

                                echo "<input type = 'submit' value = 'Submit Changes'  />";
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
                <footer align="center">
                    EVE Online, the EVE logo, EVE and all associated logos and designs are the intellectual property of
                    CCP hf.
                    <br/>
                    All artwork, screenshots, characters, vehicles, storylines, world facts or other recognizable
                    features of the intellectual property relating to these trademarks are likewise the intellectual
                    property of CCP hf.
                    <br/>
                    EVE Online and the EVE logo are the registered trademarks of CCP hf. All rights are reserved
                    worldwide. All other trademarks are the property of their respective owners.
                    <br/>
                    CCP hf. has granted permission to tdsin.net to use EVE Online and all associated logos and designs
                    for promotional and information purposes on its website but does not endorse, and is not in any way
                    affiliated with, tdsin.net.
                    <br/>
                    CCP is in no way responsible for the content on or functioning of this website, nor can it be liable
                    for any damage arising from the use of this website.
                </footer>
            </DIV>

        </td>
    </tr>
</table>
</body>

<!-- InstanceEnd --></html>
