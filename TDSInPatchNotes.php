<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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

                    <p> &nbsp; Sapphire 1.5.2 </p>
                    <ul>
                        <li>Sapphire 1.5.2 is Released (2013-10-05)</li>
                        <li>Fixed a bug in which Add Ops and Speed Ops did not show the first pilot</li>
                    </ul>
                    <p> &nbsp; Sapphire 1.5.1 </p>
                    <ul>
                        <li>Sapphire 1.5.1 is Released (2013-07-13)</li>
                        <li>Added File Viewer Page to allow corp files to be viewed from Sapphire</li>
                        <li>Added TDSIn Welcome Booklet to File Viewer</li>
                        <li>Added link to File Viewer on Home Page</li>
                    </ul>
                    <p> &nbsp; Sapphire 1.5 </p>
                    <ul>
                        <li>Sapphire 1.5 is Released (2013-07-07)</li>
                        <li>Changed Add Ops Pilot Selection to default to -= Select Pilot=-</li>
                        <li>Changed Add Ops to reject the default Pilot when submitted</li>
                        <li>Changed Transfer Isk Pilot Selection to default to -= Select Pilot=-</li>
                        <li>Changed Transfer Isk to fail when the default Pilot is submitted</li>
                        <li>Changed Eve API Page to be protected behind Dyanamic Page Access</li>
                        <li>Added Page "Edit Member Password"</li>
                        <li>When a User's password is changed though "Edit Member Password", it is required to be
                            changed on next login
                        </li>
                        <li>Removed the epic hacks that was the old system used to change user passwords</li>
                        <li>Added Page Submit Validation to prevent ops with No Pilots or pilots with no shares from
                            being submitted
                        </li>
                        <li>Changed Tower Page to better display information</li>
                        <li>Changed Tower Page to display specific information only when needed (Reinforced Exit time)
                        </li>
                        <li>Added Tower Advance Page to show advance(more) details</li>
                        <li>Added the ability to assign pilots to towers</li>
                        <li>Added the ability to remove pilots from towers</li>
                        <li>Added the ability to declare a pilot as the Landlord of a tower</li>
                        <li>Added the "View all My Towers" button in Player Page</li>
                        <li>Added a list of all towers in Player Page that the user is a resident of</li>
                        <li>Added links to the specific towers that are listed in the Player Page</li>
                        <li>Added Speed Op Page</li>
                        <li>Added Speed Op Confirmation Page to link tab delimited Eve items to Sapphire items</li>
                        <li>Added the ability to auto fill Add Op pages from the Speed Op Confirmation Page</li>
                        <li>Added Item Value/m3 to the View Items page</li>
                    </ul>
                    <p> &nbsp; Sapphire 1.4 </p>
                    <ul>
                        <li>Sapphire 1.4 is Released (2013-05-13)</li>
                        <li>Added Additional Text to Corp Transaction Types</li>
                        <li>Added Hover Text to Corp Transaction Types to show the Additional Text</li>
                        <li>Added Eve API Support</li>
                        <li>Added Pulling Information about Corp Towers from Eve API</li>
                        <li>Several Small Bugs were fixed</li>
                    </ul>
                    <p> &nbsp; Sapphire 1.3 </p>
                    <ul>
                        <li>Sapphire 1.3 is Released (2013-04-21)</li>
                        <li>Added Edit Ops, which allows ops to be editted in any way.</li>
                        <li>Edit Ops will handle all situations with any changes to an op</li>
                        <li>Changed Tab order in Ops to follow non readonly fields and then readonly fields</li>
                        <li>Changed Ops to no longer accept negative shares. Those with negative shares default to 0
                        </li>
                        <li>Fixed Transation History Linking to the old View Ops Page</li>
                        <li>Fixed User Account Settings Linking to the old Account Settings Page</li>
                        <li>Changed the Menu to say "Log Out" when you are logged in</li>
                    </ul>
                    <p> &nbsp; Sapphire 1.2.1 </p>
                    <ul>
                        <li>Sapphire 1.2.1 is Released (2013-04-07)</li>
                        <li>Fixed an Issue in giving paychecks</li>
                    </ul>
                    <p> &nbsp; Sapphire 1.2 </p>
                    <ul>
                        <li>Sapphire 1.2 is Released (2013-04-04)</li>
                        <li>Changed all isk related datatypes from Double to Decimal(14,3)</li>
                        <li>Changed The Entire Website to use Art Provided by Symbiote Anisoptera and Tshaowdyne
                            Dvorak
                        </li>
                        <li>Changed The Transfer page to now both display and accept isk values with commas</li>
                        <li>Added Dynamic Item Categories</li>
                        <li>Dynamic Item Categories can now have overrided Tax values</li>
                        <li>All Pages that Displayed Items now display them with the new Dynamic Categories</li>
                    </ul>
                    <p> &nbsp; Saphire 1.1J </p>
                    <ul>
                        <li>Saphire 1.J is Released (2013-04-01)</li>
                        <li>The Site now has 100% less complaints about it's visual appeal</li>
                    </ul>
                    <p> &nbsp; Sapphire 1.1 </p>
                    <ul>
                        <li>Sapphire 1.1 is Released (2013-03-17)</li>
                        <li>Fixed a spelling mistake in Player Page</li>
                        <li>Fixed Edit Memebers having a Test Banner</li>
                        <li>Moved Change Password to Player Settings Page</li>
                        <li>Restructured how passwords work and are stored</li>
                        <li>Fixed the issue in which Usernames were "Sometimes" case sensative</li>
                        <li>Changed Edit Memebers to show ranks > -1</li>
                        <li>Changed Transaction History to allow Target Players to be Specified</li>
                        <li>Changed Transaction History to allow a specified # of transactions to be shown, including
                            8
                        </li>
                        <li>Changed All Admin Pages/Tools to have dynamic access permissions</li>
                        <li>View all User Data now links that user's name to that user's Transaction History</li>
                        <li>Added a Patchnotes Page</li>
                        <li>Added an Edit Deleted Memebers Page, to show ranks < 0</li>
                        <li>Added a Player Settings Page</li>
                        <li>Added a button in Player Page called Account Settings, that links to the Player Settings
                            Page
                        </li>
                        <li>Added a button in Player Page called View Full Transaction History, that links to the
                            Transaction History Page for that User
                        </li>
                        <li>Added a Site Access Page, which alows page access to be dynamic</li>
                        <li>Silly Dean, Ranks aren't Items</li>
                    </ul>
                    <p> &nbsp; Sapphire 1.0.1 </p>
                    <ul>
                        <li>Patched Ops to save items with negative amounts (2013-03-14)</li>
                    </ul>
                    <p> &nbsp; Sapphire 1.0 </p>
                    <ul>
                        <li>Sapphire is Released (2013-03-03)</li>
                    </ul>

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
