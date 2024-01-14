<?php
    //include 'dpConnect.php';
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
    form {
        margin-left: 50; 
    }
</style>
<main>
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
            <form action = 'SearchResult.php' method="GET">
                <label for="carType">Please Select Car Type:</label><br>
                <select name = "carType">
                    <option>Car</option>
                    <option>Convertible</option>
                    <option>Truck</option>
                    <option>Van</option>
                    <option>Suv</option>
                </select>
                <br>
                <br>
                <?php
                    if(isset($_SESSION['logged'])){
                        echo '<label for="vin_num">Please Insert VIN:</label><br>';
                        echo '<input type = "text" name = "vin" placeholder = "Insert VIN">';
                        echo '<br>';
                        echo '<br>';
                    }
                ?>
                
                <label for="manua">Please Select Manuafacturer Name:</label><br>
                <?php
                    //$sql = "SELECT DISTINCT manufacturer_name FROM Vehicle;";
                    $conn = mysql_conn_init_6400();
                    $result = get_distinct_manu_name($conn);
                    //$resultCheck = mysqli_num_rows($result);
                    echo "<select name = 'Selected_Manuafacturer_Name'>";
                    echo '<option></option>';
                    while ($row = mysqli_fetch_array($result)){
                        echo '<option value = "'.$row['manufacturer_name'].'">'.$row['manufacturer_name'].'</option>';
                    }
                    echo '</select>';
                ?>
                <!--<input type = "text" name = "manua" placeholder = "Insert Manuafacturer Name">-->
                <br>
                <br>
                <label for="modelYear">Please Select Model Year:</label><br>
                <?php
                    // $sql = "SELECT DISTINCT model_year FROM Vehicle
                    // ORDER BY model_year;";
                    $conn = mysql_conn_init_6400();
                    $result =  get_distinct_model_year($conn);
                    //$resultCheck = mysqli_num_rows($result);
                    echo "<select name = 'Selected_Model_Year'>";
                    echo '<option></option>';
                    while ($row = mysqli_fetch_array($result)){
                        echo '<option value = "'.$row['model_year'].'">'.$row['model_year'].'</option>';
                    }
                    echo '</select>';
                ?>
                <br>
                <br>
                <label for="modelName">Please Select Model Name:</label><br>
                <?php
                    // $sql = "SELECT DISTINCT model_name FROM Vehicle
                    // ORDER BY model_name;";
                    $conn = mysql_conn_init_6400();
                    $result = get_distinct_model_name($conn);
                    //$resultCheck = mysqli_num_rows($result);
                    echo "<select name = 'Selected_Model_Name'>";
                    echo '<option></option>';
                    while ($row = mysqli_fetch_array($result)){
                        echo '<option value = "'.$row['model_name'].'">'.$row['model_name'].'</option>';
                    }
                    echo '</select>';
                ?>
                <br>
                <br>
                <label for="vehicleColor">Please Select Color:</label><br>
                <?php
                    // $sql = "SELECT DISTINCT color FROM VehicleColor
                    // ORDER BY color;";
                    $conn = mysql_conn_init_6400();
                    $result = get_distinct_color($conn);
                    //$resultCheck = mysqli_num_rows($result);
                    echo "<select name = 'Selected_Vehicle_Color'>";
                    echo '<option></option>';
                    while ($row = mysqli_fetch_array($result)){
                        echo '<option value = "'.$row['color'].'">'.$row['color'].'</option>';
                    }
                    echo '</select>';
                ?>
                <br>
                <br>
                <label for="price">Please Input Desired Price:</label><br>
                <select name = "compare">
                    <option>greater than</option>
                    <option>equal</option>
                    <option>smaller than</option>
                </select>
                <input type = "text" name = "price" placeholder = "Insert Price"> 
                <br>
                <br>
                <label for="keyWord">Please Insert Keyword:</label><br>
                <input type = "text" name = "keyWord" placeholder = "Insert KeyWord">
                <br>
                <br>
                <?php
                    if (isset($_SESSION['logged'])) {
                        $str_usertype =  implode($_SESSION["logged"]["usertype"]);
                        if(strpos($str_usertype, "owner") !== false || strpos($str_usertype, "manager") !== false){
                            echo '<label for="filter">Please Select Filter:</label><br>';
                            echo "<select name = 'Select_Filter'>";
                            echo '<option>Show Sold Vehicles</option>';
                            echo '<option>Show Unsold Vehicles</option>';
                            echo '<option>Show All Vehicles</option>';
                            echo '</select>';
                        }           
                    }
                ?>
                <br>
                <br>
                <button type = 'submit' name = 'Search'>SEARCH</button>
            </form>
        </section>
    </main>
</body>
</html>
