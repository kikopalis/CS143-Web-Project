<?php
    session_start();
    if (!isset($_GET['username']) || !isset($_GET['userpassword']) || isset($_SESSION['signedin'])){
        header('location: signin.php');
    }
    else{
        $con = mysqli_connect("localhost", "root", "", "web_project");
        if (!$con)
            exit(mysqli_connect_error());
        else{
            $username = $_GET['username'];
            $userpassword = $_GET['userpassword'];
            if ($stmt = $con->prepare('SELECT uid, upass FROM user_account WHERE uname = ?')){
                $stmt->bind_param('s',  $username);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows() > 0){
                    $stmt->bind_result($uid, $upass);
                    $stmt->fetch();
                    if($upass == $userpassword){
                        session_regenerate_id();
                        $_SESSION['signedin'] = true;
                        $_SESSION['uid'] = $uid;
                        $_SESSION['account'] = $username;
                        $con->query('INSERT into logs (info, account) values ("Logged in", "'.$username.'")');
                        echo '../index/index.php';
                    }
                    else{
                        echo 'Incorrect password';
                    }
                }
                else{
                    echo 'Incorrect username';
                }
            }
            $con->close();
        }
    }
?>