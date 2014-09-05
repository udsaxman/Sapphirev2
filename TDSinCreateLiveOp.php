<?php
session_start();
include('header.html');
?>

                <!-- InstanceBeginEditable name="content" -->
                <div id="content_area" align="left">

                    <br/>
                    <br/>



                         <form action="TDSinLiveOp.php"
                          method="post">
                <fieldset>
                    <legend>TDSIN LiveOps Tracker</legend>
<?php
include 'connection.php';
include 'functions.php';
        //Login Check
        if (isset($_SESSION["userID"])) {
            echo "<br />";
            //echo $_SESSION['current_op'];

            if (isset($_SESSION['current_op']))
            {
                $user_id = $_SESSION['userID'];
                $currentOp = $_SESSION['current_op'];
                //check that op is still active
                //echo $currentOp;
                if(CheckOpActive($currentOp)){


                    $sql = "select LiveOp_Name, LiveOp_Shares, LiveOp_Start, LiveOp_JoinTime, LiveOp_Sites, Users.user_name as FC from LiveOps JOIN LiveOp_Attendance on LiveOps.LiveOp_ID = LiveOp_Attendance.LiveOp_ID JOIN Users on LiveOps.LiveOp_FC_ID = Users.user_id WHERE member_id = " . $user_id." AND LiveOps.LiveOp_ID = ". $currentOp;
                    $result = $mysqli->query($sql);
                    $op = $result->fetch_assoc();
                    $result->free();

    //                echo "<br />";
                    echo "Op Information";
                    echo "<br />";
                    echo "<table class='Display' border=''>";
                    echo "<tr>";
                    echo "<td>Op Name</td>";
                    echo "<td>" . $op['LiveOp_Name'] . "</tr>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td>FC</td>";
                    echo "<td>" .$op['FC'] . "</tr>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td>Fleet Start Time</td>";
                    echo "<td>" . $op['LiveOp_Start'] . "</tr>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td>Total Sites Completed</td>";
                    echo "<td>" . $op['LiveOp_Sites'] . "</tr>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td>Sites you have been here for</td>";
                    echo "<td>" . $op['LiveOp_Shares'] . "</tr>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td>You joined the fleet at</td>";
                    echo "<td>" . $op['LiveOp_JoinTime'] . "</tr>";
                    echo "</tr>";
                    echo "</table>";

                    echo "<input type = 'submit' name='leave' id='leave' value = 'Leave Op'  />";
                    header("Refresh:10");

                    if(isset($_POST['leave']))
                    {
                        LeaveOp($_SESSION["current_op"], $_SESSION["userID"]);
                        header("Refresh:0");
                    }

               }else{
                    //op is no longer active, unset session variablet to send user back to list
                    unset($_SESSION["current_op"]);
                    header('Refresh:0');
                }


            }else{
                //echo "op not set";
                //$sql = select op_name from Ops;
                echo "Please select an OP or Create a new Op</br>";
                $sql = "Select LiveOp_ID, LiveOp_Name From LiveOps where LiveOp_Active = 1 order by LiveOp_ID desc LIMIT 0,20";
                $result = $mysqli->query($sql);

                while ($row = $result->fetch_assoc()) {
                    $OPArray[] = $row;
                }
                $result->free();

                   echo "<select name = 'selOp'>";
                    foreach ($OPArray as $oplist){
                    echo "<option value=".$oplist['LiveOp_ID'];
                    echo ">".$oplist['LiveOp_Name'];
                    echo "</option>";
                }

                echo "</select>";

                echo "<input type = 'submit' value = 'Join'  />";

                if (isset($_POST['selOp'])) {
                //code to join op

                    JoinOp($_POST['selOp'],$_SESSION['userID']);
                    header('Location: ./TDSinLiveOp.php');

                }
            }
        }else {
    echo "<p> You are not logged in as anyone, thus you cannot see this page </p>";

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