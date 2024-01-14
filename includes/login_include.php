<?php
include '../lib/common.php';

if (isset($_POST['login_submit'])) {
    include_once '../lib/queryfunction.php';
    $conn = mysql_conn_init_6400();

    echo "Entered username and password" . "<br>";
    echo $_POST['username'] . "<br>";
    echo $_POST['password'] . "<br>";

    // mysqli_real_escape_string to protect the scenario user try to inject code
    $entered_username = mysqli_real_escape_string($conn, $_POST['username']);
    $entered_password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check the empty input case
    if (empty($entered_username) || empty($entered_password)) {
        header("Location: ../login.php?signin=empty");
    } else {
        // Check the non-empty case
        // call sql function for log in
        $result = get_login_query($conn, $entered_username);
        $result_check = mysqli_num_rows($result);

        // When there is a result regarding logged in username
        if ($result_check > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                print_r($row);
                $result_username = $row['username'];
                $result_password = $row['password'];
            }

            // $storedHash = password_hash($result_password, PASSWORD_DEFAULT);
            // $enteredHash = password_hash($entered_password, PASSWORD_DEFAULT);

            if ($entered_password == $result_password) {
                $array_usertype = get_UserType($conn, $result_username);
                $str_usertype = implode($array_usertype);
                echo implode($array_usertype);
                // Set up session user array
                $_SESSION["logged"] = array();
                $_SESSION["logged"]["username"] = $result_username;
                $_SESSION["logged"]["usertype"] = $array_usertype;
                echo sizeof($_SESSION["logged"]["usertype"]);
                print_r($_SESSION["logged"]);
                header("Location: ../login.php?signin=success");
            } else {
                header("Location: ../login.php?signin=invalid");
            }

        } else {
            // When there is no result regarding logged in username
            header("Location: ../login.php?signin=notexist");
        }
    }
}
