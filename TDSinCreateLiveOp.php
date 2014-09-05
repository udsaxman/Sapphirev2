<?php
session_start();
include('header.html');
?>

                <!-- InstanceBeginEditable name="content" -->
                <div id="content_area" align="left">

                    <br/>
                    <br/>

                <form action="TDSinCreateLiveOp.php" method="post">
                <fieldset>
                    <legend>TDSIN LiveOps OP Creator</legend>
                    Operation Name: <input type="text" name="opname"><br>


<?php
include 'connection.php';
include 'functions.php';
        //Login Check
        if (isset($_SESSION["userID"])) {
            echo "<br />";
            //echo $_SESSION['current_op'];

            if (isset($_SESSION['current_op']))
            {
                  header('Location: ./TDSinLiveOp.php');
            }
            $user_id = $_SESSION['userID'];
                //check that op is still active
                //echo $currentOp;
            echo "<input type = 'submit' name='create' id='create' value = 'Create Op'  />";

              if(isset($_POST['create']))
                {

                   CreateOp($_SESSION["userID"],$_POST["opname"]);

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