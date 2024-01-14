<div class="div_align_center">
<h1>Please enter sales details</h1> 
<form action ='add_sale_salespeople.php' method='GET'>
        
        <label for="customer_id">Customer ID:</label>
        <input type="hidden" name='customer_id'  value="<?php
        if (isset($_SESSION['customer_identifier'])) {
                echo $_SESSION['customer_identifier'];
        }
         ?>">
        <?php
                if (isset($_SESSION['customer_identifier'])) {
                        echo "current customer_id: ".$_SESSION['customer_identifier'];
                        echo " <a href='../customer.php'>Or Change a customer</a>";
                } else {
                        echo "<a href='../customer.php'>No customer selected. Find a customer first</a>";
                }
        ?>
        <br>
        Purchase Date: <input type ='date' name='purchase_date' value="<?php echo date('Y-m-d');?>">
        <br>
        Vin: <input type ='text' name='vin'>
        <br>
        Sold Price: <input type ='number'  name='sold_price'>
        <br>
        <br>
        <button type='submit' <?php if (!isset($_SESSION['customer_identifier'])) {
                echo "disabled";
        } ?>>Add</button><br>
     </form>
</div>