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

function CheckOpActive($opID,$user_id)
{
    global $mysqli;
    $sql = "SELECT
                LiveOp_Active, LiveOp_FC_ID
            FROM
                LiveOps
            WHERE
                LiveOp_ID = ".$opID;
    $result = $mysqli->query($sql);
    $OpActive = mysqli_fetch_assoc($result);
    if ($OpActive['LiveOp_FC_ID']== $user_id and $OpActive['LiveOp_Active']== 1)
    {
        header('Location: ./TDSinLiveOp.php');
    } elseif($OpActive['LiveOp_Active'] == 1){
        return true;
    } else {
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
        //echo $_SESSION["current_op"]."set";
    }
    $sql = "CALL sp_LeaveLiveOp(" . $opID. ", " . $memberID . ")";

}
function CreateOp($FCID, $OPName)
{
    global $mysqli;

    $query =" Insert into LiveOps (LiveOp_FC_ID,LiveOp_Start,LiveOp_Name,LiveOp_Active) Values(".$FCID.",Now(),'".$OPName."',1);";
    echo $query;
    mysqli_query($mysqli, $query);
    $NewOpID = mysqli_insert_id($mysqli);
    JoinOp($NewOpID,$FCID);
    mysqli_close($mysqli);
    header("Location: ./TDSinLiveOpFC.php");
}
?>