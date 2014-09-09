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


                if(!isset($_SESSION["userID"])){
                    AccessDenied(0);
                }
                if(CheckUserAccess($_SESSION["userID"],"speed_op")){

                    $sql = "Select op_id, op_name From Ops where op_processor = 0 order by op_id desc";
                    $result = $mysqli->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        $OPArray[] = $row;
                    }
                    $result->free();


                    echo "<form action='TDSInSpeedOpConfirm.php' method='post' />";

                    echo "Select a pending OP or Create new OP:  ";
                    echo "<select name = 'selOp'>";
                    echo "<option value=0>New Op</option>";
                    foreach ($OPArray as $oplist){
                        echo "<option value=".$oplist['op_id'];
                        if ($oplist['op_id'] == $selectedOp){
                            echo " selected = 'selected'";
                        }
                        echo ">".$oplist['op_name'];
                        echo "</option>";
                    }
                    echo "</select>";
                    echo "<fieldset>";
                    echo "<legend>Hanger Data</legend>";
                    echo "<textarea rows='40' cols='100' name='opData'></textarea>";
                    echo "<br />";
                    echo "<input type='submit' value='Submit' />";
                    echo "</fieldset>";
                    echo "</form>";


                    }
                else{
                    AccessDenied(1);
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
