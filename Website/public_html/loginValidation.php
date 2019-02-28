<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Snifferlock | Validation</title>
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>
  <header>
    dude
  </header>

  <section>
    <?php
        session_start();

        include '../config.php';

        $con = mysqli_connect($host, $db_username, $db_password, $db_name);
        
        $username = $_POST["username"];
        $password = $_POST["password"];
        
        $statement = mysqli_prepare($con, "SELECT 1 FROM DB_Users WHERE username = ? AND password = ?" );
        mysqli_stmt_bind_param($statement, "ss", $username, $password);
        mysqli_stmt_execute($statement);


        mysqli_stmt_store_result($statement);
        mysqli_stmt_bind_result($statement, $success);


        while(mysqli_stmt_fetch($statement)){}

        if ($success == 1) {
          $_SESSION['username'] = $username;
          header("Location: ./userMonitorPage.php");
          exit();
        } else {
          session_destroy();
          header("Location: ./login.html");
          exit();
        }
    ?>
  </section>

</body>

</html>
