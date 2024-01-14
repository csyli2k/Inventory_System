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
    <?php
    include_once '../lib/sub_header_sale.php';
    ?>
    <div class="center">
        <?php
        include_once "salespeople_sale.php";
        ?>
    </div>
</body>

</html>