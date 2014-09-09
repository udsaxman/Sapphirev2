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
//
//
//
//                    $sql = "Select
//					    	access_power
//					        From
//						      Access
//					        Where
//						      access_page = 'edit_op'";
//
//                    $result = $mysqli->query($sql);
//                    $AccessReq = mysqli_fetch_assoc($result);
//                    $powerRequired = $AccessReq['access_power'];

                    $powerRequired = CheckAccess('edit_op');

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

                        echo "<form action='TDSInSpeedOpConfirm.php' method='post' />";
                        echo "<fieldset>";
                        echo "<legend>Hanger Data</legend>";
                        echo "<textarea rows='40' cols='100' name='opData'></textarea>";
                        echo "<br />";
                        echo "<input type='submit' value='Submit' />";
                        echo "</fieldset>";
                        echo "</form>";
                    }

                    function AccessDenied($error)
                    {
                        echo "</fieldset>";
                        echo "</form>";

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
                <!-- Add Footer -->
                <?php include('footer.html'); ?>
            </DIV>

        </td>
    </tr>
</table>
</body>

<!-- InstanceEnd --></html>
