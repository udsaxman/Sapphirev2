<?php

include 'connection.php';
function CheckAccess($pagename)
{
    global $mysqli;

    $sql = "Select
				access_power
			From
				Access
			Where
				access_page = '".$pagename."'";

        $result = $mysqli->query($sql);
        $RankResult = mysqli_fetch_assoc($result);
        Return $RankResult['access_power'];

}

function CheckOpActive($opID)
{
    global $mysqli;
    $sql = "SELECT
                LiveOp_Active
            FROM
                LiveOps
            WHERE
                LiveOp_ID = ".$opID;
    $result = $mysqli->query($sql);
    $isactive= $result -> fetch_row();
    if ($isactive[0] == 1){
        return true;
    }else{
        return false;
    }

}

function LeaveOp($opID, $memberID)
{
    global $mysqli;


    $sql = "CALL sp_LeaveLiveOp(" . $opID. ", " . $memberID . ")";

    if($mysqli->query($sql) === TRUE){
        //player has left op, unset session variable
        unset($_SESSION["current_op"]);
    }
}
function JoinOp($opID, $memberID)
{
    global $mysqli;

    $sql = "CALL sp_JoinLiveOp(" . $opID. ", " . $memberID . ")";
   // echo $sql;
    if($mysqli->query($sql) === TRUE){
        $_SESSION["current_op"] = $opID;
        echo $_SESSION["current_op"]."set";
    }
    $sql = "CALL sp_LeaveLiveOp(" . $opID. ", " . $memberID . ")";

}
?>