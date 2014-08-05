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

                    if (isset($_SESSION["userName"])) {
                        echo "<p> &nbsp; Welcome to the <a href = './TDSInPatchNotes.php'>TDSIn Sapphire 1.5.2</a></p>";
                        echo "<p></p>";
                        echo "<p>&nbsp; Go to My Page tab for how much isk you have and what ops you've been on</p>";
                        echo "<p></p>";
                        echo "<p>&nbsp; Go to the View Ops tab to View Ops and Items</p>";
                        echo "<p></p>";
                        echo "<p>&nbsp; If you find any bugs or have any issues with Sapphire, please Eve-Mail naed21</p>";
                        echo "<br />";
                        echo "<br />";
                        echo "<a href = './TDSInFileViewer.php'>View Corp Files</a>";
                    } else {
                        echo "<p align='center'><a href = './TDSInLogin.php'>You are not Logged in</a></p>";
                    }

                    ?>

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
