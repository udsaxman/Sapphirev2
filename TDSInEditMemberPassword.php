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

                    $powerRequired = 100;

                    $sql = "Select
						access_power
					From
						Access
					Where
						access_page = 'reset_password'";

                    $result = mysql_query($sql, $conn) or die(mysql_error());

                    while ($row = mysql_fetch_assoc($result)) {
                        foreach ($row as $name => $value) {
                            if ($name == "access_power") {
                                $powerRequired = $value;
                            }
                        }
                    }

                    if (isset($_SESSION["power"])) {
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

                        $userArray[0] = "";
                        $userIdArray[0] = 0;
                        $userCount = 0;

                        $sql = "Select
							user_id, user_name
						From
							Users
						left join
							Ranks on Users.rank_id = Ranks.rank_id
						Where
							rank_power > 0
						Order By
							user_name";

                        $result = mysql_query($sql, $conn) or die(mysql_error());

                        while ($row = mysql_fetch_assoc($result)) {
                            foreach ($row as $name => $value) {
                                if ($name == "user_id") {
                                    $userIdArray[$userCount] = $value;
                                }
                                if ($name == "user_name") {
                                    $userArray[$userCount] = $value;
                                    $userCount++;
                                }
                            }
                        }

                        echo "<form action='processEditMemberPassword.php' method='post'>";
                        echo "<fieldset>";
                        echo "<legend>Change User Password</legend>";
                        echo "<label>User:</label>";
                        echo "<select name = 'selUser'>";
                        echo "<option value =0>-=Select Pilot=-</option>";
                        for ($i = 1; $i < $userCount + 1; $i++) {
                            echo "<option value =" . $userIdArray[$i - 1] . ">"
                                . $userArray[$i - 1] . "</option>";
                        }
                        echo "</select>";

                        echo "<br />";
                        echo "<label>New Password:</label>";
                        echo "<input type = 'password' name = 'password1'/>";
                        echo "<label>Confirm Password:</label>";
                        echo "<input type = 'password' name = 'password2'/>";

                        echo "<br />";
                        echo "<input type = 'submit' value = 'Submit New Password'  />";
                        echo "</fieldset>";
                        echo "</form>";
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

                    ?>

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
