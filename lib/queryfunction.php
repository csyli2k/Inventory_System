<?php
// connect to the mysql
function mysql_conn_init_6400()
{
    $dbServername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbName = "cs6400_fa21";
    $conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}

function query_and_return($conn, $q) {
    $result = mysqli_query($conn, $q);
    if (!$result) {
        echo("Error description: " . mysqli_error($conn));
    }
    return $result;
}

function customError($errno, $errstr) {
    echo "<b>Error:</b> [$errno] $errstr";
}
set_error_handler("customError");

//---------------------Index related sql---------------------------------------------
function get_total($conn){
    $get_total_sql = "
    SELECT 
        COUNT(*) AS total 
    FROM
        Vehicle v
        LEFT OUTER JOIN Sale s ON v.vin = s.vin
    WHERE
        s.sale_date IS NULL
    ;";
    return mysqli_query($conn, $get_total_sql);
}
// ---------------------Log in related sql---------------------------------------------
function get_login_query($conn, $username)
{
    $login_sql = "SELECT * FROM LoggedInUser WHERE username= '$username';";
    return mysqli_query($conn, $login_sql);
}

function get_UserType($conn, $username)
{
    // a user can have multiple roles based on the design doc and EER
    $usertype_sql = "SELECT 'salepeople' AS usertype, username FROM SalePeople
    WHERE username= '$username'
    UNION
    SELECT 'servicewriter' AS usertype, username FROM ServiceWriter
    WHERE username= '$username'
    UNION
    SELECT 'theowner' AS usertype, username FROM `Owner`
    WHERE username= '$username'
    UNION
    SELECT 'manager' AS usertype, username FROM Manager
    WHERE username= '$username'
    UNION
    SELECT 'inventoryclerk' AS usertype, username FROM InventoryClerk
    WHERE username= '$username'";

    $result = mysqli_query($conn, $usertype_sql);
    $result_check = mysqli_num_rows($result);
    $usertype_array = array();

    if ($result_check > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            if($row['usertype'] == 'theowner'){
                $theusertype= "owner";
            }else{
                $theusertype= $row['usertype'];
            }
            array_push($usertype_array, $theusertype);
        }
    }

    return $usertype_array;
}

// ---------------------Customer related sql---------------------------------------------
function get_individual_search_query($conn, $customer_search_id)
{
    $individual_search = "SELECT customerID FROM `Individual` WHERE driver_license= '$customer_search_id';";
    return mysqli_query($conn, $individual_search);
}

function get_business_search_query($conn, $customer_search_id)
{
    $individual_search = "SELECT customerID FROM `Business` WHERE tax_id_number= '$customer_search_id';";
    return mysqli_query($conn, $individual_search);
}

function add_individual_query($conn, $email_address, $phone_number, $street_address, $city, $state, $postal_code, $driver_license, $first_name, $last_name)
{
    // check unique, if exist, return back
    $query = "SELECT driver_license FROM `Individual` WHERE driver_license = '$driver_license';";
    $query_check = mysqli_query($conn, $query);
    $result_count = mysqli_num_rows($query_check);
    if( $result_count > 0){
        return -1;
    }

    // add query
    $add = "INSERT INTO `Customer` (email_address, phone_number, street_address, city, state, postal_code) 
    VALUES ('$email_address', '$phone_number', '$street_address', '$city', '$state', '$postal_code');
    SET @last_id_in_Customer = LAST_INSERT_ID(); 
    
    INSERT INTO `Individual` (driver_license, first_name, last_name, customerID) 
    VALUES ('$driver_license', '$first_name', '$last_name', @last_id_in_Customer);
   ";

    //return ($conn->multi_query($add) or trigger_error("Query Failed! SQL: $sql - Error: ".mysqli_error($conn), E_USER_ERROR));
    return ($conn->multi_query($add));
}

function add_business_query($conn, $email_address, $phone_number, $street_address, $city, $state, $postal_code, $tax_identification_id, $business_name, $primary_contact_title, $primary_contact_first_name, $primary_contact_last_name){
    // check unique, if exist, return back
    $query = "SELECT tax_id_number FROM `Business` WHERE tax_id_number = '$tax_identification_id';";
    $query_check = mysqli_query($conn, $query);
    $result_count = mysqli_num_rows($query_check);
    if( $result_count > 0){
        return -1;
    }

    // add query
    $add = "INSERT INTO `Customer` (email_address, phone_number, street_address, city, state, postal_code) 
    VALUES ('$email_address', '$phone_number', '$street_address', '$city', '$state', '$postal_code');
    
    SET @last_id_in_Customer = LAST_INSERT_ID(); 
    
    INSERT INTO `Business` (tax_id_number, business_name, primary_contact_title, primary_contact_first_name, primary_contact_last_name, customerID) 
    VALUES ('$tax_identification_id', '$business_name', '$primary_contact_title', '$primary_contact_first_name', '$primary_contact_last_name', @last_id_in_Customer); ";

    //return ($conn->multi_query($add) or trigger_error("Query Failed! SQL: $sql - Error: ".mysqli_error($conn), E_USER_ERROR));
    return ($conn->multi_query($add));
}


// ---------------------Report related sql---------------------------------------------
function query_top15_customer_gross_income($conn){
    $query = "
    WITH customerinfo AS (
        SELECT c.customerID as customer_id, 
                    TRIM(CONCAT(
                    IFNULL(i.first_name, ''),' ',
                    IFNULL(i.last_name, ''),
                    IFNULL(b.primary_contact_first_name, ''),' ',
                    IFNULL(b.primary_contact_last_name, '')
                )) AS customername
        FROM `Customer` c LEFT JOIN Individual i ON c.customerID = i.customerID LEFT JOIN Business b ON c.customerID = b.customerID
    ),
    customerSaleinfo AS (
        SELECT c.customer_id,MIN(s.sale_date) AS firstsaledate,MAX(s.sale_date) AS lastsaledate,COUNT(s.vin) AS number_of_sales,IFNULL(SUM(s.sale_price),0) AS total_sale 
        FROM Sale s 
        RIGHT JOIN customerinfo c on c.customer_id = s.customerID 
        GROUP BY 1
    ),
    customerRepairPartsinfo AS (
        SELECT c.customer_id,(r.start_date) AS firstrepairdate,MAX(r.start_date) AS lastrepairdate,COUNT(r.vin) AS number_of_repairs,IFNULL(SUM(r.labor_charge)+SUM(repaircost),0) AS totalrepaircost
        FROM Repair r
        RIGHT JOIN customerinfo c on c.customer_id = r.customerID 
        LEFT JOIN (SELECT vin, start_date,SUM(price*quantity) AS repaircost FROM Parts GROUP BY 1,2) AS pc ON pc.vin = r.vin and pc.start_date = r.start_date
        GROUP BY 1
        ORDER BY COUNT(r.vin) DESC
    )    
    
    SELECT ci.customer_id,ci.customername,
    (CASE WHEN ISNULL(firstrepairdate) THEN firstsaledate 
    WHEN ISNULL(firstsaledate) THEN firstrepairdate 
    WHEN firstrepairdate<firstsaledate THEN firstrepairdate ELSE firstsaledate END) AS date_of_the_first_sale_or_startrepair,
    (CASE WHEN ISNULL(lastrepairdate) THEN lastsaledate 
    WHEN ISNULL(lastsaledate) THEN lastrepairdate 
    WHEN lastrepairdate<lastsaledate THEN lastsaledate ELSE lastrepairdate END) AS date_of_the_latest_sale_or_startrepair,
    number_of_repairs,number_of_sales,(total_sale+totalrepaircost) AS gross_income
    FROM customerinfo AS ci 
    LEFT JOIN customerRepairPartsinfo AS crp ON crp.customer_id = ci.customer_id
    LEFT JOIN customerSaleinfo AS cs ON cs.customer_id = ci.customer_id
    ORDER BY gross_income DESC,date_of_the_latest_sale_or_startrepair DESC
    ;";
    return mysqli_query($conn, $query);
}
    
