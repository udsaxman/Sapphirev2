<?php
session_start();

$fail = false;
$theName = "";

if (isset($_POST["name"]) && $_POST["name"] != "") {
    $theName = $_POST["name"];
} else {
    $fail = true;
}
//echo($password);
if (isset($_POST["password"]) && $_POST["password"] != "") {
    $password = $_POST["password"];
} else {
    $fail = true;
}

$strSearch = "'";
$strSearch2 = "*";
$strSearch3 = "\"";

$pos = stripos($theName, $strSearch);
$pos2 = stripos($theName, $strSearch2);
$pos3 = stripos($theName, $strSearch3);

if ($pos === false && $pos2 === false && $pos3 === false) {
} else {
    $fail = true;
}

$strSearch = "'";
//$strSearch2 = "*";
$strSearch3 = "\"";

$pos = stripos($PasswordOne, $strSearch);
//$pos2 = stripos($passwordOne, $strSearch2);
$pos3 = stripos($PasswordOne, $strSearch3);

if ($pos === false && $pos3 === false) {
} else {
    $fail = true;
}

$validName = 0;
$validPass = 0;
$rank = 0;

$userIdYo = 0;
$userSalt = NULL;
$userHash = "";

$resetRequired = false;
$userLocked = false;

function fail($error)
{
    if ($error == 0) {
        header('Location: ./TDSInError.php?Error=Invalid_Input_Login_Failed');
    } elseif ($error == 1) {
        header('Location: ./TDSInError.php?Error=Nope_Login_Failed');
    } else if ($error == 2) {
        header('Location: ./TDSInError.php?Error=You Account was locked because you failed to reset your password when required to');
    }
}

function success($code)
{
    if ($code != -1) {
        header('Location: ./TDSInPlayerPage.php');
    }
    /*else if($code == 1)
    {
        header( 'Location: ./TDSInPlayerPage.php?Message=You must change youre password, failure to do so will result in a lockout' ) ;
    }*/
}

if (!$fail) {
    include 'connection.php';
    global $userLocked, $resetRequired;
    $query = "SELECT
				user_id, user_password, rank_power, user_salt, isReset, isLocked
			From 
				Users 
			left join
   				Ranks on Users.rank_id = Ranks.rank_id
			Where 
				user_name = '" . strtolower($theName) . "'";

    $result = $mysqli->query($query);
    //$result = mysql_query($sql, $conn) or die(mysql_error());

    if ($result->num_rows == 0) {
        // User not found. So, redirect to login_form again.
        fail(1);
    }
    $userData = mysqli_fetch_assoc($result);

    $hash = crypt($password, '$6$rounds=10000$' + $userData['user_salt'] + '$');

    if ($hash != $userData['user_password']) {
        // Incorrect password. So, redirect to login_form again.
        fail(1);
    } else {

        if ($userData['isLocked'] == true) {
            fail(2);
        } else {
            $_SESSION["userName"] = $theName;
            $_SESSION["power"] = $userData['rank_power'];
            $_SESSION["userID"] = $userData['user_id'];

            if ($userData['isReset'] == 1) {
                global $userIdYo;
                $userIdYo = $userData['user_id'];

                $sql = "Call sp_UpdateUserReset(" . $userIdYo . ", 0)";
                $result = $mysqli->query($sql);
                //$result = mysql_query($sql, $conn) or die(mysql_error());

                $sql = "Call sp_UpdateUserLock(" . $userIdYo . ", 1)";
                $result = $mysqli->query($sql);
                //$result = mysql_query($sql, $conn) or die(mysql_error());
                success(1);
            } else {
                success(0);
            }
        }
    }
} else {
    fail(0);
}
?>