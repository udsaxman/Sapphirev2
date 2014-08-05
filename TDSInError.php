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

                    <p> &nbsp; The information you provided resulted in you coming to this page</p>

                    <p>What ever it was you were trying to do didn't work.</p>

                    <p>The Following Error Text may help you:</p>
                    <br/>
                    <?php
                    if (isset($_REQUEST["Error"])) {
                        echo "<p>" . $_REQUEST["Error"] . "</p>";
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
