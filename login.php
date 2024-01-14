<?php
include 'lib/common.php';
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jaunty Jalopies Log In</title>
    <link rel="stylesheet" href="css/style.css ">
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
                <!-- LOG IN FORM -->
                <form class="login" action="includes/login_include.php" method = "post">
                    <div class="login_text"><input type="text" class="login_input" name="username" placeholder="Username"></div>
                    <div class="login_text"><input type="password" class="login_input" name="password" placeholder="Password"></div>
                    <button class="login_submit" type = "submit" name="login_submit"><span class="button_text">Log In</span></button> <!-- https://www.w3schools.com/tags/tag_button.asp-->
                </form>

                <!-- LOG IN RESULT CHECK -->
                <div class="login_result_check">
                <?php
                $fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI] <br>";
                if (strpos($fullUrl, "signin=empty") == true) {
                    echo "<p>You did not fill in all fields!</p>";
                    echo "<p>Please try again!</p>";
                    exit();
                } elseif (strpos($fullUrl, "signin=invalid") == true) {
                    echo "<P>You did not fill in correct password!</p>";
                    echo "<p>Please try again!</p>";
                    exit();
                } elseif(strpos($fullUrl, "signin=notexist") == true){
                    echo "<p>Username doesn't exist!</p>";
                    echo "<p>Please try again!</p>";
                    exit();
                } elseif(strpos($fullUrl, "signin=success") == true){
                    header("Location: login_yes.php");
                }else{}
                ?>
                <div>
            </div>
        </div>
    </section>
</body>
</html>
