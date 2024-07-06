<?php
    session_start();
    if (!isset($_POST['get_invoice']) && !isset($_POST['cash']) && !isset($_POST['invoice_submit'])) {
        header('location: index.php');
    }
    else {
        $con = mysqli_connect('localhost', 'root', '', 'web_project');
        if (!$con)
            exit(mysqli_connect_errno());
        else {
            $invoice_id = get_current_voice_id($con);
            
            $stmt = $con->prepare('SELECT SUM(subtotal) FROM cart WHERE invoice_id = '.$invoice_id.'');
            if ($stmt) {
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($total);
                $stmt->fetch();

                if(isset($_POST['get_invoice'])) {
                    return_current_invoice($total, $invoice_id, $con);
                }
                else{
                    if ($total != NULL) {
                        $change = doubleval($_POST['cash']) - $total;
                        if (isset($_POST['cash']) && $change >= 0){
                                if(update_current_invoice($total, $invoice_id, $con, $change)){
                                    echo '<span class="text-success">Success!</span>';
                                    $con->query('INSERT INTO logs (info, account) VALUES ("Sold, Invoice ID: '.$invoice_id.'", "'.$_SESSION['account'].'")');
                                }
                        }
                        else
                            echo '<span class="text-danger">Insufficient cash!</span>';
                    }
                    else
                         echo '<span class="text-danger">Cart empty!</span>';
                }
            }
            else
                echo $con->error;
            $con->close();
        }
    }

    function get_current_voice_id($con) {
        $stmt = $con->prepare('SHOW TABLE STATUS LIKE "invoice"');
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['Auto_increment']-1;
    }

    function update_current_invoice($total, $invoice_id, $con, $change) {
        $date = date('Y-m-d'); 
        $stmt = $con->prepare('UPDATE invoice SET total = '.$total.', date = "'.$date.'", change_ = '.$change.' WHERE invoice_id = '.$invoice_id.'');
        if ($stmt) {
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $stmt = $con->prepare('INSERT INTO invoice (total, date, change_) VALUES (NULL, NULL, NULL)');
                $stmt->execute();
                return true;
            }
        }
        return false;
    }

    function return_current_invoice($total) {
        if (isset($_POST['cash']) && $total != NULL) {
            $change = floatval($_POST['cash']) - $total;
            if ($change >= 0) {
                echo '<span>Total: '.$total.'</span>
                        <span class="float-right">Change: '.$change.'</span>';
            }
            else{
                echo '<span>Total: '.$total.'</span>
                        <span class="float-right">Change: '.$change.'</span>';
            }
        }
    }
?>  