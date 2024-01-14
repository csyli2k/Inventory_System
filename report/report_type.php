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
$page_name = 'Type';
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
            <tr>
                <th>Type</th>
                <th>30 days count</th>
                <th>1 year count</th>
                <th>All Time Count</th>
                
                
            </tr>
            <?php
            $conn = mysql_conn_init_6400();
            $result = query_type_result($conn);

            while ($row = $result->fetch_assoc()) {
                // to print all columns automatically:
                echo "<tr>";
                foreach ($row as $value) {
                    if (!$value) {
                        $value = 0;
                    }
                    echo "<td>$value</td>";
                }
                echo "</tr>";
            }
            ?>
        </table>
    </div>
    
</body>

</html>