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

                    echo "<div id = 'divOutput'>";
                    echo "</div>";

                    ?>

                    <br/>
                    <a href="javascript:displayItem('https://dl.dropboxusercontent.com/u/33520992/TDSIN%20Welcome%20Booklet%20V2.pdf');">Intro
                        Booklet</a>

                    <script type="text/javascript">

                        function displayItem(targetLink) {
                            var result = "<iframe src=\"" + targetLink + "\" style=\"width:1000px; height:800px;\" frameborder=\"0\"></iframe>";

                            divOutput.innerHTML = result;
                        }

                    </script>

                </div>
                <!-- InstanceEndEditable -->
                <footer align="center">
                    EVE Online, the EVE logo, EVE and all associated logos and designs are the intellectual property of
                    CCP hf.
                    <br/>
                    All artwork, screenshots, characters, vehicles, storylines, world facts or other recognizable
                    features of the intellectual property relating to these trademarks are likewise the intellectual
                    property of CCP hf.
                    <br/>
                    EVE Online and the EVE logo are the registered trademarks of CCP hf. All rights are reserved
                    worldwide. All other trademarks are the property of their respective owners.
                    <br/>
                    CCP hf. has granted permission to tdsin.net to use EVE Online and all associated logos and designs
                    for promotional and information purposes on its website but does not endorse, and is not in any way
                    affiliated with, tdsin.net.
                    <br/>
                    CCP is in no way responsible for the content on or functioning of this website, nor can it be liable
                    for any damage arising from the use of this website.
                </footer>
            </DIV>

        </td>
    </tr>
</table>
</body>

<!-- InstanceEnd --></html>
