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
                    $powerRequired = 100;

                    $powerRequired = CheckAccess('reset_password');


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
            <?php include('footer.html'); ?>
            </DIV>

        </td>
    </tr>
</table>
</body>

<!-- InstanceEnd --></html>
