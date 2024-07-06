<?php
    session_start();
    if (!isset($_GET['get_invoice_list']) && !isset($_GET['get_invoice_content'])){
        header("location: index.php");
    }
    else {
        $con = mysqli_connect("localhost", "root", "", "web_project");
        if (!$con)
            exit(mysqli_connect_error());
        else{
            if (isset($_GET['get_invoice_list'])) {
                $stmt = $con->prepare('SELECT * FROM invoice WHERE date IS NOT NULL ORDER BY date DESC');
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>',
                        '<td>'.$row['date'].'</td>',
                        '<td>'.$row['invoice_id'].'</td>',
                        '<td>'.$row['total'].'</td>',
                        '<td>'.$row['change_'].'</td>',
                    '</tr>';
                }
            }
            if (isset($_GET['get_invoice_content'])) {
                $stmt = $con->prepare('SELECT p.img_path, p.pid, p.name, p.qty as pqty, c.qty as cqty, p.price, c.subtotal FROM cart c, products p WHERE c.invoice_id = ? AND c.pid=p.pid');
                $stmt->bind_param('s', $_GET['get_invoice_content']);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>',
                            '<td><img class="rounded" src="'.$row['img_path'].'" width="55px"></td>',
                            '<td>'.$row['name'].'</td>',
                            '<td>'.$row['cqty'].'</td>',
                            '<td>'.$row['price'].'</td>',
                            '<td>'.$row['subtotal'].'</td>',
                        '</tr>';
                }
            }
            $con->close();
        }
    }
?>