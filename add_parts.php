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
    <h1>Please enter parts details</h1> 
    <form action ='includes/addparts.php' method='POST'>
        Vin: <input type ='text' name='vin' value="<?php echo $_SESSION['vin']; ?>" disabled>
        Date: <input type ='date' name='startdate' value="<?php echo date('Y-m-d');?>" disabled>
        VendorName: <input type ='text' name='vendorname' placeholder="vendor_name">
        PartNumber: <input type ='number'  name='partnumber' placeholder="part_number">
        Quantity: <input type ='number' name='quantity' placeholder="quantity">
        Price: <input type ='number' step="0.01" name='price' placeholder="price">
        <br>
        
        <button type='submit' name='add'>Add</button><br>
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