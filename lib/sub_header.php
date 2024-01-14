<section class = "sub_header">
    <nav>
        <a href="../Index.html"><img src="../images/logo2.png"></a>
        <div class = "nav_link">
            <ul>
                <li><a href="../Index.php"> HOME </a></li>
                <li><a href="../report_index.php"> Report Index </a></li>
                <li><a> USER - <?php echo strtoupper($_SESSION['logged']["username"]); ?> </a></li>
                <li><a> ROLE - <?php echo strtoupper(implode(" ,", $_SESSION["logged"]["usertype"])); ?> </a></li>
                <li><a href="../logout.php"> LOGOUT </a></li>
            </ul>
        </div>
    </nav>
</section>