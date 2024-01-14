<?php
    include 'lib/common.php';
    include 'lib/queryfunction.php';
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jaunty Jalopies</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@1,300&display=swap" rel="stylesheet">
</head>
<body>
    <section class = "header">
    <nav>
    <a href="Index.php"><img src="images/logo2.png"></a>
    <div class = "nav_link">
        <ul>
            <li><a href="Index.php"> HOME </a></li>
            <?php
                if (!isset($_SESSION['logged'])) {
                    echo "<li><a href='login.php'> LOG IN </a></li>";
                } else {
                    $user_store = strtoupper($_SESSION["logged"]["username"]);
                    //echo $user_store;
                    $role_store = strtoupper(implode(" ,", $_SESSION["logged"]["usertype"]));
                    echo '<li><a> USER - ' .$user_store.' </a></li>';
                    echo '<li><a> ROLE - ' .$role_store.' </a></li>';
                    echo '<li><a href="logout.php"> LOGOUT </a></li>';
                }
            ?>
            
        </ul>
    </div>
    </nav>

    <div class="car_number">
        <h1> The total number vaialble for purchase : </h1>
        <form action = 'SearchPage.php' method="POST">
            <?php
                $conn = mysql_conn_init_6400();
                $result = get_total($conn);
                //echo "<select name = 'Selected_Model_Year'>";
                echo "<h1>";
                while ($row = mysqli_fetch_array($result)){
                    echo $row['total'];
                };
                echo "</h1>";
            ?>
            <button type = 'submit' name = 'SearchPage'>SEARCH AVAILABLE VEHICLE</button>
            <br>
        </form>
        <?php
            if(isset($_SESSION['logged'])) {
                $str_usertype =  implode($_SESSION["logged"]["usertype"]);
                if(strpos($str_usertype, "inventoryclerk") !== false){
                    echo '<form action = "add_vehicle.php" method="POST">';
                    echo '<button type = "submit" name = "add_vehicle">ADD VEHICLE</button>';
                    echo '</form>';
                }
                if(strpos($str_usertype, "owner") !== false || strpos($str_usertype, "manager") !== false){
                    echo '<form action = "report_index.php" method="POST">';
                    echo '<button type = "submit" name = "report_index">VIEW REPORTS</button>';
                    echo '</form>';
                }
                if(strpos($str_usertype, "servicewriter") !== false){
                    echo '<form action = "repair_search.php" method="POST">';
                    echo '<button type = "submit" name = "repair_search">REPAIR</button>';
                    echo '<form action = "repair_search.php" method="POST">';
                }
            }
        ?>
        
    </div>
    <div class="search_table">
        <h1>  </h1>
    </div>

</section>
</body>
</html>
