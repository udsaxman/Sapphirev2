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
                        echo "<form action = 'processPasswordChange.php' method = 'post'>";
                        echo "<fieldset>";
                        echo "<legend>Change Password</legend>";

                        echo "<label>Old Password</label>";
                        echo "<input type='password' name='oldPass' />";
                        echo "<br />";
                        echo "<label>New Password</label>";
                        echo "<input type='password' name='newPass1' />";
                        echo "<br />";
                        echo "<label>Confirm New Password</label>";
                        echo "<input type='password' name='newPass2' />";
                        echo "<br />";
                        echo "<input type = 'submit' value = 'Change Password'  /></a>";

                        echo "</fieldset>";
                        echo "</form>";
                    } else {
                        echo "<p>You are not logged in and thus cannot see this page</p>";
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
