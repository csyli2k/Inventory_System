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
$page_name = 'Sale Year Month Drill Down';
echo "<title>$page_name Report Page</title>"
?>
</head>

<body>
    <?php
    include_once '../lib/sub_header.php';
    if (!isset($_GET["year_month"])) {
        echo "<h1> no year_month is set </h1>";
        die("no year_month set");
    }
    $year_month = $_GET['year_month'];
    ?>
    <div>
        <h2 class="report_text">Sale Year Month <?php echo $year_month ?> Drill Down</h2>
        <table class="report_table">
            <?php
            $conn = mysql_conn_init_6400();
            $result = query_report_drilldown_sale_year_month($conn, $year_month);
            if ($result->num_rows == 0) {
                echo "<h4> no records found</h4>";
            } else {
                $is_header_printed = false;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    if (!$is_header_printed) {
                        foreach ($row as $key => $value) {
                            echo "<th>$key</th>";
                        }
                        $is_header_printed = true;
                        echo "</tr>";
                        echo "<tr>";
                    }
                    foreach ($row as $key => $value) {
                        echo "<td>$value</td>";
                    }
                    echo "</tr>";
                }
            }
            ?>
        </table>
    </div>

</body>

</html>