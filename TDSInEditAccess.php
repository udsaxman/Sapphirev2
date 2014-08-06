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
                            include 'functions.php';
                            $powerRequired = 100;

                            $powerRequired = CheckAccess('edit_access');

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
                                global $mysqli;

                                   $sql = "Select
                                              access_id, access_page, access_power
                                           From
                                              Access";


                                $result = $mysqli->query($sql);

                                while ($row = $result->fetch_array(MYSQLI_BOTH)) {
                                    $AccessLevels[] = $row;
                                }
                                $result->free();


                                echo "<input type ='text' readonly='readonly' name ='warning' size='70' value ='Be Careful with how you set these values or people might complain!!!' />";

                                echo "<br />";
                                echo "<br />";

                                echo "<br />";
                                echo "<label>Your Username:</label>";
                                echo $adminName;
                                echo "<br />";
                                echo "<br />";

                                echo "<table class='Display' border=''>";

                                echo "<tr>";
                                echo "<th>Page</th>";
                                echo "<th>Current Power</th>";
                                echo "<th>New Power</th>";
                                echo "</tr>";

                                //$accessCount = count($AccessLevels["access_id"]);

                                foreach($AccessLevels as $access){
                                    echo "<tr>";
                                    echo "<td>" . $access['access_page'] . "</td>";
                                    echo "<td>" . $access['access_power'] . "</td>";
                                    echo "<input type='hidden' name='newPower[".$access['access_id']."][id]' value ='".$access['access_id']."'/></td>";
                                    //create an array of access values  - way easier than counting stuff
                                    echo "<td><input type ='text' name ='newPower[".$access['access_id']."][power]' value ='" . $access['access_power'] . "' /></td>";
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
                <?php include('footer.html'); ?>
            </DIV>

        </td>
    </tr>
</table>
</body>

<!-- InstanceEnd --></html>
