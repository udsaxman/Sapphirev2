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

                    <form action="processItems.php"
                          method="post">
                        <fieldset>
                            <legend>Items</legend>
                            <?php

                            //There is no protection here to prevent someone form direct url going to this site

                            include 'connection.php';


                            $sql = "SELECT i.item_id, item_name, item_type,item_IskValue, item_iskValue/t.volume as m3value
					        	FROM Items i
						        LEFT JOIN Speed_Items si ON i.item_id = si.item_id
						        LEFT JOIN invTypes t ON t.typeID = si.eve_id order by i.item_id";

                            $result = $mysqli->query($sql);

                            while ($row = $result->fetch_array(MYSQLI_BOTH)) {
                                $itemIDs[] = $row;
                            }

                            $sql = "select * from Item_Category order by category_order";

                            $result = $mysqli->query($sql);

                            while ($row = $result->fetch_array(MYSQLI_BOTH)) {
                                $categoryIds[] = $row;
                            }


                            foreach ($categoryIds as $category) {
                                echo "<fieldset>";
                                if ($category['category_useOverride'] == 0) {
                                    echo "<legend>" . $category['category_name'] . "</legend>";
                                } else {
                                    echo "<legend>" . $category['category_name'] . " - Tax Override: " . $category['category_taxOverride'] . "%</legend>";
                                }

                                echo "<table class='Display' border=''>";
                                echo "<tr>";
                                echo "<th>Item Name</th>";
                                echo "<th>Item Value</th>";
                                echo "<th>Item Value/m3</th>";
                                echo "<tr>";

                                foreach ($itemIDs as $item) {
                                    if ($item['item_type'] == $category['category_id']) {
                                        echo "<tr>";
                                        echo "<td>" . $item['item_name'] . "</td>";
                                        echo "<td style='text-align:right'>" . number_format($item['item_IskValue'], 0, '.', ',') . "</td>";
                                        if ($item['m3Value'] <= 0)
                                            echo "<td>Unknown</td>";
                                        else
                                            echo "<td>" . $item['m3Value'] . "</td>";
                                        echo "</tr>";
                                    }
                                }

                                echo "</table>";
                                echo "</fieldset>";
                            }

                            ?>
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
