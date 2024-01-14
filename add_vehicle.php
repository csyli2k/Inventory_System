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
<main>
    <h1>Please enter vehicle details</h1>
    <form action ='includes/addvehicle.php' method='GET'>

        Vin: <input type ='text' name='vin' placeholder="vin">
        ModelName: <input type ='text' name='model_name' placeholder="model_name">
        ModelYear: <input type ='number' name='model_year' placeholder="model_year" min=1900 max=2022>
        Invoice_Price: <input type ='number' step="0.01" name='invoice_price' placeholder="invoice_price">
        <br>
        Description: <input type ='text' name='description' placeholder="description">
        UserName: <input type ='text' name='username' value=<?php echo ($_SESSION['logged']["username"]); ?> >
        <br>
        Manufacturer: 
        <input list='manufacturer' name=input_manufacturer_name>
                <datalist id='manufacturer'>
                <?php
                    if ($conn===false){
                    die("Error" . mysqli_connect_error());
                }
                
                $result= select_manufacturer_query_y($conn);
                while ($row= mysqli_fetch_array($result)){
                    $db_manufacturer=$row['manufacturer_name'];
                    echo "<option value='$db_manufacturer'> $db_manufacturer</option> ";
                }
                ?>
                </datalist>
                <button type='submit' name='add_manufacturer_button'>Add Manufacturer</button>(Click if the manufacturer is not in the list)


        <br>
        VehicleColor: <br>


        <select name="select_color[]" multiple>
            <?php
                if ($conn===false){
                die("Error" . mysqli_connect_error());
            }
            $color_result= select_color_query_y($conn);
            while ($row= mysqli_fetch_array($color_result)){
                $dbselected=$row['color'];
                echo "<option value='$dbselected'> $dbselected</option> ";
            }
            ?>
        </select>


            
        <br>




   
        <script type="text/javascript">
        function vehicle_type_check() {
            if (document.getElementById('Car').checked) {
                document.getElementById('vehicle_type1').style.display = 'block';
            document.getElementById('vehicle_type2').style.display = 'none';
            document.getElementById('vehicle_type3').style.display = 'none';
            document.getElementById('vehicle_type4').style.display = 'none';
            document.getElementById('vehicle_type5').style.display = 'none';
            } 
            else if(document.getElementById('Convertible').checked) {
                document.getElementById('vehicle_type2').style.display = 'block';
            document.getElementById('vehicle_type1').style.display = 'none';
            document.getElementById('vehicle_type3').style.display = 'none';
            document.getElementById('vehicle_type4').style.display = 'none';
            document.getElementById('vehicle_type5').style.display = 'none';
            }
            else if(document.getElementById('Truck').checked) {
                document.getElementById('vehicle_type3').style.display = 'block';
            document.getElementById('vehicle_type1').style.display = 'none';
            document.getElementById('vehicle_type2').style.display = 'none';
            document.getElementById('vehicle_type4').style.display = 'none';
            document.getElementById('vehicle_type5').style.display = 'none';
            }
            else if(document.getElementById('Van').checked) {
                document.getElementById('vehicle_type4').style.display = 'block';
            document.getElementById('vehicle_type1').style.display = 'none';
            document.getElementById('vehicle_type2').style.display = 'none';
            document.getElementById('vehicle_type3').style.display = 'none';
            document.getElementById('vehicle_type5').style.display = 'none';
            }
            else if(document.getElementById('Suv').checked) {
                document.getElementById('vehicle_type5').style.display = 'block';
            document.getElementById('vehicle_type1').style.display = 'none';
            document.getElementById('vehicle_type2').style.display = 'none';
            document.getElementById('vehicle_type3').style.display = 'none';
            document.getElementById('vehicle_type4').style.display = 'none';
            }
        }
        </script> 

                    <div class="vehicle_type_radio">Vehicle Type: 

                    <br> 

                    <input type="radio" onclick="javascript:vehicle_type_check();" name="vehicle_type" value="Car" id ="Car"/> Car<br> 

                    <input type="radio" onclick="javascript:vehicle_type_check();" name="vehicle_type" value="Convertible" id = "Convertible"/> Convertible<br>

                    <input type="radio" onclick="javascript:vehicle_type_check();" name="vehicle_type" value="Truck" id = "Truck"/> Truck<br>  

                    <input type="radio" onclick="javascript:vehicle_type_check();" name="vehicle_type" value="Van" id = "Van"/> Van<br>    

                    <input type="radio" onclick="javascript:vehicle_type_check();" name="vehicle_type" value="Suv" id = "Suv"/> Suv<br>   

                    

                    </div>
                    
                    <!-- for car  -->
                    <div id="vehicle_type1" style="display:none">
                    <div class="vehicle_type_text" ><input type="number" class="vehicle_type_input" name="number_of_doors" placeholder="number of doors"></div>

                    </div>

                    <!-- for Convertible  -->
                    <div id="vehicle_type2" style="display:none">
                    <div class="vehicle_type_text" ><input type="number" class="vehicle_type_input" name="back_seat_count" placeholder="back_seat_count"></div>
                    <div class="vehicle_type_text" ><input type="text" class="vehicle_type_input" name="roof_type" placeholder="roof_type"></div>
                    </div>

                    <!-- for Truck  -->
                    <div id="vehicle_type3" style="display:none">
                    <div class="vehicle_type_text" ><input type="text" class="vehicle_type_input" name="cargo_cover_type" placeholder="cargo_cover_type"></div>
                    <div class="vehicle_type_text" ><input type="number" class="vehicle_type_input" name="cargo_capacity" placeholder="cargo_capacity"></div>
                    <div class="vehicle_type_text" ><input type="number" class="vehicle_type_input" name="number_of_rear_axies" placeholder="number_of_rear_axies"></div>
                    </div>

                    <!-- for van -->  
                    <div id="vehicle_type4" style="display:none">
                    <div class="vehicle_type_text"><input type="checkbox" class="vehicle_type_input" name="has_drive_side_door" > 
                    <label for="has_drive_side_door">has_drive_side_door:</label>
                    </div>


                    </div>

                    <!--for Suv --> 
                    <div id="vehicle_type5" style="display:none">
                    <div class="vehicle_type_text" ><input type="text" class="vehicle_type_input" name="drivetrain_type" placeholder="drivetrain_type"></div>
                    <div class="vehicle_type_text" ><input type="number" class="vehicle_type_input" name="num_of_cupholders" placeholder="num_of_cupholders"></div> 
                    </div>



                <br>
    <button type='submit' name='add_vehicle_button'>Add Vehicle</button>

    </form> 



</main>


    
</section>
</body>
</html>