function query_color_report($conn) {
    $q = "WITH salebycolor1 AS (
        SELECT vc.vin, s.sale_date, (CASE WHEN COUNT(vc.vin)> 1 THEN 'multiple' ELSE vc.color End) AS the_color
        FROM VehicleColor AS vc
        INNER JOIN Sale AS s ON vc.vin = s.vin
        GROUP BY vin
        ),
        salebycolor2 AS (
        SELECT * FROM
        salebycolor1,(SELECT MAX(sale_date) AS last_available_sale_date FROM Sale) AS temp1
        )
        SELECT 
        table1.the_color,
        Sales_from_past_30days,
        Sales_from_past_year,
        Sales_from_past_alltime
        FROM(
        SELECT the_color, COUNT(the_color) AS Sales_from_past_alltime
        FROM salebycolor2 
        GROUP BY the_color)AS table1
        LEFT JOIN (
        SELECT the_color,COUNT(the_color) AS Sales_from_past_year
        FROM salebycolor2
        WHERE sale_date >= DATE_ADD(last_available_sale_date, INTERVAL -1 YEAR)
        GROUP BY the_color) AS table2 ON table1.the_color = table2.the_color
        LEFT JOIN (
        SELECT the_color,COUNT(the_color) AS Sales_from_past_30days
        FROM salebycolor2 
        WHERE sale_date >= DATE_ADD(last_available_sale_date, INTERVAL -30 DAY)
        GROUP BY the_color) AS table3 ON table1.the_color = table3.the_color
        ORDER BY the_color;";

        return query_and_return($conn, $q);
}

function query_type_result($conn) {
    $q = "WITH salebytype1 AS (
        SELECT 'Car' AS type, sale_date, COUNT(sale_date) as same_date_total_sales
        FROM Car AS v1
        INNER JOIN Sale AS s ON v1.vin = s.vin
        GROUP BY sale_date
        UNION
        SELECT 'Convertible' AS type, sale_date, COUNT(sale_date) as same_date_total_sales
        FROM Convertible AS v2
        INNER JOIN Sale AS s ON v2.vin = s.vin
        GROUP BY sale_date
        UNION
        SELECT 'Van' AS type, sale_date, COUNT(sale_date) as same_date_total_sales
        FROM Van AS v3
        INNER JOIN Sale AS s ON v3.vin = s.vin
        GROUP BY sale_date
        UNION
        SELECT 'Truck' AS type, sale_date, COUNT(sale_date) as same_date_total_sales
        FROM Truck AS v4
        INNER JOIN Sale AS s ON v4.vin = s.vin
        GROUP BY sale_date
        UNION
        SELECT 'Suv' AS type, sale_date, COUNT(sale_date) as same_date_total_sales
        FROM Suv AS v5
        INNER JOIN Sale AS s ON v5.vin = s.vin
        GROUP BY sale_date
        ),
        salebytype2 AS (
        SELECT * FROM
        salebytype1,(SELECT MAX(sale_date) AS last_available_sale_date FROM Sale) AS temp1
        )
        
        SELECT table1.type, 
        Sales_from_past_30days, 
        Sales_from_past_year, 
        Sales_from_past_alltime 
        FROM(SELECT type, SUM(same_date_total_sales) AS Sales_from_past_alltime
        FROM salebytype2 GROUP BY type) AS table1
        LEFT JOIN (SELECT type, SUM(same_date_total_sales) AS Sales_from_past_year
        FROM salebytype2 WHERE sale_date >= DATE_ADD(last_available_sale_date, INTERVAL -1 YEAR)
        GROUP BY type) AS table2 ON table1.type = table2.type
        LEFT JOIN (SELECT type, SUM(same_date_total_sales) AS Sales_from_past_30days
        FROM salebytype2 WHERE sale_date >= DATE_ADD(last_available_sale_date, INTERVAL -30 DAY)
        GROUP BY type) AS table3 ON table1.type = table3.type ORDER BY type;";
    
    return query_and_return($conn, $q);
}

function query_manufacturer_result($conn) {
    $q = "WITH salebymanufacturer1 AS (
        SELECT manufacturer_name, sale_date, COUNT(sale_date) as same_date_total_sales
        FROM Sale AS s
        LEFT JOIN Vehicle AS v ON s.vin = v.vin
        GROUP BY manufacturer_name,sale_date
        ),
        salebymanufacturer2 AS (
        SELECT * FROM
        salebymanufacturer1, (SELECT MAX(sale_date) AS last_available_sale_date FROM Sale) AS temp1
        )
        
        -- Join three tables to get the 30days, past year and past all time
        SELECT 
        table_alltime.manufacturer_name, 
        table_30days.Sales_from_past_30days, 
        table_1year.Sales_from_past_year, 
        table_alltime.Sales_from_past_alltime
        FROM (
        SELECT manufacturer_name, SUM(same_date_total_sales) AS Sales_from_past_alltime
        FROM salebymanufacturer2 
        GROUP BY manufacturer_name) AS table_alltime
        
        LEFT JOIN 
        (SELECT manufacturer_name, SUM(same_date_total_sales) AS Sales_from_past_year
        FROM salebymanufacturer2
        WHERE sale_date >= DATE_ADD(last_available_sale_date, INTERVAL -1 YEAR)
        GROUP BY manufacturer_name
        ) AS table_1year ON table_alltime.manufacturer_name = table_1year.manufacturer_name
        
        LEFT JOIN 
        (
        SELECT manufacturer_name, SUM(same_date_total_sales) AS Sales_from_past_30days
        FROM salebymanufacturer2 
        WHERE sale_date >= DATE_ADD(last_available_sale_date, INTERVAL -30 DAY)
        GROUP BY manufacturer_name
        ) AS table_30days ON table_alltime.manufacturer_name = table_30days.manufacturer_name
        
        ORDER BY manufacturer_name";

    return query_and_return($conn, $q);
}

