<?php

session_start();
include_once "php/connect.php";
if (!($_SESSION['loggedIn'])) {
  header("Location: /");
} else {
  $email = $_SESSION['email'];



  $_SESSION['loggedIn'] = false;
  $_SESSION['email'] = null;
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Involve | Home</title>

    <link rel="stylesheet" href="css/home.css">
  </head>
  <body>

    <!-- Navbar -->
    <?php include_once "includes/navbar.php"; ?>

    <div class="page">

      <!-- Page content -->
      <div class="content">
        content
      </div>

      <!-- sidebar on right -->
      <?php include_once "includes/sidebar.php"; ?>

    </div>

  </body>
</html>
