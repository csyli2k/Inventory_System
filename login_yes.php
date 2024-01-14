<?php
include 'lib/common.php';

if (!isset($_SESSION['logged'])) {
    echo "Incorrect redirect - you didn't log in. Back to the log in page in seconds";
    header("Refresh: 3; login.php");
    exit();
}
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jaunty Jalopies Log In</title>
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
                </ul>
            </div>
        </nav>
    </section>

    <section class="login_section">
        <div class="login_outerbox_display">
            <div class="login_innerbox_display">
                <div class = "login_yes_display">
                <p>Successfully logged in</p> <br>
                <p>Auto-redirect to the home page in seconds </p> <br>
                <p>You can also click the home button to go back </p> <br>
                </div>
            </div>
        </div>
    </section>

<?php
header("Refresh: 5; Index.php");
?>

</body>
</html>
