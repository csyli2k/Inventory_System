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
    <title>Jaunty Jalopies Add Customer</title>
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
    
    <script type="text/javascript">
    function customertype_check() {
        if (document.getElementById('Individual').checked) {
            document.getElementById('customertype1').style.display = 'block';
        document.getElementById('customertype2').style.display = 'none';
        } 
        else if(document.getElementById('Business').checked) {
            document.getElementById('customertype2').style.display = 'block';
        document.getElementById('customertype1').style.display = 'none';
        }
    }
    </script> 

    <section class="customer_section">
        <div class="customer_add_outerbox_display">
            <div class="customer_innerbox_display">
                
                <!-- CUSTOMER ADD FORM -->
                <form class="customer" action="includes/customer_include.php" method = "post">

                    <div class="customer_text" ><input type="text" class="customer_input" name="email_address" placeholder="Enter email address (Optional)"></div>
                    <div class="customer_text" ><input type="text" class="customer_input" name="phone_number" placeholder="Enter phone number"></div>
                    <div class="customer_text" ><input type="text" class="customer_input" name="street_address" placeholder="Enter street address"></div>
                    <div class="customer_text" ><input type="text" class="customer_input" name="city" placeholder="Enter city "></div>
                    <div class="customer_text" ><input type="text" class="customer_input" name="state" placeholder="Enter state "></div>
                    <div class="customer_text" ><input type="text" class="customer_input" name="postal_code" placeholder="Enter postal code"></div>

                    <div class="customer_radio">Customer Type: <br> 
                    <input type="radio" onclick="javascript:customertype_check();"  name="customer_type" value="Individual" id ="Individual" /> Individual<br> 
                    <input type="radio" onclick="javascript:customertype_check();" name="customer_type" value="Business" id = "Business"/> Business<br>                     
                    </div>
                    
                    <!-- for individual  -->
                    <div id="customertype1" style="display:none">
                    <div class="customer_text" ><input type="text" class="customer_input" name="driver_license" placeholder="Enter driver license"></div>
                    <div class="customer_text" ><input type="text" class="customer_input" name="first_name" placeholder="Enter customer first name"></div>
                    <div class="customer_text" ><input type="text" class="customer_input" name="last_name" placeholder="Enter customer last name"></div>
                    </div>

                    <!-- for business  -->
                    <div id="customertype2" style="display:none">
                    <div class="customer_text" ><input type="text" class="customer_input" name="tax_identification_id" placeholder="Enter tax identification id"></div>
                    <div class="customer_text" ><input type="text" class="customer_input" name="business_name" placeholder="Enter business name"></div>
                    <div class="customer_text" ><input type="text" class="customer_input" name="primary_contact_title" placeholder="Enter primary contact title"></div>
                    <div class="customer_text" ><input type="text" class="customer_input" name="primary_contact_first_name" placeholder="Enter primary contact first name"></div>
                    <div class="customer_text" ><input type="text" class="customer_input" name="primary_contact_last_name" placeholder="Enter primary contact last name"></div>
                    </div>


                    <button class="customer_submit" type = "submit" name="customer_add_submit"><span class="button_text">Add CUSTOMER</span></button>

                </form>        

                <!-- CUSTOMER ADD RESULT CHECK -->
                <div class="customer_result_check">
                <?php
                $fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI] <br>";
                if (strpos($fullUrl, "csadd=empty") == true) {
                    echo "<p>You did not fill in all fields!</p>";
                    echo "<p>Please try again!</p>";
                    exit();
                } elseif (strpos($fullUrl, "csadd=success") == true) {
                    echo "<P>Customer Information is added!</p>";
                    echo "<p>Back to the Customer Lookup Page</p>";
                    echo '<button class="customer_to_link" onclick="location.href=\'customer.php\'" id="topreviouspage">Back to Customer Lookup Page</button>';
                    exit();
                } elseif(strpos($fullUrl, "csadd=failed") == true){
                    echo "<p>Fail to add the customer Information!</p>";
                    echo "<p>Please check your input, especially Driver license/TIN</p>";
                    exit();
                }else{}
                ?>
                <div>
                <div>
            </div>
        </div>
    </section>