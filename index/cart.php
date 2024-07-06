<?php
session_start();
if (!isset($_GET['pid']) && !isset($_GET['getcart']))
    header('location: index.php');
else {
    $con = mysqli_connect('localhost', 'root', '', 'web_project');
    if (!$con)
        exit(mysqli_connect_error());

    $invoice_id = get_current_invoice_id($con);
    if (isset($_GET['getcart']))
        return_cart($invoice_id, $con);
    else {
        $pid = $_GET['pid'];
        $tocart = $_GET['tocart'];
        if (in_cart($pid, $invoice_id, $con)) {
            if (update_cart($pid, $invoice_id, $con, $tocart))
                return_cart($invoice_id, $con);
        } else {
            if (add_to_cart($invoice_id, $pid, $con))
                return_cart($invoice_id, $con);
        }
    }
    $con->close();
}

function in_cart($pid, $invoice_id, $con)
{
    $stmt = $con->prepare('SELECT * from cart WHERE pid = ? AND invoice_id = ?');
    $stmt->bind_param('si', $pid, $invoice_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows() > 0)
        return true;

    return false;
}

function return_cart($invoice_id, $con)
{
    if ($stmt = $con->prepare('SELECT p.img_path, p.pid, p.name, p.qty as pqty, c.qty as cqty, p.price, c.subtotal FROM cart c, products p WHERE c.invoice_id = ? AND c.pid=p.pid')) {
        $stmt->bind_param('i', $invoice_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $remaining_stocks = $row['pqty'] + $row['cqty'];
            echo '<tr>',
            '<td class="d-none">' . $row['pid'] . '</td>',
            '<td><img class="rounded" src="' . $row['img_path'] . '" width="55px"></td>',
            '<td>' . $row['name'] . '</td>',
            '<td><input type="number" class="form-control text-center" min="1" max="' . $remaining_stocks . '" value="' . $row['cqty'] . '"></td>',
            '<td>' . $row['price'] . '</td>',
            '<td>' . $row['subtotal'] . '</td>',
            '<td><a href=""><img src="../images/remove.png" width="25px"></a></td>',
            '</tr>';
        }
    }
}

function update_cart($pid, $invoice_id, $con, $tocart)
{
    if ($tocart == '++') {
        if ($stmt = $con->prepare('UPDATE cart c INNER JOIN products p ON (c.pid = p.pid) SET p.qty = p.qty - 1, c.qty = c.qty + 1,  c.subtotal = c.subtotal + p.price WHERE p.pid = ? AND p.qty > 0 AND c.invoice_id = ?')) {
            $stmt->bind_param('ss', $pid, $invoice_id);
            $stmt->execute();
        }
    } else {
        if ($tocart <= 0)
            $tocart = 0;
        if ($stmt = $con->prepare('UPDATE cart c INNER JOIN products p ON (c.pid = p.pid) SET p.qty = p.qty + c.qty - ?, c.qty = ?,  c.subtotal = ? * p.price WHERE p.pid = ? AND p.qty >= 0 AND c.invoice_id = ?')) {
            $stmt->bind_param('sssss', $tocart, $tocart, $tocart, $pid, $invoice_id);
            $stmt->execute();
        }
    }
    if ($stmt->affected_rows > 0) {
        if ($tocart == '0')
            remove_from_cart($pid, $invoice_id, $con);
        return true;
    } else
        return false;
}

function get_current_invoice_id($con)
{
    $stmt = $con->prepare('SHOW TABLE STATUS LIKE "invoice"');
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['Auto_increment'] - 1;
}

function add_to_cart($invoice_id, $pid, $con)
{
    if ($stmt = $con->prepare('INSERT into cart (invoice_id, pid, qty, subtotal) SELECT ?, pid, 1, price FROM products WHERE pid = ?')) {
        $stmt->bind_param('is', $invoice_id, $pid);
        $stmt->execute();
    }
    if ($stmt->affected_rows > 0) {
        if ($stmt = $con->prepare('UPDATE products SET qty = qty - 1 WHERE pid = ' . $pid . ''))
            $stmt->execute();
        return true;
    }
    return false;
}

function remove_from_cart($pid, $invoice_id,  $con)
{
    $stmt = $con->prepare('DELETE FROM cart WHERE pid = ' . $pid . ' AND invoice_id = ' . $invoice_id . '');
    $stmt->execute();
}
