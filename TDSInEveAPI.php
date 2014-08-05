<?php
session_start();
include('header.html');
?>

                <!-- InstanceBeginEditable name="content" -->
                <div id="content_area" align="left">

                    <br/>
                    <br/>
                    <br/>
                    <br/>

                    <?php
                    include 'connection.php';

                    $sql = "Select
						access_power
					From
						Access
					Where
						access_page = 'eve_api'";

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

                        $sql = "SELECT
							*
						FROM
							EveAPIKeys";

                        $result = mysql_query($sql, $conn) or die(mysql_error());

                        $apiKeyPKs[0] = 0;
                        $apiKeyIDs[0] = 0;
                        $apiVCodes[0] = "";
                        $apiCharacterNames[0] = "";
                        $apiCorpNames[0] = "";
                        $apiInUse[0] = 0;
                        $apiCount = 0;

                        while ($row = mysql_fetch_assoc($result)) {
                            foreach ($row as $name => $value) {
                                if ($name == "key_id") {
                                    $apiKeyPKs[$apiCount] = $value;
                                }
                                if ($name == "keyID") {
                                    $apiKeyIDs[$apiCount] = $value;
                                }
                                if ($name == "v_code") {
                                    $apiVCodes[$apiCount] = $value;
                                }
                                if ($name == "characterName") {
                                    $apiCharacterNames[$apiCount] = $value;
                                }
                                if ($name == "corperationName") {
                                    $apiCorpNames[$apiCount] = $value;
                                }
                                if ($name == "inUse") {
                                    $apiInUse[$apiCount] = $value;
                                    $apiCount++;
                                }
                            }
                        }

                        echo "<form action='processChangeAPIKey.php' method='post'>";
                        echo "<fieldset>";
                        echo "<legend>Change Active API Key</legend>";
                        for ($i = 0; $i < $apiCount; $i++) {
                            if ($apiInUse[$i] == 1)
                                echo "<input type='radio' name='key' checked='checked' value='" . $apiKeyPKs[$i] . "'>" . $apiCharacterNames[$i];
                            else
                                echo "<input type='radio' name='key' value='" . $apiKeyPKs[$i] . "'>" . $apiCharacterNames[$i];
                            echo "<label>	-	" . $apiKeyIDs[$i] . "	-	" . $apiCorpNames[$i] . "</label>";
                            echo "<br />";
                        }
                        echo "<input type='submit' value='Use Selected API Key' />";
                        echo "</fieldset>";
                        echo "</form>";

                        echo "<form action='processAddAPIKey.php' method='post'>";
                        echo "<fieldset>";
                        echo "<legend>Add Corp API Key</legend>";
                        echo "<label>Key Id</label>";
                        echo "<input type='text' name='keyID' value='' />";
                        echo "<label>Verification Code</label>";
                        echo "<input type='text' name='vCode' value='' />";
                        echo "<input type='submit' value='Add New Key' />";
                        echo "</fieldset>";
                        echo "</form>";
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
