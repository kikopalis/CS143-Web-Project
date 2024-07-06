<?php
    ini_set('session.gc_maxlifetime', 60);
    session_set_cookie_params(60);
    session_start();
    if (!isset($_SESSION['signedin'])){
        header("location: ../signin/signin.php");
    }
    //error_reporting(E_ERROR | E_WARNING | E_PARSE);
?>

<!doctype html>
<html lang="en">


<head>

    <title>Web Project</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="index.css">

</head>


<body>

    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-md navbar-dark">

        <a class="navbar-brand d-none d-md-block">
            <img class="logo" src="../images/logo.png" width="70">
        </a>

        <a class="navbar-brand d-md-none" href="#" data-toggle="collapse" data-target="#collapsibleNavId"
            aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation">
            <img class="logo" src="../images/logo.png" width="70">
        </a>

        <!-- Navigation bar items -->
        <div class="collapse navbar-collapse" id="collapsibleNavId">

            <ul class="navbar-nav mr-auto">

                <li class="nav-item active">
                    <a class="nav-link" href="#" data-toggle="collapse" data-target=".navbar-collapse.show">POS <span
                            class="sr-only">(current)</span></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="collapse" data-target=".navbar-collapse.show">Accounts</a>
                </li>

                <li class="nav-item">
                    <span id="products-notification1" class="badge badge-pill badge-warning"
                        style="float:right;margin-bottom:-8px;"></span> <!-- your badge -->
                    <span id="products-notification2" class="badge badge-pill badge-danger"
                        style="float:right;margin-bottom:-8px;"></span> <!-- your badge -->
                    <a class="nav-link" href="#" data-toggle="collapse" data-target=".navbar-collapse.show">Products</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="collapse" data-target=".navbar-collapse.show">Logs</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="collapse" data-target=".navbar-collapse.show">Reports</a>
                </li>

            </ul>

            <form class="form-inline signout" action="signout.php" method="post">
                <button class="btn btn-outline-danger text-white" type="submit" name="signout">Sign out</button>
            </form>

        </div>

    </nav>

    <!-- POS content container -->
    <div class="nav-item-content justify-content-center d-flex">
        <!-- product list container -->
        <div class="product-list float-left card mr-4">
            <div class="card-header">
                Products
                <form id="form-search1" action="products.php" method="post" class="float-right">
                    <input id="product-search1" name="product-search1" class="form-control" type="text"
                        placeholder="Search" aria-label="Search">
                </form>
            </div>
            <div class="card-body overflow-auto">
                <table class="table table-hover text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>Image</th>
                            <th>PID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody id="product-list1">
                        <!-- product list inserted here -->
                    </tbody>
                </table>
            </div>
        </div>
        <!-- INVOICE container -->
        <div id="invoice" class="invoice float-left card flex-shrink-0">
            <div class="card-header">
                Invoice
                <div id="invoice-show-error" class="text-center"></div>
            </div>
            <div class="card-body overflow-auto">
                <table class="table table-hover text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="invoice-list">
                        <!-- product to invoice goes here -->
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div id="invoice-info">
                </div>
                <div class="input-group mt-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">₱</span>
                    </div>
                    <div class="w-25 mr-auto">
                        <input type="number" id="cash" class="form-control rounded mr-5" min="0" step=".01" required>
                    </div>
                    <button id="invoice-submit" name="invoice-submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounts content container -->
    <div class="nav-item-content d-none justify-content-center">
        <div>
            Accounts
        </div>
    </div>

    <!-- Product content container -->
    <div class="nav-item-content d-none justify-content-center">
        <!-- product list container -->
        <div class="product-list card">
            <div class="card-header">
                Manage products
                <form id="form-search2" class="float-right">
                    <input id="product-search2" name="product-search2" class="form-control" type="text"
                        placeholder="Search" aria-label="Search">
                </form>
            </div>
            <div class="card-body overflow-auto">
                <table class="table table-hover text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>Image</th>
                            <th>PID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody id="product-list2">
                        <!-- product list inserted here -->

                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button type="button" id="add-button" class="btn btn-primary float-right mr-3" data-toggle="modal"
                    data-target="#add-form">+ Add</button>
            </div>

            <!-- Modal for Adding product -->
            <form enctype="multipart/form-data" class="modal fade" id="add-form" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="add-form-title">Add Product</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="show-error" class="mb-3 text-center">
                                <!--error goes here -->
                            </div>
                            <div class="form-group">
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">ID</span>
                                    </div>
                                    <input type="text" class="form-control" name="pid" id="pid" required>
                                </div>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Name</span>
                                    </div>
                                    <input type="text" class="form-control" name="name" id="name" required>
                                </div>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₱</span>
                                    </div>
                                    <input type="number" class="form-control" name="price" min="0" id="price" step="any"
                                        required>
                                </div>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Qty</span>
                                    </div>
                                    <input type="number" class="form-control" min="0" name="qty" id="qty" required>
                                </div>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Image</span>
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="imagefile" name="imagefile"
                                            accept=".jpeg,.jpg,.gif,.png" required>
                                        <label class="custom-file-label overflow-hidden" for="product-image">Choose a
                                            file (2MB limit)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="add-form-delete" class="btn btn-danger d-none">Delete</button>
                            <button type="reset" id="add-form-clear" class="btn btn-secondary mr-auto">Clear</button>
                            <button type="submit" id="add-form-submit" name="add-form-submit"
                                class="btn btn-primary">Confirm</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs content container -->
    <div class="nav-item-content d-none justify-content-center">
        <div class="product-list card mr-auto">
            <div class="card-header">
                Log in & out's
            </div>
            <div class="card-body overflow-auto">
                <table class="table table-hover text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>Date & Time</th>
                            <th>Info</th>
                            <th>Account</th>
                        </tr>
                    </thead>
                    <tbody id="logs-container">
                        <!-- log list inserted here -->


                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button type="button" id="clear-logs" class="btn btn-primary float-right mr-3">Clear</button>
            </div>
        </div>
        <div class="product-list card">
            <div class="card-header">
                Activities
            </div>
            <div class="card-body overflow-auto">
                <table class="table table-hover text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>Date & Time</th>
                            <th>Info</th>
                            <th>Account</th>
                        </tr>
                    </thead>
                    <tbody id="logs-container2">
                        <!-- log list2 inserted here -->


                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button type="button" id="clear-logs2" class="btn btn-primary float-right mr-3">Clear</button>
            </div>
        </div>
    </div>

    <!-- Reports content container -->
    <div class="nav-item-content d-none justify-content-center">
        <div class="product-list card">
            <div class="card-header">
                Sales report
            </div>
            <div class="card-body overflow-auto">
                <table class="table table-hover text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>Date</th>
                            <th>Invoice ID</th>
                            <th>Total</th>
                            <th>Change</th>
                        </tr>
                    </thead>
                    <tbody id="reports-container">
                        <!-- invoice list inserted here -->


                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal for reports -->
    <div class="modal fade overflow-auto" id="invoice_content" tabindex="-1" role="dialog"
        aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoice_content_id"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="invoice_content_list">
                            <!-- invoice content goes here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <div id="invoice_content_info" class="w-100">
                        <span>Total: </span><br>
                        <span>Money: </span>
                        <span class="float-right">Change: </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
    integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
</script>
<script src="index.js"></script>

</html>

<?php 
    echo '<script>hideNavBarItem("'.$_SESSION['uid'].'")</script>';
?>