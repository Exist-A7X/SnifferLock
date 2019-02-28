<?php

session_start();

include '../config.php';

$username = $_GET["username"];
$confirmationCode = $_GET["confirmationCode"];


$con = mysqli_connect($host, $db_username, $db_password, $db_name);
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$statement = mysqli_prepare($con, "SELECT COUNT(*)"
                    . " from DB_Users where username = ? "
                    . " and confirmationCode = ?");
mysqli_stmt_bind_param($statement, "ss", $username, $confirmationCode);
mysqli_stmt_execute($statement);
mysqli_stmt_store_result($statement);
mysqli_stmt_bind_result($statement, $pollResult);
while (mysqli_stmt_fetch($statement)) {
    
}


    
if ($pollResult == 1) {
    $_SESSION["username"] = $username;
    header("Location: ./userMonitorPage.php");
    exit();
} else {
    session_destroy();
    echo "registration error\n";
}
?>