function query_sale_records_of_customer($conn, $customer_id) {
    $query = "SELECT
    s.sale_date AS sale_date,
    s.sale_price AS sale_price,
    v.vin AS vin,
    v.model_year AS model_year,
    v.manufacturer_name AS manufacturer_name,
    v.model_name AS model_name,
    CONCAT(u.first_name,' ',u.last_name) AS salesperson_name
    FROM Sale s
    LEFT JOIN Customer c ON s.customerID = c.customerID
    LEFT JOIN Vehicle v ON v.vin = s.vin
    LEFT JOIN LoggedInUser u ON s.username = u.username
    WHERE s.customerID = $customer_id
    ORDER BY s.sale_date DESC,v.vin;";
    return mysqli_query($conn, $query);
}

function query_repair_records_of_customer($conn, $customer_id) {
    $query = "SELECT 
    r.start_date,
    IFNULL(r.complete_date, '') AS end_date,
    r.vin,
    r.odometer_reading,
    IFNULL(pc.repaircost,0) AS parts_cost,
    r.labor_charge,
    IFNULL(r.labor_charge+ pc.repaircost,0) AS totalrepaircost,
    CONCAT(u.first_name,' ',u.last_name) AS servicewriter_name
    FROM Repair r
    LEFT JOIN Customer c on c.customerID = r.customerID 
    LEFT JOIN (SELECT vin, start_date,SUM(price*quantity) AS repaircost FROM Parts GROUP BY 1,2) AS pc ON pc.vin = r.vin and pc.start_date = r.start_date
    LEFT JOIN LoggedInUser u ON r.username = u.username
    WHERE c.customerID = $customer_id
    ORDER BY r.start_date DESC,r.complete_date DESC,r.vin;";
    return mysqli_query($conn, $query);
}

function query_repair_by_manufacturer_type_model($conn) {
    $query = "SELECT v.manufacturer_name,
    COUNT(r.start_date) AS the_count_of_repairs,
    SUM(repaircost) AS total_parts_cost,
    SUM(r.labor_charge) AS total_labor_cost,
    IFNULL(SUM(r.labor_charge)+SUM(repaircost),0) AS total_repair_cost
    FROM Repair r
    RIGHT JOIN Vehicle v on v.vin  = r.vin
    LEFT JOIN (SELECT vin, start_date,SUM(price*quantity) AS repaircost FROM Parts GROUP BY 1,2) AS pc ON pc.vin = r.vin and pc.start_date = r.start_date
    GROUP BY 1
    ORDER BY v.manufacturer_name;";
    return mysqli_query($conn, $query);
}

function query_report_dilldown_manufacturer($conn, $manufacturer_name) {
    $query = "WITH table1 AS (SELECT v.manufacturer_name,v.vin,v.model_name,
    IFNULL(COUNT(r.start_date),0) AS the_count_of_repairs,
    IFNULL(SUM(repaircost),0) AS total_parts_cost,
    IFNULL(SUM(r.labor_charge),0) AS total_labor_cost,
    IFNULL(SUM(r.labor_charge)+SUM(repaircost),0) AS total_repair_cost
    FROM Repair r
    -- Models and/or vehicle types which do not have repairs should be excluded from this report. So left join
    LEFT JOIN Vehicle v on v.vin  = r.vin 
    LEFT JOIN (SELECT vin, start_date,SUM(price*quantity) AS repaircost FROM Parts GROUP BY 1,2) AS pc ON pc.vin = r.vin and pc.start_date = r.start_date
    GROUP BY 1,2,3
    ORDER BY v.manufacturer_name),
    table2 AS (
    SELECT 'Car' AS type, vin AS v_vin FROM `Car`
    UNION
    SELECT 'Convertible' AS type, vin AS v_vin FROM `Convertible`
    UNION
    SELECT 'Truck' AS type, vin AS v_vin FROM `Truck`
    UNION
    SELECT 'Van' AS type, vin AS v_vin FROM `Van`
    UNION
    SELECT 'Suv' AS type, vin AS v_vin FROM `Suv`
    ),
    table3 AS (
    SELECT type,CONCAT('   ',model_name) AS type_model_k,
    IFNULL(SUM(the_count_of_repairs),0) AS the_count_of_repairs_k,
    IFNULL(SUM(total_parts_cost),0) AS total_parts_cost_k,
    IFNULL(SUM(total_labor_cost),0) AS total_labor_cost_k,
    IFNULL(SUM(total_parts_cost)+SUM(total_labor_cost),0) AS total_repair_cost_k,
    -1 AS parent_k
    FROM table1 
    LEFT JOIN table2 on table2.v_vin = table1.vin
    WHERE manufacturer_name = '$manufacturer_name'
    GROUP BY 1,2
    ),
    table4 AS (
    SELECT type,CONCAT(type) AS type_model_p,
    IFNULL(SUM(the_count_of_repairs),0) AS the_count_of_repairs_p,
    IFNULL(SUM(total_parts_cost),0) AS total_parts_cost_p,
    IFNULL(SUM(total_labor_cost),0) AS total_labor_cost_p,
    IFNULL(SUM(total_parts_cost)+SUM(total_labor_cost),0) AS total_repair_cost_p,
    1 AS parent_p
    FROM table1 
    LEFT JOIN table2 on table2.v_vin = table1.vin
    WHERE manufacturer_name = '$manufacturer_name'
    GROUP BY 1
    ORDER BY the_count_of_repairs DESC
    ),
    table5 AS (
    SELECT * FROM table3 
    UNION
    SELECT * FROM table4)
    
    SELECT type_model_k,the_count_of_repairs_k,total_parts_cost_k,total_labor_cost_k,total_repair_cost_k
    FROM table4 
    INNER JOIN (SELECT * FROM table5) AS temp ON temp.type = table4.type
    ORDER BY the_count_of_repairs_p DESC,table4.type, the_count_of_repairs_k DESC, parent_k DESC;";
    return query_and_return($conn, $query);
}

function query_report_below_cost_sales($conn) {
    $query = "SELECT s.vin, s.sale_date,v.invoice_price,s.sale_price,
    CONCAT(u.first_name,' ', u.last_name) AS salesperson,
    TRIM(CONCAT(IFNULL(i.first_name,''),' ',IFNULL(i.last_name,''),IFNULL(b.primary_contact_first_name,''),' ',IFNULL(b.primary_contact_last_name,''))) AS customername,
    ROUND((s.sale_price / v.invoice_price), 2) AS invoice_over_sold_ratio
    FROM Sale AS s
    LEFT JOIN Vehicle AS v ON v.vin = s.vin
    LEFT JOIN LoggedInUser AS u ON u.username = s.username
    LEFT JOIN Customer AS c ON c.customerID = s.customerID
    LEFT JOIN Individual AS i ON i.customerID = c. customerID
    LEFT JOIN Business AS b ON b.customerID = c. customerID
    WHERE v.invoice_price > s.sale_price
    ORDER BY s.sale_date DESC, invoice_over_sold_ratio DESC;";
    return mysqli_query($conn, $query); 
}

