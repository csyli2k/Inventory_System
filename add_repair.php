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
    <h1>Please enter repair details</h1>    

    <form action ='includes/addrepair.php' method='POST'>
        Vin: <input type ='text' name='vin' value="<?php echo $_SESSION['vin']; ?>" disabled>
        Start Date(Today): <input type ='date' name='startdate' value="<?php echo date('Y-m-d');?>" disabled>
        Labor_Charge: <input type ='number' name='labor_charge' placeholder="laborcharge">
        Description: <input type ='text'  name='description' placeholder="description">
        OD_Reading: <input type ='number' name='odometer_reading' placeholder="odometer_reading">
        UserName: <input type ='text' name='username' value="<?php echo ($_SESSION['logged']['username']); ?>">
        <br>
        <?php
        if(isset($_SESSION['customer_identifier'])){
                $search_id_c=$_SESSION['customer_identifier'];
                $individual_result_c=search_individual_customer_y($conn,$search_id_c);
                $business_result_c=search_business_customer_y($conn,$search_id_c);
                echo 'Customer Info:<br>';
                if ($num_search_reuslt_i_c=mysqli_num_rows($individual_result_c)>0 ){
                      while ($row_individual_c=mysqli_fetch_array($individual_result_c)){
                        echo ('Driver_License:'.$row_individual_c['driver_license'].'<br>');
                        echo ('first_name:'.$row_individual_c['first_name'].'<br>');
                        echo ('last_name:'.$row_individual_c['last_name'].'<br>');

                      }
                }
                if ($num_search_reuslt_b_c=mysqli_num_rows($business_result_c)>0 ){
                      while ($row_business_c=mysqli_fetch_array($business_result_c)){
                        echo ('tax_id_number:'.$row_business_c['tax_id_number'].'<br>');
                        echo ('business_name:'.$row_business_c['business_name'].'<br>');
 
                      }
                }
            }
        else{
                $search_vin=$_SESSION['vin'];
                $individual_result=search_individual_y($conn,$search_vin);
                $business_result=search_business_y($conn,$search_vin);
                echo 'Customer Info:<br>';
                if ($num_search_reuslt_i=mysqli_num_rows($individual_result)>0 ){
                      while ($row_individual=mysqli_fetch_array($individual_result)){
                        echo ('Driver_License:'.$row_individual['driver_license'].'<br>');
                        echo ('first_name:'.$row_individual['first_name'].'<br>');
                        echo ('last_name:'.$row_individual['last_name'].'<br>');

                      }
                }
                if ($num_search_reuslt_b=mysqli_num_rows($business_result)>0 ){
                      while ($row_business=mysqli_fetch_array($business_result)){
                        echo ('tax_id_number:'.$row_business['tax_id_number'].'<br>');
                        echo ('business_name:'.$row_business['business_name'].'<br>');

                      }
                }
        }

        ?>

        CustomerId: <input type ='number' name='customerID' value="<?php 
        if(isset($_SESSION['customer_identifier'])){
            echo $_SESSION['customer_identifier'];
        }else{
            $vin=$_SESSION['vin'];
            $temp_customer_id= search_customer_y($conn,$vin);
            $row_customer=mysqli_fetch_array($temp_customer_id);
            echo $row_customer['customerID'];
        }
        ?>"
        placeholder='customerID'>




        <p><a href="customer.php" title="To Add A Different Customer">To Change A Different Customer</a></p>



        

        <div class="container">
            <div class="center">
                <button type='submit' name='add'>Submit</button><br>
            </div>
        </div>

       
    </form>
     <div class="container">
            <div class="center">
            <button onclick="location.href='repair_search.php'">Go Back To Repair Search</button>
            </div>
        </div>

</main>

    
</section>
</body>
</html>