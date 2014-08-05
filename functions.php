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

function CheckOpActive($opID)
{
    include 'connection.php';

    $sql = "SELECT
                LiveOp_Active
            FROM
                LiveOps
            WHERE
                LiveOp_ID = ".$opID;

    $result = $mysqli->query($sql);
    $isactive= mysqli_fetch_assoc($result);
    if ($isactive['Liveop_Active'] == 1){
        return true;
    }else{
        return false;
    }

}
?>