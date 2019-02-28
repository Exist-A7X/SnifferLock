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

            $emailAddress = $_POST["emailAddress"];
            $username = $_POST["username"];
            $password = $_POST["password"];
            $confirmPassword = $_POST["confirmPassword"];


            $statement = mysqli_prepare($con, "SELECT COUNT(*)"
                    . " from DB_Users where username = ?");
            mysqli_stmt_bind_param($statement, "s", $username);
            mysqli_stmt_execute($statement);
            mysqli_stmt_store_result($statement);
            mysqli_stmt_bind_result($statement, $nameAlreadyExistsCheck);
            while (mysqli_stmt_fetch($statement)) {
                
            }


            if ($password != $confirmPassword) {
                $errorMessage = "Password fields don't match\n";
                $_SESSION["errorMessage"] = $errorMessage;
                header("Location: register.html");
                exit();
            }

            if ($nameAlreadyExistsCheck != 0) {
                $errorMessage = $errorMessage . "Username already exists\n";
                $_SESSION["errorMessage"] = $errorMessage;
                header("Location: register.html");
                exit();
            }


            if (!isset($errorMessage)) {


                $combined1 = "CREATE TABLE usr__" . $username . "_data 
                (receiverMAC VARCHAR(18), senderMAC VARCHAR(18),
                channelNum INT(11), RSSI INT(11),
                dateTime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP)";
                $statement = mysqli_prepare($con, $combined1);
                mysqli_stmt_execute($statement);


                $combined2 = "CREATE TABLE usr__" . $username . "_registeredDevices 
                (receiverMAC VARCHAR(18), dateTime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP)";
                $statement = mysqli_prepare($con, $combined2);
                mysqli_stmt_execute($statement);

                
                $confirmationCode = substr(md5(rand()), 0, 10);
                $combined3 = "INSERT INTO DB_Users (id, username,"
                        . " password, activationStatus, confirmationCode)"
                        . " VALUES (NULL, ?, ?, 0, ?)";
                $statement = mysqli_prepare($con, $combined3);
                mysqli_stmt_bind_param($statement, "sss", $username, $password, $confirmationCode);
                mysqli_stmt_execute($statement);

                
                
                include '../config.php';
                require '/usr/share/php/libphp-phpmailer/PHPMailerAutoload.php';
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = 587;
                $mail->SMTPSecure = 'tls';
                $mail->SMTPAuth = true;
                $mail->Username = $registrationEmail;
                $mail->Password = $registrationPassword;
                $mail->setFrom($registrationEmail, 'SnifferLock');
                $mail->addAddress('hamidbg95@gmail.com', 'Customer');
                $mail->isHTML(false);                                  // Set email format to HTML
                $mail->Subject = 'Account Validation';
                $mail->Body = "Hallo ". $username . ", \r\n"
                        . "Your account has been successfully created, "
                        . "please click on the link below to"
                        . "validate your account \r\n"
                        . "http://localhost/snifferLock/public_html/validation.php"
                        . "?username=".$username."&confirmationCode="
                        .$confirmationCode;

                if (!$mail->send()) {
                    echo 'Message could not be sent. ';
                    echo 'Mailer Error: ' . $mail->ErrorInfo;
                    exit;
                }



                $_SESSION['username'] = $username;
                header("Location: ./afterRegistration.html");
                exit();
            }
            ?>
        </section>

    </body>

</html>
