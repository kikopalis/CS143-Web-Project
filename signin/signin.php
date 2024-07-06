<?php
session_start();
if (isset($_SESSION['signedin'])) {
  header('location: ../index/index.php');
}
?>

<!doctype html>
<html lang="en">


<head>

  <title>Web Project</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="signin.css">

</head>


<body>

  <div class="d-flex justify-content-center">
    <div id="welcome-con" class="justify-content-center">
      <div>
        <h1 class="display-3">Welcome!</h1>
      </div>

      <div>
        <!-- Sign in Button trigger modal -->
        <button type="button" class="btn btn-muted btn-lg modal-trigger" data-toggle="modal" data-target="#loginform">
          Sign in
        </button>
      </div>
      <!-- Quotation text -->
      <blockquote class="blockquote text-center">
        <p class="mb-0">Stay healthy by eat more fruits and veggies</p>
        <footer class="blockquote-footer">JFF</footer>
      </blockquote>
    </div>
  </div>

  <!-- Modal for login -->
  <form class="modal fade" id="loginform" name="loginform" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">

          <div class="form-group">
            <div class="d-flex justify-content-center"><img class="logo" src="../images/logo.png" width="70"></div>
          </div>

          <div class="form-group">
            <label for="username">Username</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <i class="input-group-text fa fa-user" aria-hidden="true"></i>
              </div>
              <input type="text" class="form-control" name="username" id="username" aria-describedby="helpId" autofocus required>
            </div>
            <small id="helpId-user" class="form-text text-muted">Input your username</small>
          </div>

          <div class="form-group">
            <label for="userpassword">Password</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <i class="input-group-text fa fa-lock" aria-hidden="true"></i>
              </div>
              <input type="password" class="form-control" name="userpassword" id="userpassword" aria-describedby="helpId" required>
            </div>
            <small id="helpId-pass" class="form-text text-muted">Input your password</small>
          </div>

          <div>
            <button class="btn btn-primary rounded w-100 mb-4" type="submit" id="submit" name="submit">Sign in</button>
          </div>

        </div>
      </div>
    </div>
  </form>

</body>


<!-- JavaScript Bundle with Popper -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="signin.js"></script>

</html>