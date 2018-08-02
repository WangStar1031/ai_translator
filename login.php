<?php
$email_name = "";
$password = "";
$userVeri = true;
require_once 'library/db_user_man.php';
if( isset($_POST['email_name'])){
  $email_name = $_POST['email_name'];
  if( isset($_POST['password'])){
    $password = $_POST['password'];
  }
  $userVeri = userVerification($email_name, $password);
  if( $userVeri != false){
    session_start();
    $_SESSION['jtsUserName'] = $userVeri;
    header("Location: index.php");
  }
}
?>

<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>JRA Translation Login</title>

  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/auth.css">
  <link rel="icon" type="image/png" href="assets/imgs/vision-logo.png">
</head>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

<body>
<main class="auth-main">
  <div class="auth-block">
    <h3>Sign in to <img src="assets/imgs/vision-logo.png" width="50px;"> JTS</h3>
    <form class="form-horizontal" method="POST">
      <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Email/Name</label>

        <div class="col-sm-12">
          <input type="text" class="form-control" id="inputEmail3" placeholder="Email or Name" name="email_name">
        </div>
      </div>
      <div class="form-group">
        <label for="inputPassword3" class="col-sm-2 control-label">Password</label>

        <div class="col-sm-12">
          <input type="password" class="form-control" id="inputPassword3" placeholder="Password" name="password">
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button class="btn btn-default btn-auth">Sign in</button>
        </div>
      </div>
    </form>
    </div>
  </div>
</main>
</body>
</html>
