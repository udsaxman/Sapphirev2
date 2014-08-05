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
                        $userNameList[0] = "";
                        $userRankList[0] = "";
                        $userIskList[0] = 0;
                        $userCount = 0;

                        $totalIsk = 0;

                        include 'connection.php';

                        $sql = "Select
							user_name, rank_name, user_isk
						From
							Users
						left join
							Ranks on Users.rank_id = Ranks.rank_id
						Where
							Ranks.rank_power > 0
						Order by
							user_name";


                        $result = $mysqli->query($sql);

                        while ($row = $result->fetch_array(MYSQLI_BOTH)) {
                            $Users[] = $row;
                        }

                        echo "<table class='Display' border=''>";

                        echo "<tr>";
                        echo "<th>User Name</th>";
                        echo "<th>User Rank</th>";
                        echo "<th>Isk</th>";
                        echo "</tr>";

                        foreach ($Users as $user) {
                            echo "<tr>";
                            echo "<td><a href = './TDSInTransferHistory.php?TargetUser=" . $user['user_name'] . "'>" . $user['user_name'] . "</a></td>";
                            echo "<td>" . $user['rank_name']. "</td>";
                            if ($user['user_isk'] < 0)
                                echo "<td><span>" . number_format($user['user_isk'], 2, '.', ',') . "</span></td>";
                            else
                                echo "<td>" . number_format($user['user_isk'], 2, '.', ',') . "</td>";
                            echo "</tr>";
                            $totalIsk += $user['user_isk'];
                        }

                        echo "</table>";

                        echo "<br />";
                        echo "<br />";

                        echo "<p>Total = " . number_format($totalIsk, 2, '.', ',') . " </p>";
                    } else {
                        echo "You are not logged in, please loggin to view this page";
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
