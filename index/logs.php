<?php
    session_start();
    if (!isset($_GET['get']) && !isset($_POST['clear'])){
        header("location: index.php");
    }
    else {
        $con = mysqli_connect("localhost", "root", "", "web_project");
        if (!$con)
            exit(mysqli_connect_error());
        else{
            if (isset($_GET['get'])) {
                get_logs($con);
            }
            if (isset($_POST['clear'])) {
                if($_POST['clear'] != "activity")
                    $con->query('DELETE FROM logs WHERE info LIKE "Logged%"');
                else 
                    $con->query('DELETE FROM logs WHERE info NOT LIKE "Logged%"');
            }
        }
    }
    $con->close();

    function get_logs($con) {
        if ($_GET['get'] != 'activity')
            $stmt = $con->prepare('SELECT * FROM logs WHERE info LIKE "Logged%" order by date_time DESC LIMIT 20');
        else
            $stmt = $con->prepare('SELECT * FROM logs WHERE info NOT LIKE "Logged%" order by date_time DESC LIMIT 20');
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            echo '<tr>',
                '<td>'.$row['date_time'].'</td>',
                '<td>'.$row['info'].'</td>',
                '<td>'.$row['account'].'</td>',
            '</tr>';
        }
    }
?>