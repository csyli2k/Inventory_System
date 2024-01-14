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
$page_name = 'Parts Statistics';
echo "<title>$page_name Report Page</title>"
?>
</head>


<body>
    <?php
    include_once '../lib/sub_header.php';
    ?>
    <div>
        <br>
        <table class="report_table">
            <?php
            $conn = mysql_conn_init_6400();
            $result = query_report_parts_statistics($conn);
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
            ?>
        </table>
    </div>
</body>

</html>