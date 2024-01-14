<?php
include_once '../lib/common_report.php';
include_once '../lib/queryfunction.php';

if (!isset($_SESSION['logged'])) {
    echo "Please log in first. Redirect to login page";
    header("Refresh: 3; login.php");
    exit();
}

$ID_NAME = "customer_id";
$DRILL_DOWN_LINK = "report_drilldown_customer_sale_repair.php?customer_id="
?>
<html lang="en">

<?php
include_once '../lib/header.php';
$page_name = 'Gross Customer Income';
echo "<title>$page_name Report Page</title>"
?>
</head>


<body>
    <?php
    include_once '../lib/sub_header.php';
    ?>
    <div>
        <table class="report_table">
            <?php
            $conn = mysql_conn_init_6400();
            $result = query_top15_customer_gross_income($conn);
            $is_header_printed = false;
            while ($row = $result->fetch_assoc()) {
                // skip customer_id for culumn, use in url
                echo "<tr>";
                if (!$is_header_printed) {
                    foreach($row as $key => $value) {
                        if ($key != $ID_NAME) {
                            echo "<th>$key</th>";
                        }
                    }
                    $is_header_printed = true;
                    echo "</tr>";
                    echo "<tr>";
                }
                $customer_id = 0;
                foreach ($row as $key => $value) {
                    if ($key == $ID_NAME) {
                        $customer_id = $value;
                    } else {
                        if ($key == 'customername') {
                            echo "<td><a href=$DRILL_DOWN_LINK$customer_id>$value</a></td>";
                        } else {
                            echo "<td>$value</td>";
                        }
                    }
                    
                }
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</body>

</html>