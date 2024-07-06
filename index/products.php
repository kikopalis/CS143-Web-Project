<?php
    session_start();
    if (!isset($_POST['remove']) && !isset($_POST['product-search1']) && !isset($_POST['product-search2']) && !isset($_POST['pid']) && !isset($_POST['name']) && !isset($_POST['price']) && !isset($_POST['qty']) && !isset($_FILES['imagefile']['size'])){
        header('location: index.php');
    }
    else{
        $con = mysqli_connect('localhost', 'root', '', 'web_project');
        if (!$con) 
            exit(mysqli_connect_errno());
            
        if (isset($_POST['product-search1']) || isset($_POST['product-search2']) || isset($_POST['edit'])) {
            return_search($con);
        }
        else if (isset($_POST['remove'])){
            if (remove_product($con))
                echo '<span class="text-success">Successfully removed!</span>';
            else
                echo '<span class="text-danger">Product ID already exists in invoice!</span>';
        }
        else {
            if ($_FILES['imagefile']['error'] == 0){
                $uploadto = '../images/';
                $uploaded_filename = $_FILES['imagefile']['name'];
                $uploaded_file = $_FILES['imagefile']['tmp_name'];
                $uploaded_filesize = $_FILES['imagefile']['size'];
                $extension = (pathinfo($uploaded_filename))['extension'];
                $new_filename = $_POST['pid'].'.'.$extension;
        
                if (!check_file_uploaded_name($uploaded_filename))
                    echo '<span class="text-danger">Image name should be composed of [a-z, A-Z, 0-9, -_/.] only!</span>';
                else if (!check_file_uploaded_length($uploaded_filename))
                    echo '<span class="text-danger">Image name is too long!</span>';
                else if (!check_file_uploaded_size( $uploaded_filesize))
                    echo '<span class="text-danger">Image size too large!</span>';
                else {
                    if(isset($_POST['add'])) {
                        if (insert_product($uploadto.$new_filename, $con)) {
                            move_uploaded_file($uploaded_file, $uploadto.$new_filename);
                            echo '<span class="text-success">Successfully added!</span>';
                        }
                        else
                            echo '<span class="text-danger">Product ID already exist!</span>';
                    }
                    else {
                        if (update_product($uploadto.$new_filename, $con)) {
                            move_uploaded_file($uploaded_file, $uploadto.$new_filename);
                            echo '<span class="text-success">Successfully updated!</span>';
                        }
                        else{
                            if ($_POST['pid'] == $_SESSION['oldpid'])
                                echo '<span class="text-danger">You haven\'t change anything!</span>';
                            else
                                echo '<span class="text-danger">Product ID already exists in products/invoice!</span>';
                        }
                    }
                }
            }
            else{
                if (update_product_same_image($con))
                    echo '<span class="text-success">Successfully updated!</span>';
                else{
                    if ($_POST['pid'] == $_SESSION['oldpid'])
                        echo '<span class="text-danger">You haven\'t change anything!</span>';
                    else
                        echo '<span class="text-danger">Product ID already exists in products/invoice!</span>';
                }
            }
        }
        $con->close();
    }
    
    function return_search($con) {
        if (isset($_POST['product-search1-submit'])) {
            $stmt = $con->prepare('SELECT * FROM products WHERE (pid LIKE ? OR name LIKE ?) AND qty > 0');
            $toSearch = '%'.$_POST['product-search1'].'%';
        }
        else if (isset($_POST['product-search2-submit'])) {
            $stmt = $con->prepare('SELECT * FROM products WHERE (pid LIKE ? OR name LIKE ?)');
            $toSearch = '%'.$_POST['product-search2'].'%';
        }
        else {
            $stmt = $con->prepare('SELECT * FROM products WHERE pid = ?');
            $toSearch = $_POST['pid'];
        }
        if ($stmt) {
            if (isset($_POST['edit'])) {
                $stmt->bind_param('s', $toSearch);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $_SESSION['oldpid'] = $_POST['pid'];
                echo json_encode(array("pid" =>  $row['pid'],
                                        "name" => $row['name'],
                                        "price" => $row['price'],
                                        "qty" => $row['qty'],
                                        "img_path" => $row['img_path']));
            }
            else {
                $stmt->bind_param('ss', $toSearch, $toSearch);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $rqty = '<td>'.$row['qty'].'</td>';
                    if ($row['qty'] == 0)
                        $rqty = '<td class="text-danger">Out of Stock</td>';
                    if ($row['qty'] >= 1 && $row['qty'] < 5)
                        $rqty = '<td class="text-warning">'.$row['qty'].'</td>';
                    echo '<tr>',
                            '<td><img class="rounded" src="'.$row['img_path'].'" width="55px"></td>',
                            '<td>'.$row['pid'].'</td>',
                            '<td>'.$row['name'].'</td>',
                            '<td>'.$row['price'].'</td>',
                            $rqty,
                        '</tr>';
                }
            }
        }
    }

    function remove_product($con) {
        $stmt = $con->prepare('SELECT img_path from products WHERE pid = ?');
        $stmt->bind_param('s', $_SESSION['oldpid']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $img_path = $row['img_path'];
        $stmt = $con->prepare('DELETE FROM products WHERE pid = ?');
        $stmt->bind_param('s', $_SESSION['oldpid']);
        if ($stmt->execute()){
            unlink(''.$img_path.'');
            to_log($con, 'Delete product, PID: '.$_SESSION['oldpid']);
            return true;
        }
        return false;
    }

    function update_product($newpath, $con) {
        if ($_POST['qty'] <= 0)
            $_POST['qty'] = 0;
        if ($_POST['price'] <= 0)
            $_POST['price'] = 0;

        if ($_POST['pid'] != $_SESSION['oldpid']) {
            $query = $con->prepare('UPDATE products SET img_path = ?, pid = ?, name = ?, price = ?, qty = ? WHERE pid = ?');
            $query->bind_param('ssssss', $newpath, $_POST['pid'], $_POST['name'], $_POST['price'], $_POST['qty'], $_SESSION['oldpid']);
            $query->execute();
            if ($query->affected_rows > 0) {
                to_log($con, 'Update product, PID: '.$_POST['pid']);
                return true;
            }
        }
        else{
            $query = $con->prepare('UPDATE products SET img_path = "temp" WHERE pid = '.$_SESSION['oldpid'].'');
            $query->execute();
            $query = $con->prepare('UPDATE products SET img_path = ?, name = ?, price = ?, qty = ? WHERE pid = ?');
            $query->bind_param('sssss', $newpath, $_POST['name'], $_POST['price'], $_POST['qty'], $_SESSION['oldpid']);
            $query->execute();
            if ($query->affected_rows > 0) {
                to_log($con, 'Update product, PID: '.$_SESSION['oldpid']);
                return true;
            }
        }
        
        return false;
    }

    function update_product_same_image($con) {
        if ($_POST['qty'] <= 0)
            $_POST['qty'] = 0;
        if ($_POST['price'] <= 0)
            $_POST['price'] = 0;

        if ($_POST['pid'] != $_SESSION['oldpid']) {
            $query = $con->prepare('UPDATE products SET pid = ?, name = ?, price = ?, qty = ? WHERE pid = ?');
            $query->bind_param('sssss', $_POST['pid'], $_POST['name'], $_POST['price'], $_POST['qty'], $_SESSION['oldpid']);
            $query->execute();
            if ($query->affected_rows > 0) {
                to_log($con, 'Update product, PID: '.$_POST['pid']);
                return true;
            }
        }
        else {
            $query = $con->prepare('UPDATE products SET name = ?, price = ?, qty = ? WHERE pid = ?');
            $query->bind_param('ssss', $_POST['name'], $_POST['price'], $_POST['qty'], $_SESSION['oldpid']);
            $query->execute();
            if ($query->affected_rows > 0) {
                to_log($con, 'Update product, PID: '.$_SESSION['oldpid']);
                return true;
            }
        }
        
        return false;
    }

    function insert_product($newpath, $con) {
        $query = $con->prepare('INSERT INTO products (img_path, pid, name, price, qty) VALUES (?, ?, ?, ?, ?)');
        $query->bind_param('sssss', $newpath, $_POST['pid'], $_POST['name'], $_POST['price'], $_POST['qty']);
        if ($query->execute()) {
            to_log($con, 'Add product, PID: '.$_POST['pid']);
            return true;
        }
        return false;
    }

    function check_file_uploaded_name ($filename){
        return ((preg_match('`^[-0-9A-Za-z_\.]+$`i',$filename)) ? true : false);
    }
    function check_file_uploaded_length ($filename){
        return ((mb_strlen($filename,'UTF-8') < 225) ? true : false);
    }
    function check_file_uploaded_size ($size){
        return (($size < 2097152) ? true : false);
    }

    function to_log($con, $info) {
        $con->query('INSERT INTO logs (info, account) VALUES ("'.$info.'", "'.$_SESSION['account'].'")');
    }
?>