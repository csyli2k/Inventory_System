<?php
include_once 'lib/header_base.php';
include 'lib/common.php';
$page_name = 'Report Index';
echo "<title>$page_name Page</title>";

if (!isset($_SESSION['logged'])) {
    echo "Please log in first. Redirect to login page";
    header("Refresh: 3; login.php");
    exit();
}
?>
</head>

<body>
    <?php
    include_once 'lib/sub_header_base.php';
    
    ?>
    <div style="width:800px; margin:0 auto;">
        <br>
        <table class="report_table">
            <tr>
                <th>Generate Reports</th>
            </tr>
            <tr>
                <?php 
                // color report
                $report_path = "report/report_color.php";
                $url = $report_path;
                $name = "Color Report";
                echo "<td><a href='$url'>$name</a></td>";
                ?>
            </tr>
            <tr>
                <?php 
                // type report
                $report_path = "report/report_type.php";
                $url = $report_path;
                $name = "Type Report";
                echo "<td><a href='$url'>$name</a></td>";
                ?>
            </tr>
            <tr>
                <?php 
                // manufacturer report
                $report_path = "report/report_manufacturer.php";
                $url = $report_path;
                $name = "manufacturer Report";
                echo "<td><a href='$url'>$name</a></td>";
                ?>
            </tr>
            <tr>
                <?php 
                // gross customer income report
                $report_path = "report/report_gross_customer_income.php";
                $url = $report_path;
                $name = "Gross Customer Income Report";
                echo "<td><a href='$url'>$name</a></td>";
                ?>
            </tr>
            <tr>
                <?php 
                // Repairs by Manufacturer/Type/Model
                $report_path = "report/report_repairs_by_manufacturer_type_model.php";
                $url = $report_path;
                $name = "Repairs by Manufacturer/Type/Model Report";
                echo "<td><a href='$url'>$name</a></td>";
                ?>
            </tr>
            <tr>
                <?php 
                // Generate Below Cost Sales Report
                $report_path = "report/report_below_cost_sales.php";
                $url = $report_path;
                $name = "Below Cost Sales Report";
                echo "<td><a href='$url'>$name</a></td>";
                ?>
            </tr>
            <tr>
                <?php 
                // Generate Average Time In Inventory Report
                $report_path = "report/report_avg_time_in_inventory.php";
                $url = $report_path;
                $name = "Average Time In Inventory Report Report";
                echo "<td><a href='$url'>$name</a></td>";
                ?>
            </tr>
            <tr>
                <?php 
                // Generate Parts Statistics Report
                $report_path = "report/report_parts_statistics.php";
                $url = $report_path;
                $name = "Parts Statistics Report";
                echo "<td><a href='$url'>$name</a></td>";
                ?>
            </tr>
            <tr>
                <?php 
                // Generate Monthly Sales Report
                $report_path = "report/report_monthly_sales.php";
                $url = $report_path;
                $name = "Monthly Sales Report";
                echo "<td><a href='$url'>$name</a></td>";
                ?>
            </tr>
        </table>
    </div>
</body>
</html>
