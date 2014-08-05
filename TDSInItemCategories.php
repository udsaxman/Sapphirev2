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

    $powerRequired = 100;

    include 'connection.php';

    $sql = "Select
					access_power
				From
					Access
				Where
					access_page = 'item_category'";

    $result = mysql_query($sql, $conn) or die(mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $name => $value) {
            if ($name == "access_power") {
                $powerRequired = $value;
            }
        }
    }

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
        echo "<form action = 'processItemCategories.php' method = 'post'>";
        echo "<fieldset>";
        echo "<legend>Item Categories</legend>";

        $catIds[0] = 0;
        $catNames[0] = "None";
        $catOrders[0] = 0;
        $catTaxs[0] = 0;
        $catOverrides[0] = 0;
        $catCount = 0;

        $sql = "Select * From Item_Category Order By category_order";
        $result = mysql_query($sql, $conn) or die(mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            foreach ($row as $name => $value) {
                if ($name == "category_id") {
                    $catIds[$catCount] = $value;
                }
                if ($name == "category_name") {
                    $catNames[$catCount] = $value;
                }
                if ($name == "category_order") {
                    $catOrders[$catCount] = $value;
                }
                if ($name == "category_taxOverride") {
                    $catTaxs[$catCount] = $value;
                }
                if ($name == "category_useOverride") {
                    $catOverrides[$catCount] = $value;
                    $catCount++;
                }
            }
        }

        echo "<table class='Display' border=''>";

        echo "<tr>";
        echo "<th>Category Name</th>";
        echo "<th>Category Order</th>";
        echo "<th>Category Tax</th>";
        echo "<th>Use Tax</th>";
        echo "</tr>";

        for ($i = 0; $i < $catCount; $i++) {
            echo "<tr>";
            echo "<td><input type = 'text' size='35' name = 'oldCatName" .
                $catIds[$i] . "' value = '" . $catNames[$i] . "' /></td>";
            echo "<td><input type = 'text' size='5' name = 'oldCatOrder" .
                $catIds[$i] . "' value = '" . $catOrders[$i] . "' /></td>";
            echo "<td><input type = 'text' size='10' name = 'oldCatTax" .
                $catIds[$i] . "' value = '" . $catTaxs[$i] . "' /></td>";
            if ($catOverrides[$i] == 1) {
                echo "<td><input type = 'checkbox' name = 'oldCatOverride" .
                    $catIds[$i] . "' checked='checked' value = 'yes' /></td>";
            } else {
                echo "<td><input type = 'checkbox' name = 'oldCatOverride" .
                    $catIds[$i] . "' value = 'yes' /></td>";
            }
        }
        echo "</table>";
        echo "</fieldset>";

        echo "<fieldset>";
        echo "<legend>New Categories</legend>";
        echo "<div id = 'divOutput'>";
        echo "</div>";
        echo "</fieldset>";
        echo "<input type = 'submit' value = 'Submit Changes'  />";

        echo "<button type = 'button' onclick='newCategory()'>";
        echo "Add Category";
        echo "</button>";
    }

    function AccessDenied($error)
    {
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

    <script type="text/javascript">
        //<![CDATA[
        //from TDSInItems.php
        var count = 0;

        function newCategory() {
            var result = "";
            result += "<label>CategoryName:</label>";
            result += "<input type = 'text' name = 'newCatName" + count + "' />";
            result += "<label>CategoryTax:</label>";
            result += "<input type = 'text' style='text-align:right' name = 'newCatTax" + count + "'  value = '0' />";
            result += "<label>UseTax:</label>";
            result += "<input type = 'checkbox' name = 'newCatOverride" + count + "' />";
            result += "<label>CategoryOrder:</label>";
            result += "<input type = 'text' name = 'newCatOrder" + count + "' />";
            result += "<br />";
            divOutput.innerHTML += result;

            count += 1;
        }
        //]]>
    </script>

</div>
<!-- InstanceEndEditable -->
<footer align="center">
    EVE Online, the EVE logo, EVE and all associated logos and designs are the intellectual property of CCP hf.
    <br/>
    All artwork, screenshots, characters, vehicles, storylines, world facts or other recognizable features of the
    intellectual property relating to these trademarks are likewise the intellectual property of CCP hf.
    <br/>
    EVE Online and the EVE logo are the registered trademarks of CCP hf. All rights are reserved worldwide. All other
    trademarks are the property of their respective owners.
    <br/>
    CCP hf. has granted permission to tdsin.net to use EVE Online and all associated logos and designs for promotional
    and information purposes on its website but does not endorse, and is not in any way affiliated with, tdsin.net.
    <br/>
    CCP is in no way responsible for the content on or functioning of this website, nor can it be liable for any damage
    arising from the use of this website.
</footer>
</DIV>

</td>
</tr>
</table>
</body>

<!-- InstanceEnd --></html>
