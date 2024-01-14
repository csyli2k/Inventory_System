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

    <h1>Please update repair details</h1> 

    <form action ='includes/editrepair.php' method='POST'>
        Vin: <input type ='text' name='vin' value="<?php echo $_SESSION['vin']; ?>" disabled>
        StartDate:<input type ='text' name='vin' value="<?php echo $_SESSION['start_date']; ?>" disabled>
        LaborCharge:
        <?php
        if(isset($_SESSION['logged'])) {
            $str_usertype =  implode($_SESSION["logged"]["usertype"]);
            if(strpos($str_usertype, "owner")){
                        echo "<input type='number' name= 'edit_labor_charge'>";
            }else{
                $l_vin=$_SESSION['vin'];
                $l_start_date=$_SESSION['start_date'];
                $old_labor_charge=search_labor_charge_y($conn,$l_vin, $l_start_date);
                $l_row=mysqli_fetch_array($old_labor_charge);
                $min_labor_charge= $l_row['labor_charge'];
                echo "<input type='number' name= 'edit_labor_charge' min='$min_labor_charge' placeholder='$min_labor_charge'>";
                echo "Labor charge has to be greater than the stored value!";
            }
        }
        ?>
        <br>

        <button type='submit' name='edit'>Update Repair</button>

    </form> 
    <br>



     Parts Information:
        <button onclick="location.href='./add_parts.php'">Add More Parts</button>
        <br>


    <?php
        $part_vin=$_SESSION['vin'];
        $part_start_date=$_SESSION['start_date'];

        $sql_parts=parts_search_result_query_y($conn,$part_vin,$part_start_date);
        if ($num_search_reuslt=mysqli_num_rows($sql_parts)>0){
              echo 'There is parts record for the vehicle<br>';
              while ($row=mysqli_fetch_array($sql_parts)){
                echo ('Vin:'.$row['vin'].'<br>');
                echo ('Start_date:'. $row['start_date'].'<br>');
                echo ('Vendor_Nmae:'. $row['vendor_name'].'<br>');
                echo ('Part_Number:'. $row['part_number'].'<br>');
                echo ('Quantity:'. $row['quantity'].'<br>');
                echo ('Pirce:'. $row['price'].'<br><br>');
              }
        }else{
            echo 'There is No Parts record.';
        }    

    ?>



        <br>
        <br>

            TO COMPLETE THE REPAIR, PLEASE CLICK:<br>
    <form action ='includes/completerepair.php' method='POST'>
            <button type='submit' name='complete_repair'>Complete Repair</button>
        <br>
    </form>
    Complete Date Will Be: <input type ='date' name='completedate' value="<?php echo date('Y-m-d');?>" disabled>   




     <div class="container">
            <div class="center">
            <button onclick="location.href='repair_search.php'">Go Back To Repair Search</button>
            </div>
        </div>
</main>

</section>
</body>
</html>
