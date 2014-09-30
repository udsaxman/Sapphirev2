<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!-- InstanceBegin template="/Templates/SapphireTemplate.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
    <!-- InstanceBeginEditable name="doctitle" -->
    <title>TDSIN: Sapphire</title>
    <!-- InstanceEndEditable -->
    <link href="SapphireCSS.css" rel="stylesheet" type="text/css"/>
</head>

<body class="SapphireCSS">
<table width="1024" align="center">
    <tr>
        <td>

            <div align="center">
                <DIV ID="sapphire-top">
                    <IMG SRC="./images/sapphire_top.png" WIDTH=1024 HEIGHT=182 ALT="">
                </DIV>
                <DIV ID="sapphire" align="">
                    <IMG SRC="./images/sapphire.png" WIDTH=310 HEIGHT=39 ALT="">
                </DIV>
                <DIV ID="login">
                    <?php
                    if (isset($_SESSION["userName"])) {
                        echo "<A HREF='./TDSInLogin.php'><IMG SRC='./images/logout.png' WIDTH=63 HEIGHT=39 ALT=''></A>";
                    } else {
                        echo "<A HREF='./TDSInLogin.php'><IMG SRC='./images/login.png' WIDTH=63 HEIGHT=39 ALT=''></A>";
                    }
                    ?>
                </DIV>
                <DIV ID="home">
                    <A HREF="./TDSInHome.php"><IMG SRC="./images/home.png" WIDTH=67 HEIGHT=39 ALT=""></A>
                </DIV>
                <DIV ID="mypage">
                    <A HREF="./TDSInPlayerPage.php"><IMG SRC="./images/mypage.png" WIDTH=99 HEIGHT=39 ALT=""></A>
                </DIV>
                <DIV ID="viewops">
                    <A HREF="./TDSInViewOp.php"><IMG SRC="./images/viewops.png" WIDTH=107 HEIGHT=39 ALT=""></A>
                </DIV>
                <DIV ID="admin">
                    <A HREF="./TDSInAdminTools.php"><IMG SRC="./images/admin.png" WIDTH=82 HEIGHT=39 ALT=""></A>
                </DIV>
                <DIV ID="right">
                    <IMG SRC="./images/right.png" WIDTH=296 HEIGHT=39 ALT="">
                </DIV>
                <!-- InstanceBeginEditable name="content" -->
                <div id="content_area" align="left">

                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <?php

                    //This could be a good place to add in allowing people to ONLY process ops

                    include 'connection.php';
                    include 'functions.php';

                    $powerRequired = 100;


                    $powerRequired = CheckAccess('admin_tools');

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
                        //All of the Sumbit buttons need to be replaced with correct links to pages like the other ones are

                        echo "<form action = ' '>";
                        echo "<fieldset>";
                        echo "<legend>Admin Tools</legend>";

                        echo "<p>";
                        echo "<a href = './TDSInAddOp.php'><input type = 'button' value = 'Add Op'  /></a>";
                        echo "<a href = './TDSInEditOp.php'><input type = 'button' value = 'Edit Op'  /></a>";
                        echo "<a href = './TDSInSpeedOp.php'><input type = 'button' value = 'Speed Op'  /></a>";
                        echo "</p>";

                        echo "<p>";
                        echo "<a href = './TDSInItems.php'><input type = 'button' value = 'Edit Items'  /></a>";
                        echo "<a href = './TDSInItemCategories.php'><input type = 'button' value = 'Edit Item Categories'  /></a>";
                        echo "</p>";

                        echo "<p>";
                        echo "<a href = './TDSInEditRanks.php'><input type = 'button' value = 'Edit Ranks'  /></a>";
                        echo "<a href = './TDSInEditMembers.php'><input type = 'button' value = 'Edit Members'  /></a>";
                        echo "<a href = './TDSInEditDeletedMembers.php'><input type = 'button' value = 'Edit Deleted Members'  /></a>";
                        echo "<a href = './TDSInEditMemberPassword.php'><input type = 'button' value = 'Edit Member Password' /></a>";
                        echo "</p>";

                        echo "<p>";
                        echo "<a href = './TDSInEditAccess.php'><input type = 'button' value = 'Edit Page Access' /></a>";
                        echo "</p>";

                        echo "<p>";
                        echo "<a href = './TDSInTransfer.php'><input type = 'button' value = 'Transfer Isk' /></a>";
                        echo "<a href = './TDSInTransferHistory.php'><input type = 'button' value = 'View Transfer History' /></a>";
                        echo "</p>";

                        echo "<p>";
                        echo "<a href = './TDSInViewUsers.php'><input type = 'button' value = 'View All User Data' /></a>";
                        echo "</p>";

                        echo "<p>";
                        echo "<a href = './TDSInEveAPI.php'><input type = 'button' value = 'Eve API Page' /></a>";
                        echo "<a href = './TDSInTowers.php'><input type = 'button' value = 'View Tower Details' /></a>";
                        echo "</p>";

                        echo "</fieldset>";
                        echo "</form>";

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