function query_report_avg_time_in_inventory($conn) {
    $query = "SELECT 'Car' AS type, IFNULL(ROUND(AVG(DATEDIFF(s.sale_date,v.inventory_added_date)+1),0),'N/A') AS average_days_in_inventory
    FROM Vehicle AS v
    INNER JOIN Car AS c ON v.vin = c.vin
    INNER JOIN Sale AS s ON v.vin = s.vin
    UNION
    SELECT 'Convertible' AS type, IFNULL(ROUND(AVG(DATEDIFF(s.sale_date,v.inventory_added_date)+1),0),'N/A') AS average_days_in_inventory
    FROM Vehicle AS v
    INNER JOIN Convertible AS c ON v.vin = c.vin
    INNER JOIN Sale AS s ON v.vin = s.vin
    UNION
    SELECT 'Truck' AS  type, IFNULL(ROUND(AVG(DATEDIFF(s.sale_date,v.inventory_added_date)+1),0),'N/A') AS average_days_in_inventory
    FROM Vehicle AS v
    INNER JOIN Truck AS t ON v.vin = t.vin
    INNER JOIN Sale AS s ON v.vin = s.vin
    UNION
    SELECT 'Van' AS  type, IFNULL(ROUND(AVG(DATEDIFF(s.sale_date,v.inventory_added_date)+1),0),'N/A') AS average_days_in_inventory
    FROM Vehicle AS v
    INNER JOIN Van AS t ON v.vin = t.vin
    INNER JOIN Sale AS s ON v.vin = s.vin
    UNION
    SELECT 'Suv' AS  type, IFNULL(ROUND(AVG(DATEDIFF(s.sale_date,v.inventory_added_date)+1),0),'N/A') AS average_days_in_inventory
    FROM Vehicle AS v
    INNER JOIN Suv AS t ON v.vin = t.vin
    INNER JOIN Sale AS s ON v.vin = s.vin
    ORDER BY  type;";
    return mysqli_query($conn, $query);  
}

function query_report_parts_statistics($conn) {
    $query = "SELECT vendor_name, SUM(quantity) AS total_parts_supplied,SUM(quantity*price) as total_cost
    FROM Parts
    GROUP BY vendor_name
    ORDER BY total_cost DESC";
    return mysqli_query($conn, $query);
}

function query_report_monthly_sales($conn) {
    $q = "SELECT
        EXTRACT(YEAR_MONTH FROM s.sale_date) AS sale_year_month,
        COUNT(EXTRACT(YEAR_MONTH FROM s.sale_date)) AS number_of_sold_vehicle,
        SUM(s.sale_price) AS total_sales_income,
        SUM(s.sale_price) - SUM(v.invoice_price) AS total_net_income,
        ROUND(
            SUM(s.sale_price) / SUM(v.invoice_price),
            0
        ) AS sale_invoice_price_ratio
    FROM
        Vehicle V
    JOIN Sale S ON
        S.vin = V.vin
    WHERE
        S.sale_date IS NOT NULL
    GROUP BY
        MONTH(s.sale_date)
    ORDER BY
        EXTRACT(YEAR_MONTH FROM s.sale_date)
    DESC
        ,
        MONTH(s.sale_date)
    DESC
        ;";
    return query_and_return($conn, $q);
}

function query_report_drilldown_sale_year_month($conn, $year_month) {
    $q = "SELECT 
        EXTRACT(YEAR_MONTH FROM S.sale_date) AS sale_year_month,
        U.first_name,
        U.last_name,
        COUNT(S.username) as total_num_of_vehicle_sold,
        SUM(S.sale_price) AS total_sales
        FROM
        Sale S
        JOIN LoggedInUser U ON S.username = U.username
        WHERE EXTRACT(YEAR_MONTH FROM S.sale_date) = $year_month
        GROUP BY
        U.username,
        sale_year_month
        ORDER BY
        total_num_of_vehicle_sold DESC,
        total_sales DESC
        ";
    return query_and_return($conn, $q);
}

// ---------------------Search related sql below---------------------------------------------
function get_distinct_manu_name($conn){
    $manu_name_sql = "SELECT DISTINCT manufacturer_name FROM Vehicle;";
    return mysqli_query($conn, $manu_name_sql);
}

function get_distinct_model_year($conn){
    $model_year_sql = "SELECT DISTINCT model_year FROM Vehicle ORDER BY model_year;";
    return mysqli_query($conn, $model_year_sql);
}

function get_distinct_model_name($conn){
    $model_name_sql = "SELECT DISTINCT model_name FROM Vehicle ORDER BY model_name;";
    return mysqli_query($conn, $model_name_sql);
}

function get_distinct_color($conn){
    $color_sql = "SELECT DISTINCT color FROM VehicleColor ORDER BY color;";
    return mysqli_query($conn, $color_sql);
}

// function get_search_result($filter, $color, $manua_Name, $model_Year, $model_Name, $vin, $vehicle_Type, $keyWord, $conn) {
//     //echo $color.$manua_Name.$model_Year.$model_Name.$vin.$filter; 
//     $search_result_sql = "
//             SELECT 
//                 v.vin,
//                 v.model_year,
//                 v.manufacturer_name,
//                 v.model_name,
//                 vc.combinedColor,
//                 v.invoice_price
//             FROM
//                 Vehicle v
//                 INNER JOIN (SELECT vin, GROUP_CONCAT(color SEPARATOR ', ')AS combinedColor FROM `VehicleColor` GROUP BY vin) vc ON v.vin = vc.vin
//                 LEFT OUTER JOIN Sale s ON v.vin = s.vin 
//                 INNER JOIN `{$vehicle_Type}` t ON v.vin = t.vin
//             WHERE
//                 v.vin is NOT NULL {$color} {$manua_Name} {$model_Year} {$model_Name} {$vin} {$filter} 
//             UNION 
//                 SELECT 
//                     v.vin,
//                     v.model_year,
//                     v.manufacturer_name,
//                     v.model_name,
//                     vc.combinedColor,
//                     v.invoice_price
//                 FROM
//                     Vehicle v
//                     INNER JOIN (SELECT vin, GROUP_CONCAT(color SEPARATOR ', ')AS combinedColor FROM `VehicleColor` GROUP BY vin) vc ON v.vin = vc.vin
//                     LEFT OUTER JOIN Sale s ON v.vin = s.vin
//                     INNER JOIN `{$vehicle_Type}` t ON v.vin = t.vin
//                 WHERE
//                     v.manufacturer_name LIKE '%$keyWord%' OR v.model_year LIKE '%$keyWord%' OR v.model_name LIKE '%$keyWord%' OR v.description LIKE '%$keyWord%'
//             ORDER BY
//                 vin
//             ;";
//     return mysqli_query($conn, $search_result_sql);
// }

