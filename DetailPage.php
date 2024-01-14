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
<style>
    main {
        margin-left: 50; 
    }
</style>
<body>
    <section class = "sub_header">
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
    <main>
        <?php
            $vin = (isset($_GET['detail'])) ? $_GET['detail'] : false; 
            $vehicle_Type = (isset($_GET['type'])) ? $_GET['type'] : false;
            $vehicle_Specific_Variable;
            // $result = get_detail($vehicle_Type, $vin, $conn);
            // $row = mysqli_fetch_array($result);
            $conn = mysql_conn_init_6400();
            $result = get_detail($vehicle_Type, $vin, $conn);
            $row = mysqli_fetch_array($result);
            //while ($row = mysqli_fetch_array($result)){
                echo "vin is: ";
                echo $vin;
                echo "<br>";
                echo "vehicle type is: ";
                echo $vehicle_Type."<br>";
                if($vehicle_Type == 'Car') {
                    echo "number of doors is: ";
                    echo $row['number_of_doors']."<br>";
                }
                if($vehicle_Type == 'Convertible') {
                    echo "number of back seat count is: ";
                    echo $row['back_seat_count']."<br>";
                    echo "roof type is: ";
                    echo $row['roof_type']."<br>";
                }
                if($vehicle_Type == 'Truck') {
                    echo "cargo cover type is: ";
                    echo $row['cargo_cover_type']."<br>";
                    echo "cargo capacity is: ";
                    echo $row['cargo_capacity']."<br>";
                    echo "number of rear axies is: ";
                    echo $row['number_of_rear_axies']."<br>";
                }
                if($vehicle_Type == 'Van') {
                    echo "driver side door: ";
                    echo $row['has_drive_side_door']."<br>";
                }
                if($vehicle_Type == 'Suv') {
                    echo "drive train type is: ";
                    echo $row['drivetrain_type']."<br>";
                    echo "number of cupholders is: ";
                    echo $row['num_of_cupholders']."<br>";
                }
                echo "model year is: ";
                echo $row['model_year'];
                echo "<br>";
                echo "model name is: ";
                echo $row['model_name'];
                echo "<br>";
                echo "manufacturer name is: ";
                echo $row['manufacturer_name']."<br>";
                echo "color is: ";
                echo $row['combinedColor'];
                echo "<br>";
                echo "list price is: ";
                echo $row['invoice_price'] * 1.25;
                echo "<br>";
                echo "description is: ";
                echo $row['description'].'<br>';
                if(isset($_SESSION['logged'])) {
                    echo "invoice price is: ";
                    echo $row['invoice_price'].'<br>';
                    $str_usertype =  implode($_SESSION["logged"]["usertype"]);
                    //information shows only to sales people
                    if(strpos($str_usertype, "salepeople") !== false && $row['sale_date'] == null && $row['start_date'] == null) {
                        echo '<br>';
                        echo '<form action = "sale/add_sale.php" method="POST">';
                        echo '<button type = "submit" name = "sell">SELL VEHICLE</button>';
                        echo '<form>';
                        echo '<br>';
                    }
                    //information shows only to owner and manager
                    
                    if(strpos($str_usertype, "owner") !== false || strpos($str_usertype, "manager") !== false) {
                        echo "inventory clerk name is: ";
                        echo $row['username'].'<br>';
                        echo "inventory added date is: ";
                        echo $row['inventory_added_date'].'<br>';
                        // $result = get_detail($vehicle_Type, $vin, $conn);
                        // $row = mysqli_fetch_array($result);
                        if($row['sale_date'] == null && $row['start_date'] == null) {
                            // echo '<form action = "sale/add_sale.php" method="POST">';
                            // echo '<button type = "submit" name = "sell">SELL VEHICLE</button>';
                            // echo '<form>';
                        } else if ($row['sale_date'] != null && $row['start_date'] == null) {
                            
                            // echo "inventory clerk name is: ";
                            // echo $row['username'].'<br>';
                            echo "buyer's phone number is: ";
                            echo $row['phone_number'].'<br>';
                            echo "buyer's email address is: ";
                            echo $row['email_address'].'<br>';
                            echo "buyer's address is: ";
                            echo $row['street_address'];
                            echo "  ".$row['city'];
                            echo "  ".$row['state'];
                            echo "  ".$row['postal_code'].'<br>';
                            if ($row['business_name'] == null){
                                echo "buyer's first name is: ";
                                echo $row['first_name'].'<br>';
                                echo "buyer's last name is: ";
                                echo $row['last_name'].'<br>';
                            } else {
                                echo "buyer's business name is: ";
                                echo $row['business_name'].'<br>';
                                echo "buyer's primary contact title name is: ";
                                echo $row['primary_contact_title'].'<br>';
                                echo "buyer's primary contact firt name is: ";
                                echo $row['primary_contact_first_name'].'<br>';
                                echo "buyer's primary contact last name is: ";
                                echo $row['primary_contact_last_name'].'<br>';
                            }
                            echo "sale price is: ";
                            echo $row['sale_price'].'<br>';
                            echo "sale date is: ";
                            echo $row['sale_date'].'<br>';
                            echo "salesperson's name is: ";
                            echo $row['salesPersonName'].'<br>';
                        } else if ($row['start_date'] != null) {
                            // echo "inventory clerk name is: ";
                            // echo $row['username'].'<br>';
                            // echo "inventory added date is: ";
                            // echo $row['inventory_added_date'].'<br>';
                            echo "<br>";
                        
                            echo "--------REPAIR SECTION--------".'<br>';
                            
                            if ($row['business_name'] == null){
                                echo "customer's first name is: ";
                                echo $row['first_name'].'<br>';
                                echo "customer's last name is: ";
                                echo $row['last_name'].'<br>';
                            } else {
                                echo "customer's business name is: ";
                                echo $row['business_name'].'<br>';
                            }
                            echo '<br>';
                                echo "service writer's name is: ";
                                echo $row['inventoryName'].'<br>';
                                echo "repair's start date is: ";
                                echo $row['start_date'].'<br>';
                                echo "repair's complete date is: ";
                                echo $row['complete_date'].'<br>';
                                echo "labor charge is: ";
                                echo $row['labor_charge'].'<br>';
                                echo "parts price is: ";
                                //echo "test".$row['quantity'];
                            // echo $row['price'] * $row['quantity'].'<br>';
                                echo $row['partsTotal'].'<br>';
                                echo "total price is: ";
                                echo $row['partsTotal'] + $row['labor_charge'].'<br>';
                                echo '<br>';
                            while ($row = mysqli_fetch_array($result)){
                                echo '<br>';
                                echo "service writer's name is: ";
                                echo $row['inventoryName'].'<br>';
                                echo "repair's start date is: ";
                                echo $row['start_date'].'<br>';
                                echo "repair's complete date is: ";
                                echo $row['complete_date'].'<br>';
                                echo "labor charge is: ";
                                echo $row['labor_charge'].'<br>';
                                echo "parts price is: ";
                                //echo "test".$row['quantity'];
                            // echo $row['price'] * $row['quantity'].'<br>';
                                echo $row['partsTotal'].'<br>';
                                echo "total price is: ";
                                echo $row['partsTotal'] + $row['labor_charge'].'<br>';
                                echo '<br>';
                            }
                        } 
                    }
                }
            //}
            //}
        ?>
        <br>

        <a href="SearchPage.php"> BACK TO SEARCH PAGE </a>

    </main>
    </section>
</body>
</html>
