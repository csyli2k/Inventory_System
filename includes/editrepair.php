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




      $vin= $_SESSION['vin'];
      $edit_labor_charge= $_POST['edit_labor_charge'];
      $start_date=$_SESSION['start_date'];
      $result= edit_repair_query_y($conn,$vin,$start_date, $edit_labor_charge);

      if ($result) {
        echo "Record updated successfully";
        echo '<p><<a href="../repair_search.php" title="To Edit Repair page">Go Back To Repair Search</a></p>';
      } else {
        echo "Error updating record: " . mysqli_error($conn);
        echo '<p><a href="../repair_search.php" title="To Edit Repair page">Go Back To Repair Search</a></p>';
      }
  ?>
</main>



    
</section>
</body>
</html>