function get_search_result($filter, $color, $manua_Name, $model_Year, $model_Name, $vin, $vehicle_Type, $keyWord, $conn) {
    //echo $color.$manua_Name.$model_Year.$model_Name.$vin.$filter; 
    $search_result_sql = "
            SELECT 
                v.vin,
                v.model_year,
                v.manufacturer_name,
                v.model_name,
                vc.combinedColor,
                v.invoice_price
            FROM
                Vehicle v
                INNER JOIN (SELECT vin, GROUP_CONCAT(color SEPARATOR ', ')AS combinedColor FROM `VehicleColor` GROUP BY vin) vc ON v.vin = vc.vin
                LEFT OUTER JOIN Sale s ON v.vin = s.vin 
                INNER JOIN `{$vehicle_Type}` t ON v.vin = t.vin
            WHERE
                v.vin is NOT NULL {$color} {$manua_Name} {$model_Year} {$model_Name} {$vin} {$filter} AND ('$keyWord' IS NULL OR v.manufacturer_name LIKE '%$keyWord%' OR v.model_year LIKE '%$keyWord%' OR v.model_name LIKE '%$keyWord%' OR v.description LIKE '%$keyWord%')
           
            ;";
    return mysqli_query($conn, $search_result_sql);
}

function get_unsold_detail($vehicle_Type, $vin, $conn){
    $unsold_detail_query = "";
    if($vehicle_Type === "Car"){
        $unsold_detail_query = "
        SELECT 
            v.vin,
            t.number_of_doors,
            v.model_year,
            v.model_name,
            v.manufacturer_name,
            vc.combinedColor,
            v.invoice_price,
            v.description,
            s.sale_date,
            v.inventory_added_date,
            v.username
        FROM
            Vehicle v
            INNER JOIN (SELECT vin, GROUP_CONCAT(color SEPARATOR ', ')AS combinedColor FROM `VehicleColor` GROUP BY vin) vc ON v.vin = vc.vin
            LEFT OUTER JOIN Sale s ON v.vin = s.vin
            INNER JOIN `{$vehicle_Type}` t ON t.vin = v.vin
        WHERE
            v.vin = '$vin'
        ;";
    }
    if($vehicle_Type === "Convertible"){
        $unsold_detail_query = "
        SELECT 
            v.vin,
            t.back_seat_count,
            t.roof_type,
            v.model_year,
            v.model_name,
            v.manufacturer_name,
            vc.combinedColor,
            v.invoice_price,
            v.description,
            s.sale_date,
            v.inventory_added_date,
            v.username
        FROM
            Vehicle v
            INNER JOIN (SELECT vin, GROUP_CONCAT(color SEPARATOR ', ')AS combinedColor FROM `VehicleColor` GROUP BY vin) vc ON v.vin = vc.vin
            LEFT OUTER JOIN Sale s ON v.vin = s.vin
            INNER JOIN `{$vehicle_Type}` t ON t.vin = v.vin
        WHERE
            v.vin = '$vin'
        ;";
    } 
    if($vehicle_Type === "Truck"){
        $unsold_detail_query = "
        SELECT 
            v.vin,
            t.cargo_cover_type,
            t.cargo_capacity,
            t.number_of_rear_axies,
            v.model_year,
            v.model_name,
            v.manufacturer_name,
            vc.combinedColor,
            v.invoice_price,
            v.description,
            s.sale_date,
            v.inventory_added_date,
            v.username
        FROM
            Vehicle v
            INNER JOIN (SELECT vin, GROUP_CONCAT(color SEPARATOR ', ')AS combinedColor FROM `VehicleColor` GROUP BY vin) vc ON v.vin = vc.vin
            LEFT OUTER JOIN Sale s ON v.vin = s.vin
            INNER JOIN `{$vehicle_Type}` t ON t.vin = v.vin
        WHERE
            v.vin = '$vin'
        ;";
    } 
    if($vehicle_Type === "Van"){
        $unsold_detail_query = "
        SELECT 
            v.vin,
            t.has_drive_side_door,
            v.model_year,
            v.model_name,
            v.manufacturer_name,
            vc.combinedColor,
            v.invoice_price,
            v.description,
            s.sale_date,
            v.inventory_added_date,
            v.username
        FROM
            Vehicle v
            INNER JOIN (SELECT vin, GROUP_CONCAT(color SEPARATOR ', ')AS combinedColor FROM `VehicleColor` GROUP BY vin) vc ON v.vin = vc.vin
            LEFT OUTER JOIN Sale s ON v.vin = s.vin
            INNER JOIN `{$vehicle_Type}` t ON t.vin = v.vin
        WHERE
            v.vin = '$vin'
        ;";
    } 
    if($vehicle_Type === "Suv"){
        $unsold_detail_query = "
        SELECT 
            v.vin,
            t.drivetrain_type,
            t.num_of_cupholders,  
            v.model_year,
            v.model_name,
            v.manufacturer_name,
            vc.combinedColor,
            v.invoice_price,
            v.description,
            s.sale_date,
            v.inventory_added_date,
            v.username
        FROM
            Vehicle v
            INNER JOIN (SELECT vin, GROUP_CONCAT(color SEPARATOR ', ')AS combinedColor FROM `VehicleColor` GROUP BY vin) vc ON v.vin = vc.vin
            LEFT OUTER JOIN Sale s ON v.vin = s.vin
            INNER JOIN `{$vehicle_Type}` t ON t.vin = v.vin
        WHERE
            v.vin = '$vin'
        ;";
    }
    return mysqli_query($conn, $unsold_detail_query);
} 


//----end of test----

