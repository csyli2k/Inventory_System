<?php
include 'lib/common.php';

if (!isset($_SESSION['logged'])) {
    echo "Please log in first. Redirect to login page";
    header("Refresh: 3; login.php");
    exit();
}
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

    <section class="customer_section">
        <div class="customer_outerbox_display">
            <div class="customer_innerbox_display">
                <!-- CUSTOMER SEARCH FORM -->
                <form class="customer" action="includes/customer_include.php" method = "post">
                    <div class="customer_text"><input type="text" class="customer_input" name="customer_search_id" placeholder="Enter Customer Driver's license or TIN"></div>
                    <div class="customer_radio"><p>Search by</p><br>
                    <input type="radio" name="customeridtype" value="DL" /><label for="DL"> Driver's License</label><br><br>
                    <input type="radio" name="customeridtype" value="TIN" /><label for="TIN"> Tax Identification Number</label><br>
                    </div>
                    <button class="customer_submit" type = "submit" name="customer_search_submit"><span class="button_text">Submit Search Request</span></button>
                </form>

                <!-- CUSTOMER SEARCH RESULT CHECK -->
                <div class="customer_result_check">
                <?php
                $fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI] <br>";
                if (strpos($fullUrl, "csearch=empty") == true) {
                    echo "<p>You did not fill in all fields!</p>";
                    echo "<p>Please try again!</p>";
                    exit();
                } elseif (strpos($fullUrl, "csearch=exist") == true) {
                    echo "<P>Customer Exists!</p>";
                    $str_usertype =  implode($_SESSION["logged"]["usertype"]);
                    if(strpos($str_usertype, "salepeople") !== false || strpos($str_usertype, "owner") !== false){
                        echo "<P>For Salesperson or Owner</p>";
                        echo '<button class="customer_to_link" onclick="location.href=\'sale/add_sale.php\'" id="topreviouspage">Back to Sales Order Form</button>';
                    }
                    if(strpos($str_usertype, "servicewriter") !== false || strpos($str_usertype, "owner") !== false){
                        echo "<P>For Service Writer or Owner</p>";
                        echo '<button class="customer_to_link" onclick="location.href=\'add_repair.php\'" id="topreviouspage">Back to the Repair Form </button>';
                    }              
                    exit();
                } elseif(strpos($fullUrl, "csearch=notexist") == true){
                    echo "<p>Customer Doesn't exist!</p>";
                    echo '<p class="customer_to_link"><a href = customeradd.php>Add customer</a></p>';
                    exit();
                }else{}
                ?>
                <div>
                <div>
            </div>
        </div>
    </section>
