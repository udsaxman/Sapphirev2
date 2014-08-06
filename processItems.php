<?php
include 'connection.php';
//Note to self, figure out userinput validation so people don't submit sql strings for itemnames/values
print_r($_POST);

foreach ($_POST['itemupdate'] as $item){
    if(isset($item['id']) && isset($item['value'])&& isset($item['order'])&& isset($item['type'])){

    //update item value
        $sql = "CALL sp_UpdateItemValue(".$item['id'].", ".$item['value'].")";
        echo $sql."</br>";
        $mysqli->query($sql);


     //update item order
        $sql = "CALL sp_UpdateItemOrder(".$item['id'].",". $item['order']. ")";
        echo $sql."</br>";
        $mysqli->query($sql);


    //update item type
        $sql = "CALL sp_UpdateItemType(".$item['id'].",".$item['type'] .")";
        echo $sql."</br>";
       $mysqli->query($sql);
     }


}







//
//
//
//$new = true;
//$old = true;
//
//$newCount = 0;
//$oldCount = 0;
//
//$newItemname[0] = "";
//$newItemtype[0] = 0;
//$newItemvalue[0] = 0;
//$newItemorder[0] = 0;
//
//$oldItemname[0] = "";
//$oldItemtype[0] = 0;
//$oldItemvalue[0] = 0;
//$oldItemorder[0] = 0;
//
//$oldTotal = 0;
//
//while ($new == true) {
//    //These need to be changed to $_POST
//    if (isset($_POST["newItemname" . $newCount])) {
//        $newItemname[$newCount] = $_POST["newItemname" . $newCount];
//        $newItemtype[$newCount] = $_POST["selType" . $newCount];
//
//        if (isset($_POST["newValue" . $newCount])) {
//            $newItemvalue[$newCount] = $_POST["newValue" . $newCount];
//        } else {
//            $newItemvalue[$newCount] = 0;
//        }
//
//        if (isset($_POST["newOrder" . $newCount])) {
//            $newItemorder[$newCount] = $_POST["newOrder" . $newCount];
//        } else {
//            $newItemorder[$newCount] = 0;
//        }
//        $newCount++;
//        //echo "yes";
//    } else {
//        $new = false;
//        //echo "no";
//        //echo "newItemname" . $count;
//    }
//}
//
//$sql = "Select
//			Count(item_id) As Total
//		From
//			Items";
//
//$result = mysql_query($sql, $conn) or die(mysql_error());
//
//while ($row = mysql_fetch_assoc($result)) {
//    foreach ($row as $name => $value) {
//        if ($name == "Total") {
//            $oldTotal = $value;
//        }
//    }
//}
//
//for ($i = 0; $i < $oldTotal + 1; $i++) {
//    //These need to be changed to $_POST
//    if (isset($_POST["oldItemname" . $i])) {
//        $oldItemname[$i] = $_POST["oldItemname" . $i];
//        if (isset($_POST["oldValue" . $i])) {
//            $oldItemvalue[$i] = $_POST["oldValue" . $i];
//        } else {
//            $oldItemvalue[$i] = 0;
//        }
//
//        if (isset($_POST["oldOrder" . $i])) {
//            $oldItemorder[$i] = $_POST["oldOrder" . $i];
//        } else {
//            $oldItemorder[$i] = 0;
//        }
//        if (isset($_POST["oldType" . $i])) {
//            $oldItemtype[$i] = $_POST["oldType" . $i];
//        } else {
//            $oldItemtype[$i] = 1;
//        }
//        $oldCount++;
//    }
//}
//
////echo $oldCount;
//
////OLD ITEMS UPDATE
//for ($i = 0; $i < $oldCount; $i++) {
//    $oldItemvalue[$i] = str_replace(",", "", $oldItemvalue[$i]);
//    //echo "Call sp_UpdateItemValue(". $i .", ". $oldItemvalue[$i] .")";
//    if (trim($oldItemvalue[$i]) != "" || !is_numeric($oldItemvalue[$i])) {
//        $sql = "Call sp_UpdateItemValue(" . $i . ", " . $oldItemvalue[$i] . ")";
//        $result = mysql_query($sql, $conn) or die(mysql_error());
//    }
//
//    if (trim($oldItemorder[$i]) != "" || !is_numeric($oldItemorder[$i])) {
//        $sql = "Call sp_UpdateItemOrder(" . $i . ", " . $oldItemorder[$i] . ")";
//        $result = mysql_query($sql, $conn) or die(mysql_error());
//    }
//
//    if (trim($oldItemtype[$i]) != "" || !is_numeric($oldItemtype[$i])) {
//        $sql = "Call sp_UpdateItemType(" . $i . ", " . $oldItemtype[$i] . ")";
//        $result = mysql_query($sql, $conn) or die(mysql_error());
//    }
//}
//
////NEW ITEMS INSERT
////There is an Error when you try to enter NULL, because the user hit enter by mistake or ignorance
//$strSearch = "'";
//
//for ($i = 0; $i < $newCount; $i++) {
//    $pos = stripos($newItemname[$i], $strSearch);
//    if (trim($newItemname[$i]) != "" && $pos === false) {
//        $newItemvalue[$i] = str_replace(",", "", $newItemvalue[$i]);
//        if (trim($newItemvalue[$i]) == "" || !is_numeric($newItemvalue[$i])) {
//            $newItemvalue[$i] = 0;
//        }
//        if (trim($newItemorder[$i]) == "" || !is_numeric($newItemorder[$i])) {
//            $newItemorder[$i] = 0;
//        }
//
//        $sql = "Call sp_AddNewItem('" . $newItemname[$i] . "', " . $newItemtype[$i] . ", " . $newItemvalue[$i] . ", " . $newItemorder[$i] . ")";
//
//        $result = mysql_query($sql, $conn) or die(mysql_error());
//    }


Success();

function Fail()
{
    //header('Location: ./TDSInError.php');
}

function Success()
{
  //  header('Location: ./TDSInAdminTools.php');
    //echo "Test";
}

?>