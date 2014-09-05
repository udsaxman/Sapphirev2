<?php
include 'connection.php';

//Note to self, figure out userinput validation so people don't submit sql strings for update values
foreach ($_POST['catupdate'] as $cat){
    if(isset($cat['id']) && isset($cat['order'])&& isset($cat['tax'])){

        $sql = "CALL sp_UpdateItemCategory(".$cat['id'].",". $cat['order'].",". $cat['tax'].",".(isset($cat['override'])? 1 : 0)." )";
        $result = $mysqli->query($sql);
        if (!$result) {
            Fail();
        }
        $result->close();
    }
}

foreach ($_POST['newCat'] as $newcat){
    print_r($newcat);
    echo "</br>";

    if(isset($newcat['name']) && isset($newcat['tax'])&& isset($newcat['order'])){

        //create new category
        $sql = "CALL sp_AddNewItemCategory('".$newcat['name']."', ".$newcat['order'].", ".$newcat['tax'].", ".(isset($newcat['override'])? 1 : 0).")";
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
   header('Location: ./TDSInError.php');
}

function Success()
{
    header('Location: ./TDSInItemCategories.php');
    //echo "Test";
}

?>
