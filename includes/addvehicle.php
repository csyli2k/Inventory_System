<?php
include '../lib/common.php';
include '../lib/queryfunction.php';
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
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@1,300&display=swap" rel="stylesheet">
</head>
<body>
    <section class = "sub_header">
        <nav>
            <a href="Index.php"><img src="../images/logo2.png"></a>
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

<main>

<?php



//add vechiles
      $vin= $_GET['vin'];
      $model_name= $_GET['model_name'];
      $model_year= $_GET['model_year'];
      $invoice_price= $_GET['invoice_price'];
      $description= $_GET['description'];
      $manufacturer_name=$_GET['input_manufacturer_name'];
      $username= $_GET['username'];

      if (isset($_GET['select_color'])){
        $color_selection=$_GET['select_color'];
      }




      
      
      $number_of_doors=$_GET['number_of_doors'];
      $back_seat_count=$_GET['back_seat_count'];
      $roof_type=$_GET['roof_type'];
      $cargo_cover_type=$_GET['cargo_cover_type'];
      $cargo_capacity=$_GET['cargo_capacity'];
      $number_of_rear_axies=$_GET['number_of_rear_axies'];
      $has_drive_side_door=0;
      if (isset($_GET['has_drive_side_door'])) {
        $has_drive_side_door=TRUE;
        }


      $drivetrain_type=$_GET['drivetrain_type'];

      $num_of_cupholders=$_GET['num_of_cupholders'];





      //Update manufacturer
      if (isset($_GET['add_manufacturer_button']))
      {
        $add_manufacturer_result= add_manufacturer_query_y($conn,$manufacturer_name);
                if ($add_manufacturer_result) {
                        echo "Record updated successfully";        
                      } else {
                        echo "Error manufacturer updating record: " . mysqli_error($conn);

                      } 


      }


//add vehicle details
     if (isset($_GET['add_vehicle_button']))
      {         
            $add_vehicle_result =add_vehicle_query_y($conn,$vin,$model_name, $model_year, $invoice_price,$description, $manufacturer_name, $username);
            if ($add_vehicle_result) {
                        echo "Vehicle Record updated successfully<br>";        

                        foreach ($color_selection as $color){
                            $add_color_result= add_color_query_y($conn,$vin,$color);
                            if ($add_color_result) {
                                echo "Color Record updated successfully<br>";        

                              } else {
                                echo "Error updating color record: " . mysqli_error($conn);

                              }   
                        }
                 
                        if (isset($_GET['vehicle_type']) && $_GET['vehicle_type']=="Car")
                            {
                            $add_car_result= add_car_query_y($conn,$vin,$number_of_doors);   
                            if ($add_car_result) {
                                    echo "Record updated successfully<br>";        

                                  } else {
                                    echo "Error updating record: " . mysqli_error($conn);

                                  } 
                            }
                        if (isset($_GET['vehicle_type']) && $_GET['vehicle_type']=="Convertible")
                            {   
                            $add_convertible_result= add_convertible_query_y($conn,$vin,$back_seat_count,$roof_type);
                            if ($add_convertible_result) {
                                    echo "Record updated successfully<br>";        

                                  } else {
                                    echo "Error updating record: " . mysqli_error($conn);

                                  } 
                            }
                        if (isset($_GET['vehicle_type']) && $_GET['vehicle_type']=="Truck")
                            {
                            $add_truck_result= add_truck_query_y($conn,$vin,$cargo_cover_type,$cargo_capacity,$number_of_rear_axies);
                                if ($add_truck_result) {
                                        echo "Record updated successfully<br>";        
         
                                      } else {
                                        echo "Error updating record: " . mysqli_error($conn);

                                      } 
                            }

                        if (isset($_GET['vehicle_type']) && $_GET['vehicle_type']=="Van")
                            {
                            $add_van_result= add_van_query_y($conn,$vin,$has_drive_side_door);
                                if ($add_van_result) {
                                        echo "Record updated successfully<br>";        

                                      } else {
                                        echo "Error updating record: " . mysqli_error($conn);
         
                                      } 
                            }
                        if (isset($_GET['vehicle_type']) && $_GET['vehicle_type']=="Suv")
                            {
                            $add_suv_result= add_suv_query_y($conn,$vin,$drivetrain_type,$num_of_cupholders);
                                if ($add_suv_result) {
                                        echo "Record updated successfully<br>";        

                                      } else {
                                        echo "Error updating record: " . mysqli_error($conn);

                                      } 
                            }
                } else {
                    echo "Error updating Vehicle record: " . mysqli_error($conn);

                      }                             

      }


       echo '<p><a href="../add_vehicle.php" title="To Add vehicle page">Go Back</a></p>';
      




  ?>
</main>



    

</body>
</html>
