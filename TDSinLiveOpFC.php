<?php
session_start();
include('header.html');
?>
                <!-- InstanceBeginEditable name="content" -->
                <div id="content_area" align="left">

                    <br/>
                    <br/>



                    <form action="TDSinLiveOp.php"
                          method="">
                        <fieldset>
                            <legend>TDSIN LiveOps Tracker</legend>
                            <?php
                            include 'connection.php';
                            //Login Check
                            if (isset($_SESSION["userID"])) {
                                echo "<br />";
                                //echo $_SESSION['current_op'];
                                if (isset($_SESSION['current_op']))
                                {
                                    $user_id = $_SESSION["userID"];
                                    $currentOp = $_SESSION["current_op"];

                                    $sql = "select LiveOps.LiveOp_ID as OpID, LiveOp_Name, LiveOp_Shares, LiveOp_Start, LiveOp_JoinTime, LiveOp_Sites,LiveOp_LootLoc, Users.user_name as FC from LiveOps JOIN LiveOp_Attendance on LiveOps.LiveOp_ID = LiveOp_Attendance.LiveOp_ID JOIN Users on LiveOps.LiveOp_FC_ID = Users.user_id WHERE member_id = " . $user_id." AND LiveOps.LiveOp_ID = ". $currentOp;
                                    //echo $sql;
                                    $result = $mysqli->query($sql);
                                    $op = $result->fetch_assoc();
                                    $result->free();

                                    echo "<br />";
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
                                    echo "<td>Loot Location</td>";
//                                    echo "<td>" . $op['LiveOp_LootLoc'] . "</tr>";
                                    echo "<td><input type='text' name = 'oldOp" . $op['OpID'] . "' value = '" . $op['LiveOp_LootLoc'] . "' />";
                                    echo "<a href = ''><input type = 'button' value = 'Update'  /></a></td>";
//                                    echo "</tr>";
//                                    echo "<tr>";
//                                    echo "<td>Sites you have been here for</td>";
//                                    echo "<td>" . $op['LiveOp_Shares'] . "</tr>";
//                                    echo "</tr>";

                                    echo "</table>";

                                    $sql = "Select
                                                Users.user_id as id, user_name, LiveOp_Shares, LiveOp_JoinTime,LiveOp_LeaveTime, LiveOp_Active
                                            from
                                                Users
                                            left join
                                                    LiveOp_Attendance
                                                on
                                                   LiveOp_ID = " . $currentOp . "
                                                AND
                                                    LiveOp_Attendance.member_id = Users.user_id
                                            where
                                                Users.user_id =
                                                ANY
                                                    (select member_id from LiveOp_Attendance where LiveOp_ID = " . $currentOp . ")
                                            group by
                                                user_name";
                                    //echo $sql;
                                    $result = $mysqli->query($sql);

                                    while ($row = $result->fetch_array(MYSQLI_BOTH)) {
                                        $Users[] = $row;
                                    }
                                    $result->free();

                                    echo "<fieldset>";
                                    echo "<legend>Pilots</legend>";
                                    echo "<table class='Display' border=''>";
                                    echo "<tr>";
                                    echo "<th>Pilot Name</th>";
                                    echo "<th>Shares</th>";
                                    echo "<th>Active</th>";
                                    echo "<th>Join Time</th>";
                                    echo "<th>Left Time</th>";
                                    echo "<th>Flag as Inactive</th>";
                                    echo "</tr>";

                                    foreach ($Users as $user) {
                                        echo "<tr>";
                                        echo "<td>" . $user['user_name'] . "</td>";
                                        echo "<td><input type='text' name = 'oldUser" . $user['id'] . "' value = '" . $user['LiveOp_Shares'] . "' /></td>";
                                       // echo "<td><input type='checkbox' name=''oldUserActive".$user['id']."value='active'";
                                        //echo ($user['LiveOp_Active']== 1) ? 'checked="checked"' : '';
                                        //echo "/></td>";
                                        echo "<td>";
                                        echo ($user['LiveOp_Active']== 1) ? "YES" : "NO";
                                        echo "</td>";
                                        echo "<td>".$user['LiveOp_JoinTime'] ."</td>";
                                        echo "<td>".$user['LiveOp_LeaveTime'] ."</td>";
                                        if($user['LiveOp_Active']== 1){
                                            echo "<td><a href = ''><input type = 'button' value = 'Flag as Inactive'  /></a></td>";
                                        }else{
                                            echo "<td><a href = ''><input type = 'button' value = 'Flag as Active'  /></a></td>";
                                        }
                                         echo "</tr>";
                                    }
                                    echo "</table>";
                                    echo "</br>";
                                    echo "<a href = ''><input type = 'button' value = 'Warp to next site and add credit to active members'  /></a>";
                                    echo "</br>";
                                    echo "</br>";
                                    echo "<a href = ''><input type = 'button' value = 'Update Share Counts'  /></a>";
                                    echo "</br>";
                                    echo "</br>";
                                    echo "<a href = ''><input type = 'button' value = 'Finish fleet and submit'  /></a>";
                                    echo "</br>";
                                } else{
                                    //echo "op not set";
                                    //$sql = select op_name from Ops;
                                    $sql = "Select LiveOp_ID, LiveOp_Name From LiveOps where LiveOp_Active = 1 order by LiveOp_ID desc LIMIT 0,20";
                                    $result = $mysqli->query($sql);

                                    while ($row = $result->fetch_assoc()) {
                                        $OPArray[] = $row;
                                    }
                                    $result->free();

                                    echo "<select name = 'selOp'>";
                                    foreach ($OPArray as $oplist){
                                        echo "<option value=".$oplist['LiveOp_ID'];
                                        if ($oplist['LiveOp_ID'] == $selectedOp){
                                            echo " selected = 'selected'";
                                        }
                                        echo ">".$oplist['LiveOp_Name'];
                                        echo "</option>";
                                    }

                                    echo "</select>";

                                    echo "<input type = 'submit' value = 'Join'  />";

                                    if (isset($_REQUEST["selOp"])) {
                                        //code to join op

                                        $selOp = $_REQUEST["selOp"];
                                        // echo "request recieved";
                                        $user_id = $_SESSION["userID"];
                                        $sql = "CALL sp_JoinLiveOp(" . $selOp . ", " . $user_id . ")";
                                        //echo $sql;
                                        if($mysqli->query($sql) === TRUE){
                                            $_SESSION['current_op'] = $selOp;
                                            //echo $_SESSION['current_op']."set";
                                        }
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