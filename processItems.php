<?php
include 'connection.php';
//Note to self, figure out userinput validation so people don't submit sql strings for itemnames/values
print_r($_POST);

foreach ($_POST['itemupdate'] as $item){
    if(isset($item['id']) && isset($item['value'])&& isset($item['order'])&& isset($item['type'])){

    //update item value
        $sql = "CALL sp_UpdateItemValue(".$item['id'].", ".$item['value'].")";
        $result = $mysqli->query($sql);
        if (!$result) {
            Fail();
        }
        $result->close();


     //update item order
        $sql = "CALL sp_UpdateItemOrder(".$item['id'].",". $item['order']. ")";
        $result = $mysqli->query($sql);
        if (!$result) {
            Fail();
        }
        $result->close();


    //update item type
        $sql = "CALL sp_UpdateItemType(".$item['id'].",".$item['type'] .")";
        $result = $mysqli->query($sql);
        if (!$result) {
            Fail();
        }
        $result->close();
     }
}

foreach ($_POST['newItems'] as $newitem){
    if(isset($newitem['name']) && isset($newitem['value'])&& isset($newitem['order'])&& isset($newitem['type'])){

        //create new item
        $sql = "CALL sp_AddNewItem('".$newitem['name']."', ".$newitem['type'].", ".$newitem['value'].", ".$newitem['order'].")";
        $result = $mysqli->query($sql);
        if (!$result) {
            Fail();
        }
        $result->close();

    }
}


$mysqli->close();

Success();

function Fail()
{
    //header('Location: ./TDSInError.php');
}

function Success()
{
    //header('Location: ./TDSInAdminTools.php');
    //echo "Test";
}

?>