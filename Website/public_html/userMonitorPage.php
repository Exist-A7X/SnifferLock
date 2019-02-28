<!DOCTYPE html><html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Snifferlock | Monitor</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        table {
            margin: auto;
            margin-top: 30px;
            margin-bottom: 30px;

            border-collapse: collapse;
            width: 80%;
        }
        
        th, td {
          text-align: left;
          padding: 8px;
        }
        
        tr:nth-child(even){background-color: #f2f2f2}
        
        th {
          background-color: #4CAF50;
          color: white;
        }
    </style>
    
  </head>
  <body>
    <header>
      <div class="container">
        <div id="branding">
          <h1><span class="highlight">Sniffer</span>Lock</h1>
        </div>
        <nav>
          <ul>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </nav>
      </div>
    </header>

    <section id="showcase", style="min-height: 200px;">
      <div class="container">
        
        <?php
            session_start();            
            echo "<h1>Welcome " . $_SESSION["username"] . "</h1>";
        ?>
        
      </div>
    </section>
    

     
    <table style="width:30%">
        <th>Registered Devices</th>
        <th>Registration Date</th>

    <?php        
        if ( isset( $_SESSION['username'] ) ) {
            
            $tableName = "usr__".$_SESSION['username']."_registeredDevices";
            include '../config.php';
    
            $con = mysqli_connect($host, $db_username, $db_password, $db_name);
            /* check connection */
            if (mysqli_connect_errno()) {
                printf("Connect failed: %s\n", mysqli_connect_error());
                exit();
            }
            
            $combined = "SELECT * FROM " . $tableName;
            $statement = mysqli_prepare($con, $combined );
            mysqli_stmt_execute($statement);
            mysqli_stmt_bind_result($statement, $MAC, $dateTIme);
            
            while (mysqli_stmt_fetch($statement) == true){
                    echo "<tr>";
                    echo "<td>"."$MAC"."</td>";
                    echo "<td>"."$dateTIme"."</td>";
                    echo "</tr>";
            }              
        } else {
            session_destroy();
            header("Location: login.html");
            exit();
        }        
    ?>    
    </table>
      
      
      
    <table>
        <th>Receiver MAC</th>
        <th>Sender MAC</th>
        <th>Channel Number</th>
        <th>RSSI</th>
        <th>Date and Time</th>

    <?php        
        if ( isset( $_SESSION['username'] ) ) {
            
            $tableName = "usr__".$_SESSION['username']."_data";
            include '../config.php';
    
            $con = mysqli_connect($host, $db_username, $db_password, $db_name);
            /* check connection */
            if (mysqli_connect_errno()) {
                printf("Connect failed: %s\n", mysqli_connect_error());
                exit();
            }
            $combined = "SELECT * FROM " . $tableName;
            $statement = mysqli_prepare($con, $combined );
            mysqli_stmt_execute($statement);
            mysqli_stmt_bind_result($statement, $receiverMAC, $senderMAC, $channelNum, $RSSI, $dateTIme);
            
            while (mysqli_stmt_fetch($statement) == true){
                    echo "<tr>";
                    echo "<td>"."$receiverMAC"."</td>";
                    echo "<td>"."$senderMAC"."</td>";
                    echo "<td>"."$channelNum"."</td>";
                    echo "<td>"."$RSSI"."</td>";
                    echo "<td>"."$dateTIme"."</td>";
                    echo "</tr>";
            }                 
        } else {
            session_destroy();
            header("Location: login.html");
            exit();
        }        
    ?>    
    </table>
      
      

        

  </body>
</html>