function get_detail($vehicle_Type, $vin, $conn){
    $detail_query = "";
    
    if($vehicle_Type === "Car"){
        $detail_query = "
        SELECT 
            v.vin,
            t.number_of_doors,
            v.model_year,
            v.model_name,
            v.manufacturer_name,
            vc.combinedColor,
            v.invoice_price,
            v.description,
            s.sale_date,
            v.inventory_added_date,
            v.username,
            cu.phone_number,
            cu.email_address,
            cu.street_address,
            cu.city,
            cu.state,
            cu.postal_code,
            i.first_name,
            i.last_name,
            b.business_name,
            b.primary_contact_first_name,
            b.primary_contact_last_name,
            b.primary_contact_title,
            s.sale_price,
            s.sale_date,
            s.username AS salesPersonName,
            r.start_date,
            r.username AS inventoryName,
            r.complete_date,
            r.labor_charge,
            p.quantity,
            p.price,
            SUM(p.quantity * p.price) AS partsTotal
        FROM
            Vehicle v
            INNER JOIN (SELECT vin, GROUP_CONCAT(color SEPARATOR ', ')AS combinedColor FROM `VehicleColor` GROUP BY vin) vc ON v.vin = vc.vin
            INNER JOIN `{$vehicle_Type}` t ON t.vin = v.vin
            LEFT OUTER JOIN Sale s ON v.vin = s.vin
            LEFT OUTER JOIN Repair r ON v.vin = r.vin
            LEFT OUTER JOIN Parts p ON r.vin = p.vin AND r.start_date = p.start_date
            LEFT OUTER JOIN Customer cu ON s.customerID = cu.customerID
            LEFT OUTER JOIN Business b ON b.customerID = cu.customerID
            LEFT OUTER JOIN Individual i ON i.customerID = cu.customerID
        WHERE
            v.vin = '$vin'
        GROUP BY r.vin, r.start_date
        ;";
    } 
    if($vehicle_Type === "Convertible"){
        $detail_query = "
        SELECT 
            v.vin,
            t.back_seat_count,
            t.roof_type,
            v.model_year,
            v.model_name,
            v.manufacturer_name,
            vc.combinedColor,
            v.invoice_price,
            v.description,
            s.sale_date,
            v.inventory_added_date,
            v.username,
            cu.phone_number,
            cu.email_address,
            cu.street_address,
            cu.city,
            cu.state,
            cu.postal_code,
            i.first_name,
            i.last_name,
            b.business_name,
            b.primary_contact_first_name,
            b.primary_contact_last_name,
            b.primary_contact_title,
            s.sale_price,
            s.sale_date,
            s.username AS salesPersonName,
            r.start_date,
            r.username AS inventoryName,
            r.complete_date,
            r.labor_charge,
            p.quantity,
            p.price,
            SUM(p.quantity * p.price) AS partsTotal
        FROM
            Vehicle v
            INNER JOIN (SELECT vin, GROUP_CONCAT(color SEPARATOR ', ')AS combinedColor FROM `VehicleColor` GROUP BY vin) vc ON v.vin = vc.vin
            INNER JOIN `{$vehicle_Type}` t ON t.vin = v.vin
            LEFT OUTER JOIN Sale s ON v.vin = s.vin
            LEFT OUTER JOIN Repair r ON v.vin = r.vin
            LEFT OUTER JOIN Parts p ON r.vin = p.vin AND r.start_date = p.start_date
            LEFT OUTER JOIN Customer cu ON s.customerID = cu.customerID
            LEFT OUTER JOIN Business b ON b.customerID = cu.customerID
            LEFT OUTER JOIN Individual i ON i.customerID = cu.customerID
        WHERE
            v.vin = '$vin'
        GROUP BY r.vin, r.start_date
        ;";
    } 
    if($vehicle_Type === "Truck"){
        $detail_query = "
        SELECT 
            v.vin,
            t.cargo_cover_type,
            t.cargo_capacity,
            t.number_of_rear_axies,
            v.model_year,
            v.model_name,
            v.manufacturer_name,
            vc.combinedColor,
            v.invoice_price,
            v.description,
            s.sale_date,
            v.inventory_added_date,
            v.username,
            cu.phone_number,
            cu.email_address,
            cu.street_address,
            cu.city,
            cu.state,
            cu.postal_code,
            i.first_name,
            i.last_name,
            b.business_name,
            b.primary_contact_first_name,
            b.primary_contact_last_name,
            b.primary_contact_title,
            s.sale_price,
            s.sale_date,
            s.username AS salesPersonName,
            r.start_date,
            r.username AS inventoryName,
            r.complete_date,
            r.labor_charge,
            p.quantity,
            p.price,
            SUM(p.quantity * p.price) AS partsTotal
        FROM
            Vehicle v
            INNER JOIN (SELECT vin, GROUP_CONCAT(color SEPARATOR ', ')AS combinedColor FROM `VehicleColor` GROUP BY vin) vc ON v.vin = vc.vin
            INNER JOIN `{$vehicle_Type}` t ON t.vin = v.vin
            LEFT OUTER JOIN Sale s ON v.vin = s.vin
            LEFT OUTER JOIN Repair r ON v.vin = r.vin
            LEFT OUTER JOIN Parts p ON r.vin = p.vin AND r.start_date = p.start_date
            LEFT OUTER JOIN Customer cu ON s.customerID = cu.customerID
            LEFT OUTER JOIN Business b ON b.customerID = cu.customerID
            LEFT OUTER JOIN Individual i ON i.customerID = cu.customerID
        WHERE
            v.vin = '$vin'
        GROUP BY r.vin, r.start_date
        ;";
    } 
    if($vehicle_Type === "Van"){
        $detail_query = "
        SELECT 
            v.vin,
            t.has_drive_side_door,
            v.model_year,
            v.model_name,
            v.manufacturer_name,
            vc.combinedColor,
            v.invoice_price,
            v.description,
            s.sale_date,
            v.inventory_added_date,
            v.username,
            cu.phone_number,
            cu.email_address,
            cu.street_address,
            cu.city,
            cu.state,
            cu.postal_code,
            i.first_name,
            i.last_name,
            b.business_name,
            b.primary_contact_first_name,
            b.primary_contact_last_name,
            b.primary_contact_title,
            s.sale_price,
            s.sale_date,
            s.username AS salesPersonName,
            r.start_date,
            r.username AS inventoryName,
            r.complete_date,
            r.labor_charge,
            p.quantity,
            p.price,
            SUM(p.quantity * p.price) AS partsTotal
        FROM
            Vehicle v
            INNER JOIN (SELECT vin, GROUP_CONCAT(color SEPARATOR ', ')AS combinedColor FROM `VehicleColor` GROUP BY vin) vc ON v.vin = vc.vin
            INNER JOIN `{$vehicle_Type}` t ON t.vin = v.vin
            LEFT OUTER JOIN Sale s ON v.vin = s.vin
            LEFT OUTER JOIN Repair r ON v.vin = r.vin
            LEFT OUTER JOIN Parts p ON r.vin = p.vin AND r.start_date = p.start_date
            LEFT OUTER JOIN Customer cu ON s.customerID = cu.customerID
            LEFT OUTER JOIN Business b ON b.customerID = cu.customerID
            LEFT OUTER JOIN Individual i ON i.customerID = cu.customerID
        WHERE
            v.vin = '$vin'
        GROUP BY r.vin, r.start_date
        ;";
    } 
    if($vehicle_Type === "Suv"){
        $detail_query = "
        SELECT 
            v.vin,
            t.drivetrain_type,
            t.num_of_cupholders,  
            v.model_year,
            v.model_name,
            v.manufacturer_name,
            vc.combinedColor,
            v.invoice_price,
            v.description,
            s.sale_date,
            v.inventory_added_date,
            v.username,
            cu.phone_number,
            cu.email_address,
            cu.street_address,
            cu.city,
            cu.state,
            cu.postal_code,
            i.first_name,
            i.last_name,
            b.business_name,
            b.primary_contact_first_name,
            b.primary_contact_last_name,
            b.primary_contact_title,
            s.sale_price,
            s.sale_date,
            s.username AS salesPersonName,
            r.start_date,
            r.username AS inventoryName,
            r.complete_date,
            r.labor_charge,
            p.quantity,
            p.price,
            SUM(p.quantity * p.price) AS partsTotal
        FROM
            Vehicle v
            INNER JOIN (SELECT vin, GROUP_CONCAT(color SEPARATOR ', ')AS combinedColor FROM `VehicleColor` GROUP BY vin) vc ON v.vin = vc.vin
            INNER JOIN `{$vehicle_Type}` t ON t.vin = v.vin
            LEFT OUTER JOIN Sale s ON v.vin = s.vin
            LEFT OUTER JOIN Repair r ON v.vin = r.vin
            LEFT OUTER JOIN Parts p ON r.vin = p.vin AND r.start_date = p.start_date
            LEFT OUTER JOIN Customer cu ON s.customerID = cu.customerID
            LEFT OUTER JOIN Business b ON b.customerID = cu.customerID
            LEFT OUTER JOIN Individual i ON i.customerID = cu.customerID
        WHERE
            v.vin = '$vin'
        GROUP BY r.vin, r.start_date
        ;";
    } 
    return mysqli_query($conn, $detail_query);
}
// ---------------------Search related sql above---------------------------------------------




