<?php
    session_start();
    if (isset($_POST['signout'])) {
        $con = mysqli_connect("localhost", "root", "", "web_project");
        if (!$con)
            exit(mysqli_connect_error());
        else{
            if ($_SESSION['uid'] == 1)
                $username = 'admin';
            else if ($_SESSION['uid'] == 2)
                $username = 'manager';
            else
                $username = 'cashier';
            $con->query('INSERT into logs (info, account) values ("Logged out", "'.$username.'")');
        }
        session_destroy();
        header("location: ../signin/signin.php");
    }
    else
        header("location: index.php");
?>