<?php
include 'lib/common.php';
include 'lib/queryfunction.php';
if (!isset($_SESSION['logged'])) {
    echo "Please log in first. Redirect to login page";
    header("Refresh: 3; login.php");
    exit();
}
 $conn = mysql_conn_init_6400();
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jaunty Jalopies Customer Lookup</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@1,300&display=swap" rel="stylesheet">
</head>
<body>
    <section class = "sub_header">
        <nav>
            <a href="Index.php"><img src="images/logo2.png"></a>
            <div class = "nav_link">
                <ul>
                    <li><a href="Index.php"> HOME </a></li>
                    <li><a> USER - <?php echo strtoupper($_SESSION['logged']["username"]); ?> </a></li>
                    <li><a> ROLE - <?php echo strtoupper(implode(" ,", $_SESSION["logged"]["usertype"])); ?> </a></li>
                    <li><a href="logout.php"> LOGOUT </a></li>
                </ul>
            </div>
        </nav>
    </section>
<style>
.container { 
  height: 50px;
  position: relative;
  border: 3px white; 
}

.center {
  margin: 0;
  position: absolute;
  top: 50%;
  left: 50%;
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
}
</style>

<main>
 
<?php

  //search repair
        $_SESSION['vin']= $_POST['vin'];
        $vin=$_SESSION['vin'];

        $vin_search= vin_search_query_y($conn,$vin);

        $search_result= search_query_y($conn,$vin);


        $search_complete_date= complete_date_query_y($conn, $vin);



        if ($num_vin=mysqli_num_rows($vin_search)>0){
            //vin in the system, check if vin in repair
            if ($num_search_reuslt=mysqli_num_rows($search_result)>0){
              echo 'There is repair record for the vin<br>';
              while ($row=mysqli_fetch_array($search_result)){

                echo ('Vin:'.$row['vin'].'<br>');
                echo ('Model_Name:'. $row['model_name'].'<br>');
                echo ('Model_Year:'. $row['model_year'].'<br>');
                echo ('Manufacturer:'. $row['manufacturer_name'].'<br>');
                echo ('Color:'. $row['color'].'<br> <br>');
              }

                if ($num_search_complete_date=mysqli_num_rows($search_complete_date)>0){
                $row_complete_date=mysqli_fetch_array($search_complete_date);
                $_SESSION['start_date'] =($row_complete_date['start_date']);



                  echo 'There is repair in progress, edit it if needed';

                  echo '<p><a href="edit_repair.php" title="To Edit Repair page">Edit Exising Repair</a></p>';
                }else {
                  echo 'There is no repair in progress, start an new repair';
                  echo '<p><a href="add_repair.php" title="To add Repair page">Add New Repair</a></p>';
                }
              }
            else{
              //No repair record ADD repair
              echo 'No repair record for the vehicle';
              echo '<p><a href="add_repair.php" title="To Add repair  page">Add New Repair</a></p>';
            }
        // vin is not found in the system, return to main page
          }else{
            echo 'No such vehicle is sold in the system';
            echo '<p><a href="repair_search.php" title="Return to repair search page">Go back</a></p>';

            }

?>
          <div class="container">
          <div class="center">
            <button onclick="location.href='repair_search.php'">Go Back To Repair Search</button>
          </div>
        </div>
</main>




    
</section>
</body>
</html>

