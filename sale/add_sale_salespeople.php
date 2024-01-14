<?php
include_once '../lib/common_report.php';
include_once '../lib/queryfunction.php';

if (!isset($_SESSION['logged'])) {
    echo "Please log in first. Redirect to login page";
    header("Refresh: 3; login.php");
    exit();
}
?>
<html lang="en">

<?php
include_once '../lib/header.php';
$page_name = 'Add Sale';
echo "<title>$page_name Page</title>"
?>
</head>

<body>
    <div>
        <?php
        include_once '../lib/sub_header.php';
        $conn = mysql_conn_init_6400();
        $customer_id = mysqli_real_escape_string($conn, $_GET["customer_id"]);
        $purchase_date = mysqli_real_escape_string($conn, $_GET["purchase_date"]);
        $vin = mysqli_real_escape_string($conn, $_GET["vin"]);
        $sold_price = mysqli_real_escape_string($conn, $_GET["sold_price"]);
        $user_type = implode($_SESSION["logged"]["usertype"]);
        $str_usertype =  implode($_SESSION["logged"]["usertype"]);
        $user_type = strpos($str_usertype, "owner") ? "owner" : "manager";
        $username = mysqli_real_escape_string($conn, $_SESSION['logged']["username"]);

        add_sale_by_salespeople($conn, $customer_id, $username, $purchase_date, $vin, $sold_price, $user_type);
        ?>
    </div>
    <div>
        <a href="add_sale.php">Add One more</a>
    </div>
</body>
</html>