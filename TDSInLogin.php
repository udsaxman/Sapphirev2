<?php
session_start();

if (isset($_SESSION["userName"])) {
    session_destroy();
//    $_SESSION["userName"] = "Test";
//    $_SESSION["power"] = 0;
//    $_SESSION["user_id"] = NULL;


    //unset($_SESSION["userName"]);
    //unset($_SESSION["power"]);
} else {
    $_SESSION["userName"] = NULL;
    $_SESSION["power"] = NULL;
    $_SESSION["user_id"] = NULL;
}

$_SESSION["userName"] = NULL;
$_SESSION["power"] = NULL;
$_SESSION["user_id"] = NULL;
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

                    <form action="processLogin.php"
                          method="post">
                        <fieldset>
                            <legend><strong>Login</strong></legend>
                            <p>
                                <label>Username:</label>
                                <input type="text"
                                       name="name"/>
                            </p>

                            <p>
                                <label>Password :</label>
                                <input type="password"
                                       name="password"/>
                            </p>

                            <p>
                                <input type="submit" value="Login"/>
                                <a href="./TDSInRegister.php"><input type="button" value="Register"/></a>
                            </p>

                        </fieldset>
                    </form>

                </div>
                <!-- InstanceEndEditable -->
                <?php include('footer.html'); ?>
            </DIV>

        </td>
    </tr>
</table>
</body>

<!-- InstanceEnd --></html>
