<?php
include '../lib/common.php';

if (isset($_POST['customer_search_submit'])) {
    include_once '../lib/queryfunction.php';
    $conn = mysql_conn_init_6400();

    echo "Entered Driver License/VIN" . "<br>";
    echo $_POST['customer_search_id'] . "<br>";
    $customer_search_id = mysqli_real_escape_string($conn, $_POST['customer_search_id']);

    $by_individual = 0;
    $by_business = 0;
    if (isset($_POST['customeridtype']) && $_POST['customeridtype']=="DL")
    {
        echo "DL checked";
        $by_individual = 1;
    }

    if (isset($_POST['customeridtype']) && $_POST['customeridtype']=="TIN")
    {
        echo "TIN checked";
        $by_business = 1;
    }

    if (empty($customer_search_id) || ($by_individual == 0 && $by_business == 0)){
        header("Location: ../customer.php?csearch=empty");
    }else{
        if($by_individual == 1){
            $result =get_individual_search_query($conn,$customer_search_id);
        }else{
            $result =get_business_search_query($conn,$customer_search_id);
        }

        $result_check = mysqli_num_rows($result);
        if($result_check == 0){
            header("Location: ../customer.php?csearch=notexist");
        }else{
            while ($row = mysqli_fetch_assoc($result)) {
                print_r($row);
                $needed_ID = $row['customerID'];
            }
            $_SESSION['customer_identifier'] = $needed_ID;
            header("Location: ../customer.php?csearch=exist");
        }
    } 
}

if (isset($_POST['customer_add_submit'])) {
    include_once '../lib/queryfunction.php';
    $conn = mysql_conn_init_6400();
    $by_individual = 0;
    $by_business = 0;

    $email_address=mysqli_real_escape_string($conn, $_POST['email_address']);
    $phone_number=mysqli_real_escape_string($conn, $_POST['phone_number']);
    $street_address=mysqli_real_escape_string($conn, $_POST['street_address']);
    $city=mysqli_real_escape_string($conn, $_POST['city']);
    $state=mysqli_real_escape_string($conn, $_POST['state']);
    $postal_code=mysqli_real_escape_string($conn, $_POST['postal_code']);

    if (isset($_POST['customer_type']) && $_POST['customer_type']=="Individual")
    {
        $by_individual = 1;
        $driver_license=mysqli_real_escape_string($conn, $_POST['driver_license']);
        $first_name=mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name=mysqli_real_escape_string($conn, $_POST['last_name']);
    }

    if (isset($_POST['customer_type']) && $_POST['customer_type']=="Business"){
        $by_business = 1;
        $tax_identification_id=mysqli_real_escape_string($conn, $_POST['tax_identification_id']);
        $business_name=mysqli_real_escape_string($conn, $_POST['business_name']);
        $primary_contact_title=mysqli_real_escape_string($conn, $_POST['primary_contact_title']);
        $primary_contact_first_name=mysqli_real_escape_string($conn, $_POST['primary_contact_first_name']);
        $primary_contact_last_name=mysqli_real_escape_string($conn, $_POST['primary_contact_last_name']);
    }

    if ($by_individual == 0 && $by_business == 0){
        header("Location: ../customeradd.php?csadd=empty");
    }else{
        // Individual case 
        if($by_individual == 1){
            if (empty($phone_number) || empty($street_address) || empty($city) || empty($state) || empty($postal_code) || empty($driver_license) || empty($first_name) || empty($last_name)){
                echo $phone_number;
                header("Location: ../customeradd.php?csadd=empty");
            }else{
                $result = add_individual_query($conn, $email_address, $phone_number, $street_address, $city, $state, $postal_code, $driver_license, $first_name, $last_name);
                //https://stackoverflow.com/questions/42327938/check-if-update-query-was-successful-php-mysqli
                echo "result".$result;
                if($result > 0 ){
                    header("Location: ../customeradd.php?csadd=success");
                }else{
                    header("Location: ../customeradd.php?csadd=failed");
                }
            }
        }else{
            //Business case
            if (empty($phone_number) || empty($street_address) || empty($city) || empty($state) || empty($postal_code) || empty($tax_identification_id) || empty($business_name) || empty($primary_contact_title) || empty($primary_contact_first_name) || empty($primary_contact_last_name)){
                //header("Location: customeradd.php?csadd=empty");
            }else{
                $result = add_business_query($conn, $email_address, $phone_number, $street_address, $city, $state, $postal_code, $tax_identification_id, $business_name, $primary_contact_title, $primary_contact_first_name, $primary_contact_last_name);
                echo "result".$result;
                if($result > 0 ){
                   header("Location: ../customeradd.php?csadd=success");
                }else{
                    header("Location: ../customeradd.php?csadd=failed");
                }
            }
        }
    }
}
