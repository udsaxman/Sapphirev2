<?php


function CheckAccess($pagename)
{
    include 'connection.php';

    $sql = "Select
				access_power
			From
				Access
			Where
				access_page = ".$pagename;

        $result = $mysqli->query($sql);
        $RankResult = mysqli_fetch_assoc($result);
        Return $RankResult['access_power'];

}
?>