//------------add parts related -----------------
function add_parts_query($conn,$vin,$vendorname,$partnumber,$quantity,$price)
{
    $add_parts_sql="INSERT INTO Parts (vin, start_date, vendor_name, part_number, quantity, price) VALUES ('$vin', current_date(),'$vendorname','$partnumber','$quantity','$price');";
    return mysqli_query($conn, $add_parts_sql);
}


//------------complete repair related -----------------
function complete_repair_query($conn,$vin,$start_date)
{
    $complete_repair_sql="UPDATE Repair SET complete_date= current_date() WHERE (vin='$vin' AND start_date='$start_date');";
    return mysqli_query($conn, $complete_repair_sql);
}



//------------Edit repair related -----------------
function edit_repair_query($conn,$vin,$start_date, $labor_charge)
{
    $edit_repair_sql="UPDATE Repair SET labor_charge= '$labor_charge' WHERE (vin='$vin' AND start_date='$start_date');";
    return mysqli_query($conn, $edit_repair_sql);

}



//------------Add repair related -----------------
function add_repair_query($conn,$vin, $labor_charge, $description, $odometer_reading, $customerID, $username)
{
    $add_repair_sql="INSERT INTO Repair (vin, start_date, labor_charge, description, complete_date, odometer_reading, customerID, username) VALUES ('$vin', current_date(),'$labor_charge','$description', NULL,'$odometer_reading', '$customerID', '$username');";

    return mysqli_query($conn, $add_repair_sql);

}


function select_manufacturer_query($conn)
{
    $select_manufacturer_sql="SELECT manufacturer_name From Manufacturer;";

    return mysqli_query($conn, $select_manufacturer_sql);

}

function select_color_query($conn)
{
    $select_color_sql="SELECT color From VehicleColor;";

    return mysqli_query($conn, $select_color_sql);

}



//------------Search Result related -----------------
function vin_search_query($conn,$vin)
{
    $vin_search_sql="SELECT * 
        FROM Vehicle v JOIN Sale s ON v.vin=s.vin
        WHERE v.vin='$vin';";

    return mysqli_query($conn, $vin_search_sql);

}


function search_query($conn,$vin)
{
    $search_sql="SELECT DISTINCT v.vin, v.model_name, v.model_year,v.manufacturer_name, vc.color FROM Vehicle as v INNER JOIN Sale as s ON v.vin= s.vin INNER JOIN VehicleColor as vc ON v.vin= vc.vin
        WHERE v.vin= '$vin';";
    return mysqli_query($conn, $search_sql);

}


function complete_date_query($conn,$vin)
{
    $complete_date_sql="SELECT DISTINCT start_date FROM Repair WHERE vin= '$vin' AND complete_date is Null;";

    return mysqli_query($conn, $complete_date_sql);

}

//-----------edit_repair-------------
function repair_search_result_query($conn,$vin,$start_date)
{
    $repair_search_result_sql="SELECT * FROM Parts WHERE vin='$part_vin'and start_date='$part_start_date';";

    return mysqli_query($conn, $repair_search_result_sql);

}

function add_sale_by_salespeople($conn, $customer_id, $username, $purchase_date, $vin, $sold_price, $user_type) {
    $get_invoice_price = "SELECT v.invoice_price
                            FROM vehicle v
                            WHERE v.vin = '$vin'";
    $result = query_and_return($conn, $get_invoice_price);
    if ($result->num_rows == 0) {
        echo "NO vehicle is found";
        return;
    }
    $invoice_price = mysqli_fetch_row($result)[0];
    if ($invoice_price && ($invoice_price * 0.95 < $sold_price || $user_type == "owner")) {
        $add_vehicle = 
        "INSERT INTO sale (vin, customerID, username, sale_price, sale_date) 
        VALUES ('$vin', $customer_id, '$username', $sold_price, CAST('$purchase_date' AS DATE));";
        $result = query_and_return($conn, $add_vehicle);
        if ($result) {
            echo "add successfully";
        }
    } else {
        echo "sold price should be larger than 95% of the invoice price";
    }
}

function query_test($conn) {
    $q = "show tables";
    return query_and_return($conn, $q);
}















//------------add parts related -----------------
function add_parts_query_y($conn,$vin,$vendorname,$partnumber,$quantity,$price)
{
    $add_parts_sql="INSERT INTO Parts (vin, start_date, vendor_name, part_number, quantity, price) VALUES ('$vin', current_date(),'$vendorname','$partnumber','$quantity','$price');";
    return mysqli_query($conn, $add_parts_sql);
}


//------------complete repair related -----------------
function complete_repair_query_y($conn,$vin,$start_date)
{
    $complete_repair_sql="UPDATE Repair SET complete_date= current_date() WHERE (vin='$vin' AND start_date='$start_date');";
    return mysqli_query($conn, $complete_repair_sql);
}



//------------Edit repair related -----------------
function edit_repair_query_y($conn,$vin,$start_date, $labor_charge)
{
    $edit_repair_sql="UPDATE Repair SET labor_charge= '$labor_charge' WHERE (vin='$vin' AND start_date='$start_date');";
    return mysqli_query($conn, $edit_repair_sql);

}



