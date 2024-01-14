<?php
    include 'lib/common.php';
    include 'lib/queryfunction.php';
    include 'lib/common_report.php';
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
    table, th, td {
        border:1px solid black;
    }
    table {
        text-align: center;
        margin-left: auto; 
        margin-right: auto;
    }
    .headin2 {
        text-align: center;
    }
    .Select_Filter {
        margin-left: auto;
    }
    .back {
        margin-left: 55;
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
    <div class="headin2"><strong> <h3>Search Result </h3></strong></div>
    <form action = 'DetailPage.php' method="GET">
        <?php
            
            //$carTypes = array("Car","Convertible","Truck","Van","Suv");
            $manua_Name_Get = $_GET['Selected_Manuafacturer_Name'];
            $manua_Name = empty($manua_Name_Get) ? null : " AND v.manufacturer_name = '{$manua_Name_Get}'";
            $vehicle_Type = (isset($_GET['carType'])) ? $_GET['carType'] : false; 
            $model_Year_Get = $_GET['Selected_Model_Year'];
            $model_Year = empty($model_Year_Get) ? null : " AND v.model_year = {$model_Year_Get}";
            $model_Name_Get = $_GET['Selected_Model_Name'];
            $model_Name = empty($model_Name_Get) ? null : " AND v.model_name = '{$model_Name_Get}'";
            $color_Get = $_GET['Selected_Vehicle_Color'];
            $color = empty($color_Get) ? null : " AND vc.combinedColor LIKE '%{$color_Get}%'"; 
            $Operator = (isset($_GET['compare'])) ? $_GET['compare'] : false;
            $price = (isset($_GET['price'])) ? $_GET['price'] : false; 
            $keyWord_get = (isset($_GET['keyWord'])) ? $_GET['keyWord'] : false;
            $keyWord = empty($keyWord_get) ? null : $keyWord_get;
            $vin_get = (isset($_GET['vin'])) ? $_GET['vin'] : false;
            $vin = empty($vin_get) ? null : " AND v.vin = '{$vin_get}'";
            //Select_Filter=Show+Sold+Vehicles
            $filter_get = (isset($_GET['Select_Filter'])) ? $_GET['Select_Filter'] : false;
            $filter = " AND s.sale_date IS NULL";
            if ($filter_get == "Show Sold Vehicles"){
                $filter = " AND s.sale_date IS NOT NULL"; 
            } else if ($filter_get == "Show Unsold Vehicles") {
                $filter = " AND s.sale_date IS NULL";
            } else if ($filter_get == "Show All Vehicles"){
                $filter = null;
            }
            $conn = mysql_conn_init_6400();
            $result = get_search_result($filter, $color, $manua_Name, $model_Year, $model_Name, $vin, $vehicle_Type, $keyWord, $conn);
            $result_check = mysqli_num_rows($result);
            if($result_check == 0) {
                echo "No Vehicle Found";
            } else {
                echo "<table>";
                    echo "<table width=\"80%\">";
                    echo "<tr>";
                    echo "<th id=\"td1\">vin</th><th id=\"td2\">vehicle_type</th><th id=\"td3\">model_year</th>
                                <th id=\"td4\">manufacturer_name</th><th id=\"td5\">model_name</th><th id=\"td6\">color</th>
                                <th id=\"td7\">list_price</th><th id=\"td8\">show detail</th>";
                    echo "</tr>";
                //check if search button has been clicked, ['button的name']
                if (isset($_GET['Search'])) {
                    while ($row = mysqli_fetch_assoc($result)){
                        if($Operator === "greater than" && $row['invoice_price'] * 1.25 > $price || $Operator === "equal" && $row['invoice_price'] * 1.25 == $price || $Operator === "smaller than" && $row['invoice_price'] * 1.25 < $price) {
                            $vin = $row['vin'];
                            echo "<tr>";
                            echo "<td>".$row['vin']."</td><td>".$vehicle_Type."</td><td>".$row['model_year']."</td>
                                    <td>".$row['manufacturer_name']."</td><td>".$row['model_name']."</td><td>".$row['combinedColor']."</td><td>".$row['invoice_price'] * 1.25."</td><td>
                                    "."<input type=\"submit\" method = \"post\" name=\"detail\" placeholder=\"detail\" value=\"$vin\"/>"."<input type=\"hidden\" method = \"post\" name=\"type\" placeholder=\"type\" value=\"$vehicle_Type\"/>"."</td>";
                            echo "</tr>";
                        }
                        
                    }
                }
                echo "</table>";
                
            }
        ?>
    </form>
    <br>
        <a href="SearchPage.php"> BACK TO SEARCH PAGE </a>
    </main>
</section>

</body>
</html>
