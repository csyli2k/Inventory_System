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
    <h1>Please enter vin to search for repair</h1>
    <form action ='repair_search_result.php' method='POST'>
        Vin: <input type ='text' name='vin' placeholder="vin">
        <br>
        <button type='submit' name='search'>Search</button>
    </form>
</main>



    
</section>
</body>
</html>