//------------Add repair related -----------------
function add_repair_query_y($conn,$vin, $labor_charge, $description, $odometer_reading, $customerID, $username)
{
    $add_repair_sql="INSERT INTO Repair (vin, start_date, labor_charge, description, complete_date, odometer_reading, customerID, username) VALUES ('$vin', current_date(),'$labor_charge','$description', NULL,'$odometer_reading', '$customerID', '$username');";

    return mysqli_query($conn, $add_repair_sql);

}


function select_manufacturer_query_y($conn)
{
    $select_manufacturer_sql="SELECT manufacturer_name From Manufacturer;";

    return mysqli_query($conn, $select_manufacturer_sql);

}

function select_color_query_y($conn)
{
    $select_color_sql="SELECT DISTINCT color From VehicleColor ORDER BY color;";

    return mysqli_query($conn, $select_color_sql);

}



//------------Search Result related -----------------
function vin_search_query_y($conn,$vin)
{
    $vin_search_sql="SELECT * 
        FROM Vehicle v JOIN Sale s ON v.vin=s.vin
        WHERE v.vin='$vin';";

    return mysqli_query($conn, $vin_search_sql);

}


function search_query_y($conn,$vin)
{
    $search_sql="SELECT DISTINCT v.vin, v.model_name, v.model_year,v.manufacturer_name, vc.color FROM Vehicle as v INNER JOIN Sale as s ON v.vin= s.vin INNER JOIN VehicleColor as vc ON v.vin= vc.vin
        WHERE v.vin= '$vin';";
    return mysqli_query($conn, $search_sql);

}

//--------complete_date_query--------------
function complete_date_query_y($conn,$vin)
{
    $complete_date_sql="SELECT DISTINCT start_date FROM Repair WHERE (vin= '$vin' AND complete_date is Null);";

    return mysqli_query($conn, $complete_date_sql);

}

//-----------edit_repair-------------
function parts_search_result_query_y($conn,$vin,$start_date)
{
    $parts_search_result_sql="SELECT * FROM Parts WHERE vin='$vin'and start_date='$start_date';";

    return mysqli_query($conn, $parts_search_result_sql);

}

//------------Add Vehicle -----------------
function add_vehicle_query_y($conn,$vin,$model_name, $model_year, $invoice_price,$description, $manufacturer_name, $username)
{
    $add_vehicle_sql= "INSERT INTO Vehicle (vin, model_name, model_year, invoice_price, description, manufacturer_name, username, inventory_added_date) VALUES ('$vin','$model_name', '$model_year', '$invoice_price','$description', '$manufacturer_name', '$username', current_date());";

    return mysqli_query($conn, $add_vehicle_sql);

}

//--------add manufacturer-------------
function add_manufacturer_query_y($conn,$manufacturer_name)
{
    $add_manufacturer_sql="INSERT INTO Manufacturer (manufacturer_name) VALUES ('$manufacturer_name');";

    return mysqli_query($conn, $add_manufacturer_sql);

}

//--------add Color-------------
function add_color_query_y($conn,$vin,$color)
{
    $add_color_sql="INSERT INTO VehicleColor (vin,color) VALUES ('$vin', '$color');";

    return mysqli_query($conn, $add_color_sql);

}

//--------add Car-------------
function add_car_query_y($conn,$vin,$number_of_doors)
{
    $add_car_sql="INSERT INTO Car (vin,number_of_doors) VALUES ('$vin','$number_of_doors');";

    return mysqli_query($conn, $add_car_sql);

}

//--------add convertible-------------
function add_convertible_query_y($conn, $vin, $back_seat_count,$roof_type)
{
    $add_convertible_sql="INSERT INTO Convertible (vin,back_seat_count, roof_type) VALUES ('$vin','$back_seat_count', '$roof_type');";

    return mysqli_query($conn, $add_convertible_sql);

}

//--------add truck-------------
function add_truck_query_y($conn,$vin,$cargo_cover_type,$cargo_capacity,$number_of_rear_axies)
{
    $add_truck_sql="INSERT INTO Truck (vin,cargo_cover_type,cargo_capacity,number_of_rear_axies) VALUES ('$vin','$cargo_cover_type', '$cargo_capacity', '$number_of_rear_axies');";

    return mysqli_query($conn, $add_truck_sql);

}


//--------add van-------------
function add_van_query_y($conn,$vin,$has_drive_side_door)
{
    $add_van_sql="INSERT INTO Van (vin,has_drive_side_door) VALUES ('$vin','$has_drive_side_door');";

    return mysqli_query($conn, $add_van_sql);

}


//--------add suv-------------
function add_suv_query_y($conn,$vin,$drivetrain_type,$num_of_cupholders)
{
    $add_suv_sql="INSERT INTO Suv (vin,drivetrain_type, num_of_cupholders) VALUES ('$vin','$drivetrain_type','$num_of_cupholders');";

    return mysqli_query($conn, $add_suv_sql);

}

//--------search_customer_id-------------
function search_customer_y($conn,$vin)
{
    $search_customer_sql_y="SELECT customerID from Sale WHERE vin= '$vin'; ";

    return mysqli_query($conn, $search_customer_sql_y);

}

//--------search_business_id-------------
function search_business_y($conn,$vin)
{
    $search_business_sql_y="SELECT tax_id_number, business_name customerID FROM Business NATURAL JOIN Sale WHERE vin= '$vin'; ";

    return mysqli_query($conn, $search_business_sql_y);

}


//--------search_individual_id-------------
function search_individual_y($conn,$vin)
{
    $search_individual_sql_y="SELECT driver_license, first_name, last_name, customerID FROM Individual NATURAL JOIN Sale WHERE vin= '$vin'; ";

    return mysqli_query($conn, $search_individual_sql_y);

}


//--------search_business_customer_id-------------
function search_business_customer_y($conn,$customerID_search)
{
    $search_business_customer_sql_y="SELECT tax_id_number, business_name, customerID FROM Business WHERE customerID= '$customerID_search';";

    return mysqli_query($conn, $search_business_customer_sql_y);

}

//--------search_individual_customer_id-------------
function search_individual_customer_y($conn,$customerID_search)
{
    $search_individual_customer_sql_y="SELECT driver_license, first_name, last_name, customerID FROM Individual WHERE customerID= '$customerID_search'; ";

    return mysqli_query($conn, $search_individual_customer_sql_y);

}

//--------search_individual_customer_id-------------
function search_labor_charge_y($conn,$vin,$start_date)
{
    $search_labor_charge_sql_y="SELECT labor_charge FROM Repair WHERE vin= '$vin' AND start_date='$start_date'; ";

    return mysqli_query($conn, $search_labor_charge_sql_y);

}







